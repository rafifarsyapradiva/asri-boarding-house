<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestChatThread;
use App\Models\GuestChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class GuestChatController extends Controller
{
    /**
     * Display the guest chats management dashboard.
     */
    public function index()
    {
        return view('admin.guest-chats.index');
    }

    /**
     * Get list of all guest chat threads via AJAX.
     */
    public function listThreads(Request $request): JsonResponse
    {
        $threadsPaginator = GuestChatThread::with(['latestMessage'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_type', 'guest')
                    ->where('is_read', false);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $threadsPaginator->getCollection()->transform(function ($thread) {
            $preview = $thread->latestMessage ? $thread->latestMessage->message : 'Belum ada pesan.';
            if ($preview === '___CHAT_CLOSED___') {
                $preview = 'Chat ditutup oleh Admin.';
            }

            return [
                'id' => $thread->id,
                'name' => $thread->name,
                'no_hp' => $thread->no_hp,
                'status' => $thread->status,
                'unread_count' => $thread->unread_count,
                'last_message' => Str::limit($preview, 60),
                'updated_at' => $thread->updated_at ? $thread->updated_at->format('d M H:i') : '',
            ];
        });

        return response()->json(['threads' => $threadsPaginator]);
    }

    /**
     * Fetch all messages for a specific guest thread.
     */
    public function fetchMessages(GuestChatThread $thread, Request $request): JsonResponse
    {
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

        // Mark guest messages as read
        GuestChatMessage::where('guest_chat_thread_id', $thread->id)
            ->where('sender_type', 'guest')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $formattedMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_type === 'admin' ? ($msg->sender->nama ?? 'Admin') : 'Guest',
                'created_at' => $msg->created_at ? $msg->created_at->format('Y-m-d H:i:s') : '',
            ];
        });

        return response()->json([
            'messages' => $formattedMessages,
            'status' => $thread->status,
        ]);
    }

    /**
     * Send a reply message from admin.
     */
    public function sendMessage(GuestChatThread $thread, Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($thread, $request) {
            $lockedThread = GuestChatThread::where('id', $thread->id)->lockForUpdate()->first();
            if (!$lockedThread) {
                return response()->json(['success' => false, 'message' => 'Obrolan tidak ditemukan.'], 404);
            }

            if ($lockedThread->status === 'closed') {
                $lockedThread->update(['status' => 'active']);
                \Illuminate\Support\Facades\Cache::forget("guest_chat_closed_status_{$lockedThread->session_token}");
                \Illuminate\Support\Facades\Cache::forget("guest_chat_messages_{$lockedThread->session_token}");
            }

            $message = GuestChatMessage::create([
                'guest_chat_thread_id' => $lockedThread->id,
                'sender_type' => 'admin',
                'sender_id' => Auth::id(),
                'message' => $request->message,
                'is_read' => false,
            ]);

            // Touch updated_at of the thread to bring it to the top of list
            $lockedThread->touch();

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        });
    }

    /**
     * Close the guest chat thread.
     */
    public function closeThread(GuestChatThread $thread): JsonResponse
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($thread) {
            $lockedThread = GuestChatThread::where('id', $thread->id)->lockForUpdate()->first();
            if (!$lockedThread) {
                return response()->json(['success' => false, 'message' => 'Obrolan tidak ditemukan.'], 404);
            }

            if ($lockedThread->status === 'closed') {
                return response()->json(['success' => true, 'message' => 'Obrolan sudah ditutup sebelumnya.']);
            }

            $lockedThread->update(['status' => 'closed']);

            // Send system message
            GuestChatMessage::create([
                'guest_chat_thread_id' => $lockedThread->id,
                'sender_type' => 'admin',
                'sender_id' => Auth::id(),
                'message' => '___CHAT_CLOSED___',
                'is_read' => true,
            ]);

            // Seed short-term cache for 'closed' status instantly
            $sessionToken = $lockedThread->session_token;
            Cache::put("guest_chat_closed_status_{$sessionToken}", 'closed', 8);
            Cache::put("guest_chat_thread_name_{$sessionToken}", $lockedThread->name, 8);

            return response()->json(['success' => true]);
        });
    }

    /**
     * Delete the guest chat thread permanently.
     */
    public function destroy(GuestChatThread $thread): JsonResponse
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($thread) {
            $lockedThread = GuestChatThread::where('id', $thread->id)->lockForUpdate()->first();
            if ($lockedThread) {
                $sessionToken = $lockedThread->session_token;
                $lockedThread->delete();

                // Clear cache on deletion
                Cache::forget("guest_chat_closed_status_{$sessionToken}");
                Cache::forget("guest_chat_thread_name_{$sessionToken}");
                Cache::forget("guest_chat_messages_{$sessionToken}");
            }
            return response()->json(['success' => true]);
        });
    }
}
