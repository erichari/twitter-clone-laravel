<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Tweet;
use App\Models\User;
use App\Models\Like;
use App\Models\Follow;
use DB;

class TweetController extends Controller
{
    /**
     * コンストラクタ
     * 
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * トップ画面表示 （ツイート一覧）
     * 
     * @param Request $request
     * @return Response
     */
    public function tweets(Request $request){
        $login_user = $request->user();

        $following_users = $login_user->follows()
        ->join('users as U', 'U.id', '=', 'follows.followed_user_id')
        ->where('follows.status', 'active')
        ->select('U.id')
        ->get();

        foreach($following_users as $following_user){
            $following_user_ids[] = $following_user->id;
        }
        $following_user_ids[] = $login_user->id;

        $tweets = Tweet::
        join('users as U', 'U.id', '=', 'tweets.user_id')
        ->whereIn('U.id', $following_user_ids)
        ->leftJoin('tweets as R', function($join) use($login_user){
            $join->on('R.retweet_id', '=', 'tweets.id')
            ->where('R.status', 'active')
            ->where('R.user_id', $login_user->id);
        })
        ->leftJoin('likes as L', function($join) use($login_user){
            $join->on('L.tweet_id', '=', 'tweets.id')
            ->where('L.status', 'active')
            ->where('L.user_id', $login_user->id);
        })
        ->where([['tweets.status', 'active'], ['tweets.reply_id', null]])
        ->leftjoin('tweets as retweet', 'retweet.id', 'tweets.retweet_id')
        ->leftjoin('users as retweet_user', 'retweet_user.id', 'retweet.user_id')
        ->leftJoin('tweets as retweet_R', function($join) use($login_user){
            $join->on('retweet_R.retweet_id', '=', 'retweet.id')
            ->where('retweet_R.status', 'active')
            ->where('retweet_R.user_id', $login_user->id);
        })
        ->leftJoin('likes as retweet_L', function($join) use($login_user){
            $join->on('retweet_L.tweet_id', '=', 'retweet.id')
            ->where('retweet_L.status', 'active')
            ->where('retweet_L.user_id', $login_user->id);
        })
        ->leftjoin('tweets as retweet_replied_tweet', 'retweet_replied_tweet.id', 'retweet.reply_id')
        ->leftjoin('users as retweet_replied_user', 'retweet_replied_user.id', 'retweet_replied_tweet.user_id')
        ->select('tweets.*', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = tweets.id) AS reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = tweets.id) AS retweet_count'),
                'U.nickname', 'U.name', 'U.user_image',
                'retweet.id as retweet_id', 'retweet.status as retweet_status', 'retweet.user_id as retweet_user_id', 'retweet.body as retweet_body', 'retweet.tweet_image as retweet_tweet_image', 'retweet.reply_id as retweet_reply_id', 'retweet.created_at as retweet_created_at', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = retweet.id) AS retweet_reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = retweet.id) AS retweet_retweet_count'),
                'retweet_user.nickname as retweet_user_nickname', 'retweet_user.name as retweet_user_name', 'retweet_user.user_image as retweet_user_image',
                'retweet_replied_user.id as replied_user_id', 'retweet_replied_user.name as replied_user_name',
                'R.id as R_id', 'retweet_R.id as retweet_R_id',
                'L.id as like_id', 'retweet_L.id as retweet_like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = tweets.id) AS like_count'), DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = retweet.id) AS retweet_like_count'))
        ->orderBy('created_at', 'desc')
        ->get();
        // dd($tweets);
        
        return view('/top', [
            'login_user' => $login_user,
            'tweets' => $tweets,
        ]);
    }

    /**
     * ツイート詳細画面に遷移
     * 
     * @param Request $request
     * @return Response
     */
    public function tweet(Request $request){
        $login_user = $request->user();

        //リクエストされたツイートのリプライ先
        $replied_tweet = Tweet::
            join('tweets as replied_tweet', 'replied_tweet.id', '=', 'tweets.reply_id')
            ->join('users as U', 'U.id', '=', 'replied_tweet.user_id')
            ->leftJoin('likes as L', function($join) use($login_user){
                $join->on('L.tweet_id', '=', 'replied_tweet.id')
                ->where('L.status', 'active')
                ->where('L.user_id', $login_user->id);
            })
            ->leftJoin('tweets as R', function($join) use($login_user){
                $join->on('R.retweet_id', '=', 'replied_tweet.id')
                ->where('R.status', 'active')
                ->where('R.user_id', $login_user->id);
            })
            ->where('replied_tweet.status', 'active')
            ->select('replied_tweet.*', DB::raw('(SELECT COUNT(*) FROM tweets WHERE status = "active" AND reply_id = replied_tweet.id) AS reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = replied_tweet.id) AS retweet_count'),
                'U.nickname', 'U.name', 'U.user_image',
                'R.id as R_id',
                'L.id as like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = replied_tweet.id) AS like_count'))
            ->find($request->id);

        //リクエストされたツイート
        $requested_tweet = Tweet::
            join('users as U', 'U.id', '=', 'tweets.user_id')
            ->leftJoin('likes as L', function($join) use($login_user){
                $join->on('L.tweet_id', '=', 'tweets.id')
                ->where('L.status', 'active')
                ->where('L.user_id', $login_user->id);
            })
            ->leftJoin('tweets as R', function($join) use($login_user){
                $join->on('R.retweet_id', '=', 'tweets.id')
                ->where('R.status', 'active')
                ->where('R.user_id', $login_user->id);
            })
            ->leftjoin('tweets as retweet', 'retweet.id', 'tweets.retweet_id')
            ->leftjoin('users as retweet_user', 'retweet_user.id', 'retweet.user_id')
            ->leftjoin('tweets as replied_tweet', 'replied_tweet.id', 'tweets.reply_id')
            ->leftjoin('users as replied_user', 'replied_user.id', 'replied_tweet.user_id')
            ->select('tweets.*', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = tweets.id) AS reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = tweets.id) AS retweet_count'),
                'U.nickname', 'U.name', 'U.user_image',
                'retweet.id as retweet_id', 'retweet.status as retweet_status', 'retweet.user_id as retweet_user_id', 'retweet.body as retweet_body', 'retweet.tweet_image as retweet_tweet_image', 'retweet.reply_id as retweet_reply_id', 'retweet.created_at as retweet_created_at', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = retweet.id) AS retweet_reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = retweet.id) AS retweet_retweet_count'),
                'retweet_user.nickname as retweet_user_nickname', 'retweet_user.name as retweet_user_name', 'retweet_user.user_image as retweet_user_image',
                'replied_user.id as replied_user_id', 'replied_user.name as replied_user_name',
                'R.id as R_id',
                'L.id as like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = tweets.id) AS like_count'))
                ->find($request->id);

        //リクエストされたツイートに対するリプライ一覧
        $replies = Tweet::
            leftJoin('tweets as replies', 'replies.reply_id', '=', 'tweets.id')
            ->join('users as U', 'U.id', '=', 'replies.user_id')
            ->leftJoin('likes as L', function($join) use($login_user){
                $join->on('L.tweet_id', '=', 'replies.id')
                ->where('L.status', 'active')
                ->where('L.user_id', $login_user->id);
            })
            ->leftJoin('tweets as R', function($join) use($login_user){
                $join->on('R.retweet_id', '=', 'replies.id')
                ->where('R.status', 'active')
                ->where('R.user_id', $login_user->id);
            })
            ->where([['replies.status', 'active'], ['replies.reply_id', $request->id]])
            ->leftjoin('tweets as replied_tweet', 'replied_tweet.id', 'replies.reply_id')
            ->leftjoin('users as replied_user', 'replied_user.id', 'replied_tweet.user_id')
            ->select('replies.*', DB::raw('(SELECT COUNT(*) FROM tweets WHERE status = "active" AND reply_id = replies.id) AS reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = replies.id) AS retweet_count'),
                'U.nickname', 'U.name', 'U.user_image',
                'replied_user.id as replied_user_id', 'replied_user.name as replied_user_name',
                'R.id as R_id',
                'L.id as like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = replies.id) AS like_count'))
            ->orderBy('replies.created_at', 'desc')
            ->get();

        
        return view('/tweet', [
            'login_user' => $login_user,
            'replied_tweet' => $replied_tweet,
            'requested_tweet' => $requested_tweet,
            'tweets' => $replies,
        ]);
    }
    


    
    
    /**
     * 検索画面に遷移
     * 
     * @param Request $request
     * @return Response
     */
    public function search(Request $request){
        $login_user = $request->user();
        $tweets = $request->session()->get('tweets');
        $keyword = $request->session()->get('keyword');

        return view('/search', [
            'login_user' => $login_user,
            'tweets' => $tweets,
            'keyword' => $keyword,
        ]);
    }

    /**
     * 検索結果一覧
     * 
     * @param Request $request
     * @return Response
     */
    public function searchTweet(Request $request){
        $login_user = $request->user();
        $keyword = $request->keyword;
        $escape_keyword = addcslashes($keyword, '\\_%');
        $tweets = Tweet::
            join('users as U', 'tweets.user_id', '=', 'U.id')
            ->leftJoin('likes as L', function($join) use($login_user){
                $join->on('L.tweet_id', '=', 'tweets.id')
                ->where('L.status', 'active')
                ->where('L.user_id', $login_user->id);
            })
            ->leftJoin('tweets as R', function($join) use($login_user){
                $join->on('R.retweet_id', '=', 'tweets.id')
                ->where('R.status', 'active')
                ->where('R.user_id', $login_user->id);
            })
            ->where('tweets.status', 'active')
            ->leftjoin('tweets as replied_tweet', 'replied_tweet.id', 'tweets.reply_id')
            ->leftjoin('users as replied_user', 'replied_user.id', 'replied_tweet.user_id')
            ->select('tweets.*', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = tweets.id) AS reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = tweets.id) AS retweet_count'),
                    'U.nickname', 'U.name', 'U.user_image',
                    'replied_user.id as replied_user_id', 'replied_user.name as replied_user_name',
                    'R.id as R_id',
                    'L.id as like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = tweets.id) AS like_count'))
                    ->where(Tweet::raw('CONCAT(U.nickname, U.name, tweets.body)'), 'like', '%'.$escape_keyword.'%')
            ->orderBy('created_at', 'desc')
            ->get();

        return redirect()->route('search')->with([
            'tweets' => $tweets,
            'keyword' => $escape_keyword,
        ]);
    }

    /**
     * ツイート画面に遷移
     * 
     * @return Response
     */
    public function post(Request $request){
        $login_user = $request->user();
        return view('/post', [
            'login_user' => $login_user,
        ]);
    }

    /**
     * ツイートを投稿
     * 
     * @param Request $request
     * @return Response
     */
    public function postTweet(Request $request){
        if($request->tweet_id){
            $request->user()->tweets()->create([
                'retweet_id' => $request->tweet_id,
            ]);
            return true;

        }elseif($request->retweet_id){
            $this->validate($request, [
                'body' => 'required|max:140',
                'tweet_image' => 'file|image|mimes:jpeg,png,jpg|max:1000',
            ]);

            if($request->tweet_image){
                $dir = 'tweet';
                $image_name = $request->user()->id . '_' . date('YmdHis');
                $request->tweet_image->storeAs('public/'.$dir, $image_name);
                $request->tweet_image = 'storage/'.$dir.'/'.$image_name;
                }

            $request->user()->tweets()->create([
                'body' => $request->body,
                'tweet_image' => $request->tweet_image,
                'retweet_id' => $request->retweet_id,
            ]);

            return back();

        }else{

            $this->validate($request, [
                'body' => 'required|max:140',
                'tweet_image' => 'file|image|mimes:jpeg,png,jpg|max:1000',
            ]);

            //Tweet::create([
            //    'user_id' => $request->user()->id,
            //    'body' => $request->body,
            //    'image_name' => $request->image_name,
            //]);
            if($request->tweet_image){
                $dir = 'tweet';
                $image_name = $request->user()->id . '_' . date('YmdHis');
                $request->tweet_image->storeAs('public/'.$dir, $image_name);
                $request->tweet_image = 'storage/'.$dir.'/'.$image_name;
                }

            $request->user()->tweets()->create([
                'body' => $request->body,
                'tweet_image' => $request->tweet_image,
                'reply_id' => $request->reply_id,
            ]);

            return back();
        }
    }




    /**
     * ツイート削除
     * 
     * @param Request $request
     * @return Response
     */
    public function deleteTweet(Request $request){
        
        
        $tweet = Tweet::find($request->id);
        $this->authorize('deleteTweet', $tweet);

        $tweet->update([
            'status' => 'deleted',
            'updated_at' => now(),
        ]);
        
        return back();
    }
}