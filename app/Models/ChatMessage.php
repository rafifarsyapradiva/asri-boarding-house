<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'reservasi_id',
        'sender_id',
        'message',
        'is_read',
    ];

    public function reservasi(): BelongsTo
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id')->withTrashed();
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }
}
