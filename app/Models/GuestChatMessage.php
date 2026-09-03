<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestChatMessage extends Model
{
    use HasFactory;

    protected $table = 'guest_chat_messages';

    protected $fillable = [
        'guest_chat_thread_id',
        'sender_type',
        'sender_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the thread that owns the message.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(GuestChatThread::class, 'guest_chat_thread_id');
    }

    /**
     * Get the admin user who sent the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }
}
