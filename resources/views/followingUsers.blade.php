@extends('layouts.app')

@section('content')

@include('common.side')
<div class="main">
    <div class="header">
        <p>{{$requested_user->nickname}}</p>
    </div>

    <div class="tweets-list">
        @if($following_users)
        <!--フォロー一覧-->
            @if(count($following_users) == 0)
                <p>フォローしているユーザーはいません。</p>
            @else
                @foreach($following_users as $following_user)
                    <div class="follower-container">
                        <div class="tweet">
                            <div class="my-icon">
                                <a href="/profile/{{$following_user->u_id}}">
                                    <img src="{{ $following_user->user_image }}" class="user-icon">
                                </a>
                            </div>

                            <div class="user-name-container">
                                <p class="nickname">{{ $following_user->nickname }}</p>
                                <p class="name">{{'@'.$following_user->name}}</p>
                            </div>
                        </div>

                        <div class="btn-container">
                        @if($following_user->u_id == $login_user->id)
                        
                        @elseif($following_user->follow_id)
                            <button class="btn js-follow" data-followed-user-id="{{$following_user->u_id}}" data-follow-id="{{$following_user->follow_id}}">フォローを外す</button>
                        @else
                            <button class="btn btn-reverse js-follow" data-followed-user-id="{{$following_user->u_id}}">フォローする</button>
                        @endif
                        </div>
                    </div>
                @endforeach
            @endif

        @else
        <!--フォロワー一覧-->
            @if(count($followers) == 0)
                <p>フォロワーはいません。</p>
            @else
                @foreach($followers as $follower)
                    <div class="follower-container">
                        <div class="tweet">
                            <div class="my-icon">
                                <a href="/profile/{{$follower->u_id}}">
                                    <img src="{{ $follower->user_image }}" class="user-icon">
                                </a>
                            </div>

                            <div class="user-name-container">
                                <p class="nickname">{{ $follower->nickname }}</p>
                                <p class="name">{{'@'.$follower->name}}</p>
                            </div>
                        </div>

                        <div class="btn-container">
                        @if($follower->u_id == $login_user->id)
                        
                        @elseif($follower->follow_id)
                            <button class="btn js-follow" data-followed-user-id="{{$follower->u_id}}" data-follow-id="{{$follower->follow_id}}">フォローを外す</button>
                        @else
                            <button class="btn btn-reverse js-follow" data-followed-user-id="{{$follower->u_id}}">フォローする</button>
                        @endif
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
    </div>
</div>

@endsection