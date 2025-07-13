<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'connected_user_id',
        'status',
        'connected_at'
    ];

    protected $casts = [
        'connected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function connectedUser()
    {
        return $this->belongsTo(User::class, 'connected_user_id');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
                    ->orWhere('connected_user_id', $userId);
    }
}