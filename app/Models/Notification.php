<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'received_user_id', 'message', 'liked_tweet_id'];

    public function noticedUser(){
        //通知を保持するユーザー
        $this->belongsTo(User::class);
    }
}
