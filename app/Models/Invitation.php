<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'status',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('sender_id', $userId)
                    ->orWhere('recipient_id', $userId);
    }

    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'responded_at' => now()
        ]);

        Connection::create([
            'user_id' => $this->sender_id,
            'connected_user_id' => $this->recipient_id,
            'status' => 'accepted',
            'connected_at' => now()
        ]);

        Connection::create([
            'user_id' => $this->recipient_id,
            'connected_user_id' => $this->sender_id,
            'status' => 'accepted',
            'connected_at' => now()
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected',
            'responded_at' => now()
        ]);
    }
}