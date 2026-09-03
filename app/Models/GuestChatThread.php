<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GuestChatThread extends Model
{
    use HasFactory;

    protected $table = 'guest_chat_threads';

    protected $fillable = [
        'session_token',
        'name',
        'no_hp',
        'status',
    ];

    /**
     * Get all of the messages for the guest chat thread.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(GuestChatMessage::class, 'guest_chat_thread_id');
    }

    /**
     * Get the latest message for the guest chat thread.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(GuestChatMessage::class, 'guest_chat_thread_id')->latestOfMany('id');
    }
}
