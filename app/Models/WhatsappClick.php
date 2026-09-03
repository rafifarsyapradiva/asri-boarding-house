<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappClick extends Model
{
    protected $table = 'whatsapp_clicks';

    protected $fillable = [
        'source',
        'kamar_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the room associated with this click.
     */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
