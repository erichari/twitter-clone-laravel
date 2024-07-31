<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'body', 'tweet_image', 'reply_id', 'retweet_id'];

    public function reply(){ 
        //ツイートが保持するリプライの取得
        return $this->hasMany(self::class, 'reply_id');
    }

    public function repliedTweet(){ 
        //リプライされたツイートの取得
        return $this->belongsTo(self::class, 'reply_id');
    }

    public function replyTweet(){ 
        //ツイートが保持するリプライの取得
        return $this->hasMany(self::class, 'id', 'reply_id');
    }

    public function user(){ 
        //ツイートを保持するユーザーの取得
        //T.user_id--U.id
        return $this->belongsTo(User::class);
    }

    public function like(){
        //ツイートが保持するいいね
        return $this->hasMany(Like::class);
    }
    
}
