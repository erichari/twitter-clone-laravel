@extends('layouts.app')

@section('content')

@include('common.side')
<div class="main">
    <div class="header">
        <p>通知</p>
    </div>

    <!--仕切りエリア-->
    <div class="division-area"></div>

    <!--通知一覧エリア-->
    <div class="tweets-list">
    @if(count($notifications) == 0)
        <p>通知はありません。</p>

    @else
        @foreach($notifications as $notification)
            @if($notification->sent_user_id == $login_user->id)
                @continue
            @endif
            <div class="tweet">
                <div class="my-icon">
                    <a href="/profile/{{$notification->sent_user_id}}">
                        <img src="{{ asset($notification->user_image) }}" class="user-icon">
                    </a>
                </div>

                <div class="tweet-container">
                    <p>
                        <span class="nickname">{{ $notification->nickname }}</span>
                        <span>{{$notification->message}}</span>
                    </p>

                    <div class="liked-tweet-body">
                        <p>{{ $notification->body }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
</div>



@endsection