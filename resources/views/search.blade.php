@extends('layouts.app')

@section('content')

@include('common.side')
<div class="main">
    <div class="header">
        <p>検索</p>
    </div>
    <div class="search-area">
        <form action="/searchTweet" method="post">
            @csrf
            <input type="text" name="keyword" placeholder="キーワード検索" value="{{$keyword}}" class="search-box">
            <input type="submit" value="検索" class="btn">
        </form>
    </div>

    <!--仕切りエリア-->
    <div class="division-area"></div>

    <!--検索結果一覧エリア-->
    @include('common.tweets')

</div>

@endsection