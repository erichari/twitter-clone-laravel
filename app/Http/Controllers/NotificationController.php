<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    //
    /**
     * 通知画面に遷移
     * 
     * @return Response
     */
    public function notification(Request $request){
        $login_user = $request->user();

        $notifications = $login_user->receivedNotifications()
            ->join('users', 'notifications.sent_user_id', 'users.id')
            ->leftjoin('tweets', 'notifications.liked_tweet_id', 'tweets.id')
            ->select('notifications.*', 'users.nickname', 'users.user_image', 'tweets.body')
            ->get();

        return view('/notification', [
            'login_user' => $login_user,
            'notifications' => $notifications,
        ]);
    }
}
