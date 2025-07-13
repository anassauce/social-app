<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function connections()
    {
        return $this->hasMany(Connection::class);
    }

    public function connectedUsers()
    {
        return $this->belongsToMany(User::class, 'connections', 'user_id', 'connected_user_id')
                    ->wherePivot('status', 'accepted');
    }

    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }

    public function receivedInvitations()
    {
        return $this->hasMany(Invitation::class, 'recipient_id');
    }

    public function isConnectedTo($userId)
    {
        return Connection::where(function ($query) use ($userId) {
            $query->where('user_id', $this->id)
                  ->where('connected_user_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('connected_user_id', $this->id);
        })->where('status', 'accepted')->exists();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function createToken($name, $abilities = ['*'])
    {
        $token = hash('sha256', $plainTextToken = \Str::random(40));

        $this->tokens()->create([
            'name' => $name,
            'token' => $token,
            'abilities' => $abilities,
        ]);

        return new class($plainTextToken) {
            public function __construct(public $plainTextToken) {}
            public function __toString() { return $this->plainTextToken; }
        };
    }
}
