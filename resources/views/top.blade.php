@extends('layouts.app')

@section('content')
@include('common.side')
<div class="main">
    <div class="header">
        <p>ホーム</p>
    </div>
    <!--つぶやき投稿エリア-->
    <div class="tweet-area">
        <!-- バリデーションエラーの表示 -->
        @include('common.errors')
        <div class="my-icon">
            <img src="{{ $login_user->user_image }}" class="user-icon">
        </div>
        <form action="/postTweet" method="POST" enctype="multipart/form-data">
            @csrf
            <textarea name="body" placeholder="いまどうしてる？"></textarea>
            <div class="file">
                <input type="file" name="tweet_image">
                <button type="submit" class="btn">つぶやく</button>
            </div>
        </form>
    </div>

    <!--仕切りエリア-->
    <div class="division-area"></div>

    <!--つぶやき一覧エリア-->
    @include('common.tweets')
</div>

@endsection