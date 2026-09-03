<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuestChatThread;
use App\Models\GuestChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;

class GuestChatApiController extends Controller
{
    /**
     * Helper terpusat untuk mengekstrak raw session token dari Cookie, Header, atau Input payload (DRY Principle).
     */
    private function extractToken(Request $request): ?string
    {
        return $request->cookie('guest_chat_token') 
            ?? $request->header('X-Guest-Chat-Token') 
            ?? $request->input('session_token');
    }

    /**
     * Helper to find a thread by cookie, header, or input token.
     */
    private function getThread(Request $request, bool $lock = false): ?GuestChatThread
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return null;
        }

        // Hash token sebelum dicocokkan ke database
        $hashedToken = hash('sha256', $token);

        $query = GuestChatThread::where('session_token', $hashedToken);
        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Start a new chat thread for a guest.
     */
    public function startThread(Request $request): JsonResponse
    {
        if ($request->has('no_hp') && is_string($request->input('no_hp'))) {
            $request->merge([
                'no_hp' => preg_replace('/[^0-9+]/', '', $request->input('no_hp')),
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{8,13}$/'],
        ], [
            'name.required' => 'Nama Lengkap wajib diisi.',
            'no_hp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_hp.regex' => 'Format nomor WhatsApp tidak valid (gunakan format Indonesia seperti 0812xxx atau 62812xxx).',
        ]);

        // Generate token and create thread
        $token = Str::uuid()->toString();
        $hashedToken = hash('sha256', $token);
        
        $thread = GuestChatThread::create([
            'session_token' => $hashedToken,
            'name' => $request->name,
            'no_hp' => $request->no_hp,
            'status' => 'active',
        ]);

        // Return token both in response and set as a cookie (valid for 30 days, readable by JS)
        return response()->json([
            'success' => true,
            'token' => $token,
            'thread' => $thread,
        ])->cookie('guest_chat_token', $token, 60 * 24 * 30, null, null, null, true); // 30 days, httpOnly = true
    }

    /**
     * Fetch messages for the current guest thread.
     */
    public function fetchMessages(Request $request): JsonResponse
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json(['messages' => [], 'error' => 'Thread tidak ditemukan.'], 404);
        }

        $hashedToken = hash('sha256', $token);

        // Check short-term cache (TTL 8 seconds) for 'closed' status to avoid any DB queries
        $cachedStatus = Cache::get("guest_chat_closed_status_{$hashedToken}");
        if ($cachedStatus === 'closed') {
            $threadName = Cache::get("guest_chat_thread_name_{$hashedToken}", 'Guest');
            $cachedMessages = Cache::get("guest_chat_messages_{$hashedToken}", []);

            return response()->json([
                'messages' => $cachedMessages,
                'status' => 'closed',
                'thread_name' => $threadName,
            ]);
        }

        // Cache miss: query the DB
        $thread = $this->getThread($request);

        if (!$thread) {
            return response()->json(['messages' => [], 'error' => 'Thread tidak ditemukan.'], 404);
        }

        // If the thread is closed, cache its status and name
        if ($thread->status === 'closed') {
            Cache::put("guest_chat_closed_status_{$hashedToken}", 'closed', 8);
            Cache::put("guest_chat_thread_name_{$hashedToken}", $thread->name, 8);
        }

        $query = GuestChatMessage::where('guest_chat_thread_id', $thread->id);

        if ($request->has('after') && (int) $request->query('after') > 0) {
            $query->where('id', '>', (int) $request->query('after'));
            $messages = $query->orderBy('id', 'asc')
                ->with('sender:id,nama')
                ->get();
        } else {
            // First load: load last 50 messages to keep memory low, sorted chronologically (asc)
            $messages = $query->orderBy('id', 'desc')
                ->limit(50)
                ->with('sender:id,nama')
                ->get()
                ->reverse()
                ->values();
        }

        // Mark admin messages as read (only if active and unread admin messages exist)
        if ($thread->status === 'active') {
            $hasUnreadAdminMsg = $messages->contains(function ($msg) {
                return $msg->sender_type === 'admin' && !$msg->is_read;
            });

            if ($hasUnreadAdminMsg) {
                GuestChatMessage::where('guest_chat_thread_id', $thread->id)
                    ->where('sender_type', 'admin')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        }

        $formattedMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_type === 'guest' ? 'Anda' : ($msg->sender->nama ?? 'Admin'),
                'created_at' => $msg->created_at ? $msg->created_at->format('Y-m-d H:i:s') : '',
            ];
        })->toArray();

        // If the thread is closed, cache the final formatted messages to completely bypass DB next time
        if ($thread->status === 'closed') {
            Cache::put("guest_chat_messages_{$hashedToken}", $formattedMessages, 8);
        }

        return response()->json([
            'messages' => $formattedMessages,
            'status' => $thread->status,
            'thread_name' => $thread->name,
        ]);
    }

    /**
     * Send a message from the guest.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Sesi chat telah kedaluwarsa atau tidak valid.'], 403);
        }

        $hashedToken = hash('sha256', $token);

        // Check short-term cache for 'closed' status to prevent DB transaction/queries instantly
        if (Cache::get("guest_chat_closed_status_{$hashedToken}") === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.'
            ], 403);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $hashedToken) {
            // Lock the thread row for update to prevent concurrent updates or deletion
            $thread = $this->getThread($request, true);

            if (!$thread) {
                return response()->json(['success' => false, 'message' => 'Sesi chat telah kedaluwarsa atau tidak valid.'], 403);
            }

            if ($thread->status === 'closed') {
                Cache::put("guest_chat_closed_status_{$hashedToken}", 'closed', 8);
                Cache::put("guest_chat_thread_name_{$hashedToken}", $thread->name, 8);

                return response()->json([
                    'success' => false,
                    'message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.'
                ], 403);
            }

            $message = GuestChatMessage::create([
                'guest_chat_thread_id' => $thread->id,
                'sender_type' => 'guest',
                'message' => $request->message,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        });
    }
}
