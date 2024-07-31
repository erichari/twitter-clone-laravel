<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'tweet_id'];


    /**
     * いいねしたユーザーの取得
     */
    public function likeUser(){
        return $this->belongsTo(User::class);
    }

    /**
     *いいねされたツイート情報
     */
    public function likedTweet(){
        return $this->belongsTo(Tweet::class);
    }

}
