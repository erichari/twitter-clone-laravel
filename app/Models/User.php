<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'nickname',
        'name',
        'email',
        'password',
        'profile',
        'user_image',
        'header_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    public function tweets(){
        //ユーザーの保持する全ツイート
        return $this->hasMany(Tweet::class);
    }

    public function likes(){
        //ユーザーが保持するいいね
        return $this->hasMany(Like::class);
    }

    public function sentNotifications(){
        //ユーザーが送った全通知
        return $this->hasMany(Notification::class, 'sent_user_id');
    }

    public function receivedNotifications(){
        //ユーザーが保持する全通知
        return $this->hasMany(Notification::class, 'received_user_id');
    }

    public function follows(){
        //ユーザーが保持するフォロー
        //U.id--F.user_id
        return $this->hasMany(Follow::class);
    }

    public function followers(){
        //ユーザーが保持するフォロワー
        //U.id--F.followed_user_id
        return $this->hasMany(Follow::class, 'followed_user_id');
    }
}
