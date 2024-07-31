<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\User;
use App\Models\Notification;


class FollowController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //
    /**
     * フォロー削除
     * 
     * @param Request $request
     * @return Response
     */
    public function deleteFollow(Request $request){
        $login_user = $request->user();

        $follow_id = $request->follow_id;
        Follow::where('user_id', $login_user->id)->where('id', $follow_id)->update([
            'status' => 'deleted',
            'updated_at' => now(),
        ]);

        return true;
    }
    //
    /**
     * フォロー
     * 
     * @param Request $request
     * @return Response
     */
    public function followUser(Request $request){
        $followed_user_id = $request->followed_user_id;
        
        $follow = $request->user()->follows()->create([
            'followed_user_id' => $followed_user_id,
        ]);

        $request->user()->sentNotifications()->create([
            'received_user_id' => $followed_user_id,
            'message' => 'さんにフォローされました',
        ]);

        $param = [
            'follow_id' => $follow->id,
        ];
        
        return response()->json($param);
    }

    /**
     * フォローしているユーザー一覧画面
     */
    public function followingUsers(Request $request){
        $login_user = $request->user();
        $requested_user = User::find($request->id);

        $following_users = $requested_user->follows()
        ->join('users as U', 'U.id', '=', 'follows.followed_user_id')
        ->leftJoin('follows as F', function($join) use($login_user){
            $join->on('F.followed_user_id', '=', 'U.id')
            ->where('F.status', '=', 'active')
            ->where('F.user_id', '=', $login_user->id);
        })
        ->where('follows.status', 'active')
        ->select('U.id as u_id', 'U.nickname', 'U.name', 'U.user_image', 'F.id as follow_id')
        ->orderBy('follows.created_at', 'desc')
        ->get();
        // dd($following_users);

        return view('followingUsers', [
            'login_user' => $login_user,
            'requested_user' => $requested_user,
            'following_users' => $following_users,
        ]);
    }

    /**
     * フォロワー一覧画面
     */
    public function followers(Request $request){
        $login_user = $request->user();
        $requested_user = User::find($request->id);

        $followers = $requested_user->followers()
        ->join('users as U', 'U.id', '=', 'follows.user_id')
        ->leftJoin('follows as F', function($join) use($login_user){
            $join->on('F.followed_user_id', '=', 'U.id')
            ->where('F.status', '=', 'active')
            ->where('F.user_id', '=', $login_user->id);
        })
        ->where('follows.status', 'active')
        ->select('U.id as u_id', 'U.nickname', 'U.name', 'U.user_image', 'F.id as follow_id')
        ->orderBy('follows.created_at', 'desc')
        ->get();


        return view('followingUsers', [
            'login_user' => $login_user,
            'requested_user' => $requested_user,
            'followers' => $followers,
            'following_users' => null,
        ]);
    }
}
