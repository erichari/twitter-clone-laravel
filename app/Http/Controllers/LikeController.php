<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\User;
use App\Models\Tweet;

class LikeController extends Controller
{
        //
    /**
     * いいね削除
     * 
     * @param Request $request
     * @return Response
     */
    public function deleteLike(Request $request){
        $login_user = $request->user();

        $like_id = $request->like_id;
        Like::find($like_id)->update([
            'status' => 'deleted',
            'updated_at' => now(),
        ]);

        return true;
    }
    //
    /**
     * いいね
     * 
     * @param Request $request
     * @return Response
     */
    public function likeTweet(Request $request){
        $tweet_id = $request->tweet_id;

        $liked_user_id = Tweet::find($tweet_id)->user_id;
        
        $like = $request->user()->likes()->create([
            'tweet_id' => $tweet_id,
        ]);

        $request->user()->sentNotifications()->create([
            'received_user_id' => $liked_user_id,
            'message' => 'さんにいいねされました',
            'liked_tweet_id' => $tweet_id,
        ]);

        $param = [
            'like_id' => $like->id,
        ];
        
        return response()->json($param);
    }
    
}
