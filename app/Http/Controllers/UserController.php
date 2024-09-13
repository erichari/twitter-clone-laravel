<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use DB;


class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * 会員登録画面
     * 
     * @return Response
     */
    public function register(){
        return view('register');
    }

    /**
     * プロフィール画面に遷移
     * 
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request){
        //ログイン中のユーザー
        $login_user = $request->user();

        //リクエストされたユーザー
        $requested_user = User::
        leftJoin('follows as F', function($join) use($login_user){
        $join->on('F.followed_user_id', '=', 'users.id')
        ->where('F.status', '=', 'active')
        ->where('user_id', '=', $login_user->id);
        })
        ->select('users.*', 'F.id as follow_id', DB::raw('(SELECT COUNT(*) FROM follows WHERE status = "active" AND user_id = users.id) AS follows_count'), DB::raw('(SELECT COUNT(*) FROM follows WHERE status = "active" AND followed_user_id = users.id) AS followers_count'))
        ->find($request->id);

        //リクエストされたユーザーのツイート一覧
        $tweets = $requested_user->tweets()
        ->join('users as U', 'U.id', '=', 'tweets.user_id')
        ->leftJoin('tweets as R', function($join) use($login_user){
            $join->on('R.retweet_id', '=', 'tweets.id')
            ->where('R.status', 'active')
            ->where('R.user_id', $login_user->id);
        })
        ->leftJoin('likes as L', function($join) use($login_user){
            $join->on('L.tweet_id', '=', 'tweets.id')
            ->where('L.status', '=', 'active')
            ->where('L.user_id', '=', $login_user->id);
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
        ->select('tweets.*', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = tweets.id) AS reply_count'),DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = tweets.id) AS retweet_count'),
                'U.nickname', 'U.name', 'U.user_image',
                'retweet.id as retweet_id', 'retweet.user_id as retweet_user_id', 'retweet.body as retweet_body', 'retweet.tweet_image as retweet_tweet_image', 'retweet.created_at as retweet_created_at', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = retweet.id) AS retweet_reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = retweet.id) AS retweet_retweet_count'),
                'retweet_user.nickname as retweet_user_nickname', 'retweet_user.name as retweet_user_name', 'retweet_user.user_image as retweet_user_image',
                'retweet_replied_user.id as replied_user_id', 'retweet_replied_user.name as replied_user_name',
                'R.id as R_id', 'retweet_R.id as retweet_R_id',
                'L.id as like_id', 'retweet_L.id as retweet_like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = tweets.id) AS like_count'), DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = retweet.id) AS retweet_like_count'))
        ->orderBy('created_at', 'desc')
        ->get();

        //リクエストされたユーザーのリプライ一覧
        $replies = $requested_user->tweets()
        ->join('users as U', 'U.id', '=', 'tweets.user_id')
        ->leftJoin('tweets as R', function($join) use($login_user){
            $join->on('R.retweet_id', '=', 'tweets.id')
            ->where('R.status', 'active')
            ->where('R.user_id', $login_user->id);
        })
        ->leftJoin('likes as L', function($join) use($login_user){
            $join->on('L.tweet_id', '=', 'tweets.id')
            ->where('L.status', '=', 'active')
            ->where('L.user_id', '=', $login_user->id);
        })
        ->where([['tweets.status', 'active'], ['tweets.reply_id', '!=', null]])
        ->join('tweets as replied_tweet', 'replied_tweet.id', 'tweets.reply_id')
        ->join('users as replied_user', 'replied_user.id', 'replied_tweet.user_id')
        ->select('tweets.*', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = tweets.id) AS reply_count'),DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = tweets.id) AS retweet_count'),
                'U.nickname', 'U.name', 'U.user_image',
                'replied_user.id as replied_user_id', 'replied_user.name as replied_user_name',
                'R.id as R_id',
                'L.id as like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = tweets.id) AS like_count'))
        ->orderBy('created_at', 'desc')
        ->get();

        //リクエストされたユーザーのいいね一覧
        $likes = $requested_user->likes()
        ->join('tweets', 'tweets.id', '=', 'likes.tweet_id')
        ->join('users as U', 'U.id', '=', 'tweets.user_id')
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
        ->where([['tweets.status', 'active'], ['likes.status', 'active']])
        ->leftjoin('tweets as retweet', 'retweet.id', 'tweets.retweet_id')
        ->leftjoin('users as retweet_user', 'retweet_user.id', 'retweet.user_id')
        ->leftjoin('tweets as replied_tweet', 'replied_tweet.id', 'tweets.reply_id')
        ->leftjoin('users as replied_user', 'replied_user.id', 'replied_tweet.user_id')
        ->select('tweets.*', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = tweets.id) AS reply_count'),DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = tweets.id) AS retweet_count'),
                'U.nickname', 'U.name', 'U.user_image',
                'retweet.id as retweet_id', 'retweet.status as retweet_status', 'retweet.user_id as retweet_user_id', 'retweet.body as retweet_body', 'retweet.tweet_image as retweet_tweet_image', 'retweet.reply_id as retweet_reply_id', 'retweet.created_at as retweet_created_at', DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.reply_id = retweet.id) AS retweet_reply_count'), DB::raw('(SELECT COUNT(*) FROM tweets AS T WHERE status = "active" AND T.retweet_id = retweet.id) AS retweet_retweet_count'),
                'retweet_user.nickname as retweet_user_nickname', 'retweet_user.name as retweet_user_name', 'retweet_user.user_image as retweet_user_image',
                'replied_user.id as replied_user_id', 'replied_user.name as replied_user_name',
                'R.id as R_id',
                'L.id as like_id', DB::raw('(SELECT COUNT(*) FROM likes WHERE status = "active" AND tweet_id = tweets.id) AS like_count'))
        ->orderBy('created_at', 'desc')
        ->get();

        return view('/profile', [
            'login_user' => $login_user,
            'requested_user' => $requested_user,
            'tweets' => $tweets,
            'replies' => $replies,
            'likes' => $likes,
        ]);
    }

    /**
     * プロフィール編集
     * 
     * @param Request $request
     * @return Response
     */
    public function editProfile(Request $request){
        $login_user = $request->user();

        $this->validate($request, [
            'nickname' => 'required|max:50',
            'name' => 'required|max:50|regex:/^[a-zA-Z0-9-_]+$/',
            'email' => 'required|max:255|email',
            'profile' => 'max:160',
        ]);
        
        User::find($login_user->id)->update([
            'nickname' => $request->nickname,
            'name' => $request->name,
            'email' => $request->email,
            'profile' => $request->profile,
            'updated_at' => now(),
        ]);

        //ヘッダーがアップロードされてる場合
        if($request->header_image){
            $this->validate($request, [
                'header_image' => 'file|image|mimes:jpeg,png,jpg|max:1000',
            ]);

            //////// storageを使う場合
            // $dir = 'header';
            // $image_name = $request->user()->id . '_' . date('YmdHis');
            // $request->header_image->storeAs('public/'.$dir, $image_name);
            // $request->header_image = 'storage/'.$dir.'/'.$image_name;

            //base64を使う場合
            $base64Image = base64_encode(file_get_contents($request->header_image->getRealPath()));
            $mimeType = $request->header_image->getMimeType();

            User::find($login_user->id)->update([
                'header_image' => 'data:' . $mimeType . ';base64,' . $base64Image,
            ]);
            }

        //アイコンがアップロードされてる場合
        if($request->image){
            $this->validate($request, [
                'image' => 'file|image|mimes:jpeg,png,jpg|max:1000',
            ]);

            //////// storageを使う場合
            // $dir = 'user';
            // $image_name = $request->user()->id . '_' . date('YmdHis');
            // $request->image->storeAs('public/'.$dir, $image_name);
            // $request->image = 'storage/'.$dir.'/'.$image_name;

            //base64を使う場合
            $base64Image = base64_encode(file_get_contents($request->image->getRealPath()));
            $mimeType = $request->image->getMimeType();
            
            User::find($login_user->id)->update([
                'user_image' => 'data:' . $mimeType . ';base64,' . $base64Image,
            ]);
        }

        if($request->password){
            $this->validate($request, [
                'password' => 'min:8',
            ]);

            User::find($login_user->id)->update([
                'password' => $request->password,
            ]);
        }
        
        return redirect('/profile/'.$login_user->id);
    }

    /**
     * フォローしているユーザー一覧画面
     */
    public function followingUsers(Request $request){
        $following_users = $request->user()->followUser();
        dd($following_users);

        return view('followingUsers', [
            'following_users' => $following_users,
        ]);
    }

    
}
