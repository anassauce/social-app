<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_ai_generated',
        'ai_prompt',
        'status',
        'visibility'
    ];

    protected $casts = [
        'is_ai_generated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isLikedBy($userId)
    {
        try {
            return $this->likes()->where('user_id', $userId)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function likesCount()
    {
        try {
            return $this->likes()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForConnectedUsers($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            // User's own posts (all visibility levels)
            $q->where('user_id', $userId)
              // Public posts from anyone
              ->orWhere(function ($subQ) {
                  $subQ->where('visibility', 'public')
                       ->where('status', 'published');
              })
              // Posts from connected users (connections visibility)
              ->orWhere(function ($subQ) use ($userId) {
                  $subQ->where('visibility', 'connections')
                       ->where('status', 'published')
                       ->whereHas('user', function ($userQ) use ($userId) {
                           $userQ->whereHas('connections', function ($connQ) use ($userId) {
                               $connQ->where('connected_user_id', $userId)
                                     ->where('status', 'accepted');
                           })
                           ->orWhereHas('connectedUsers', function ($connQ) use ($userId) {
                               $connQ->where('users.id', $userId);
                           });
                       });
              });
        });
    }
}
