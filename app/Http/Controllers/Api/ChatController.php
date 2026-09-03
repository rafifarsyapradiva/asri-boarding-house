<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    /**
     * Fetch all messages for a specific reservation.
     */
    public function fetch(Request $request, Reservasi $reservasi): JsonResponse
    {
        // Kebijakan Hak Akses (Security Gate) via Policy
        Gate::authorize('chat', $reservasi);

        $query = ChatMessage::where('reservasi_id', $reservasi->id);

        if ($request->has('after') && (int) $request->query('after') > 0) {
            $query->where('id', '>', (int) $request->query('after'));
            $messages = $query->orderBy('id', 'asc')
                ->with('sender:id,nama,role')
                ->get();
        } else {
            // First load: load last 50 messages to keep memory low, sorted chronologically (asc)
            $messages = $query->orderBy('id', 'desc')
                ->limit(50)
                ->with('sender:id,nama,role')
                ->get()
                ->reverse()
                ->values();
        }

        // Otomatisasi Status Terbaca (Mark as Read) hanya jika ada pesan baru yang belum dibaca dari lawan bicara
        if ($messages->isNotEmpty()) {
            $hasUnreadFromPartner = $messages->contains(function ($msg) {
                return $msg->sender_id !== Auth::id() && !$msg->is_read;
            });

            if ($hasUnreadFromPartner) {
                ChatMessage::where('reservasi_id', $reservasi->id)
                    ->where('sender_id', '!=', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        }

        // Map messages to format expected by the frontend JavaScript
        $formattedMessages = [];
        if ($messages->isNotEmpty()) {
            $formattedMessages = $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at ? (is_string($msg->created_at) ? $msg->created_at : $msg->created_at->format('Y-m-d H:i:s')) : '',
                    'sender' => $msg->sender->nama ?? 'Sistem',
                    'is_admin' => ($msg->sender->role ?? '') === 'admin',
                ];
            });
        }

        $isPolling = $request->has('after') && (int) $request->query('after') > 0;
        
        // Cache the reservation status for 60 seconds to avoid hitting MySQL database repeatedly during polling
        $status = \Illuminate\Support\Facades\Cache::remember("reservasi_status_{$reservasi->id}", 60, function () use ($reservasi) {
            return $reservasi->status;
        });
        
        $isClosed = !in_array($status, ['pending', 'dp', 'lunas']);

        // Bandwidth Optimization: Only return status & is_closed on first load (after = 0) OR if there are new messages.
        // For empty polling responses, omit redundant global state to save byte payload size.
        if ($isPolling && $messages->isEmpty()) {
            $responsePayload = [
                'messages' => [],
            ];
            if ($isClosed) {
                $responsePayload['is_closed'] = true;
            }
            return response()->json($responsePayload);
        }

        return response()->json([
            'messages' => $formattedMessages,
            'status' => $status,
            'is_closed' => $isClosed,
        ]);
    }

    /**
     * Send a new message inside a specific reservation chat thread.
     */
    public function send(Request $request, Reservasi $reservasi): JsonResponse
    {
        // Kebijakan Hak Akses (Security Gate) via Policy
        Gate::authorize('chat', $reservasi);

        $status = \Illuminate\Support\Facades\Cache::remember("reservasi_status_{$reservasi->id}", 60, function () use ($reservasi) {
            return $reservasi->status;
        });

        if (!in_array($status, ['pending', 'dp', 'lunas'])) {
            return response()->json(['message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.'], 403);
        }

        // Validasi input
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Sanitasi input pesan untuk mencegah Stored XSS
        $sanitizedMessage = strip_tags($request->message);
        if (trim($sanitizedMessage) === '' && trim($request->message) !== '') {
            return response()->json(['message' => 'Pesan tidak valid atau hanya berisi tag HTML.'], 422);
        }

        // Simpan pesan baru
        $message = ChatMessage::create([
            'reservasi_id' => $reservasi->id,
            'sender_id' => Auth::id(),
            'message' => $sanitizedMessage,
            'is_read' => false,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}
