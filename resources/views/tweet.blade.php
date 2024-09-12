@extends('layouts.app')

@section('content')

@include('common.side')
<div class="main">
    <div class="header">
        <p>ツイート</p>
    </div>

    @if($requested_tweet->status == 'deleted')
        <p>このツイートは削除されました。</p>
    @else
        <!-- 返信先のツイートエリア -->
        @if($replied_tweet)
        <div class="tweet">
            <div class="my-icon">
                <a href="/tweet/{{$replied_tweet->id}}" class="stretched-link"></a>
                <a href="/profile/{{$replied_tweet->user_id}}">
                    <img src="{{ $replied_tweet->user_image }}" class="user-icon">
                </a>
            </div>

            <div class="tweet-container">
                <div class="name-area">
                    <div>
                        <span>{{ $replied_tweet->nickname }}</span>
                        <span class="time"> {{ '@'.$replied_tweet->name }}</span>
                        @inject('time', 'App\Util')
                        <span class="time"> {{$time->convertToDayTimeAgo($replied_tweet->created_at)}} </span>
                    </div>
                    @if($replied_tweet->user_id == $login_user->id)
                    <div class="icon" class="user-icon" data-toggle="popover" data-bs-trigger="focus" tabindex="0" data-bs-content="<a href='deleteTweet/{{$replied_tweet->id}}'>ツイートを削除</a>">
                        <img src="{{asset('/images/img/icon-menu.svg')}}">
                    </div>
                    @endif
                </div>

                <div class="body">
                    <p>{{ $replied_tweet->body }}</p>
                    @if($replied_tweet->tweet_image)
                    <div class="tweet-image" data-bs-toggle="modal" data-bs-target="#js-modal-image-{{$replied_tweet->id}}">
                            <img src="{{ $replied_tweet->tweet_image }}">
                    </div>
                    @endif

                    <div class="modal fade" id="js-modal-image-{{$replied_tweet->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <img src="{{ $replied_tweet->tweet_image }}">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="action-area">
                    <!-- リプライ -->
                    <div class="like icon reply-icon" data-bs-toggle="modal" data-bs-target="#js-modal-reply-{{$replied_tweet->id}}">
                        <i class="fa-regular fa-comment ja-reply-icon"></i>
                        @if($replied_tweet->reply_count > 0)
                            <p>{{$replied_tweet->reply_count}}</p>
                        @else
                            <p>　</p>
                        @endif
                    </div>

                    <div class="modal fade" id="js-modal-reply-{{$replied_tweet->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="tweet mb-3">
                                        <div class="my-icon">
                                            <a href="/profile/{{$replied_tweet->user_id}}">
                                                <img src="{{ $replied_tweet->user_image }}" class="user-icon">
                                            </a>
                                        </div>

                                        <div class="tweet-container">
                                            <div class="name-area">
                                                <div>
                                                    <span>{{ $replied_tweet->nickname }}</span>
                                                    <span class="time"> {{ '@'.$replied_tweet->name }}</span>
                                                    <span class="time"> {{$time->convertToDayTimeAgo($replied_tweet->created_at)}} </span>
                                                </div>
                                            </div>

                                            <div class="body">
                                                <p>{{ $replied_tweet->body }}</p>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tweet-area">
                                        <!-- バリデーションエラーの表示 -->
                                        @include('common.errors')
                                        <div class="my-icon">
                                            <img src="{{ $login_user->user_image }}" class="user-icon">
                                        </div>
                                        <form action="/postTweet" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="text" name="reply_id" value="{{$replied_tweet->id}}" hidden>
                                            <textarea name="body" placeholder="返信をツイート"></textarea>
                                            <div class="file">
                                                <input type="file" name="tweet_image">
                                                <button type="submit" class="btn">返信</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- リツイートボタン -->
                    <button type="button" popovertarget="retweet-{{$replied_tweet->id}}" class="icon retweet-icon">
                        <div popover id="retweet-{{$replied_tweet->id}}">
                            @if($replied_tweet->R_id)
                            <div class="js-retweet" data-retweet-id="{{$replied_tweet->R_id}}">リツイートを取り消す</div>
                            @else
                            <div class="js-retweet" data-tweet-id="{{$replied_tweet->id}}">リツイート</div>
                            @endif
                            <div data-bs-toggle="modal" data-bs-target="#js-modal-quote-{{$replied_tweet->id}}">引用</div>
                        </div>
                    
                        @if($replied_tweet->R_id)
                            <i class="fa-solid fa-retweet" style="color:lightgreen;"></i>
                        @else
                            <i class="fa-solid fa-retweet" style="color:black;"></i>
                        @endif
                        <span class="js-retweets-count">{{$replied_tweet->retweet_count}}</span>
                    </button>

                    <div class="modal fade" id="js-modal-quote-{{$replied_tweet->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="tweet-area">
                                        <!-- バリデーションエラーの表示 -->
                                        @include('common.errors')
                                        <div class="my-icon">
                                            <img src="{{ $login_user->user_image }}" class="user-icon">
                                        </div>
                                        <form action="/postTweet" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="text" name="retweet_id" value="{{$replied_tweet->id}}" hidden>
                                            <textarea name="body" placeholder="コメントを追加"></textarea>

                                            <div class="tweet quote-tweet mb-3">
                                                <div class="my-icon quote-icon">
                                                    <img src="{{ $replied_tweet->user_image }}" class="user-icon">
                                                </div>

                                                <div class="tweet-container">
                                                    <div class="name-area">
                                                        <div>
                                                            <span>{{ $replied_tweet->nickname }}</span>
                                                            <span class="time"> {{ '@'.$replied_tweet->name }}</span>
                                                            <span class="time"> {{$time->convertToDayTimeAgo($replied_tweet->created_at)}} </span>
                                                        </div>
                                                    </div>

                                                    <div class="body">
                                                        <p>{{ $replied_tweet->body }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="file">
                                                <input type="file" name="tweet_image">
                                                <button type="submit" class="btn">返信</button>
                                            </div>

                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- いいね -->
                    <div class="like js-like icon like-icon" data-tweet-id="{{$replied_tweet->id}}" data-like-id="{{$replied_tweet->like_id}}">
                            @if($replied_tweet->like_id)
                                <img src="{{ asset('/images/img/icon-heart-twitterblue.svg') }}">
                            @else
                                <img src="{{ asset('/images/img/icon-heart.svg') }}">
                            @endif
                        <p class="js-likes-count">{{$replied_tweet->like_count}}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif


        <!-- リクエストされたツイートエリア -->
        <div class="tweet-detail">
            <div class="tweet">
                <div class="tweet-container">
                    <div class="name-area">
                        <div class="detail-name">
                            <div class="my-icon">
                                <a href="/profile/{{$requested_tweet->user_id}}">
                                    <img src="{{ $requested_tweet->user_image }}" class="user-icon">
                                </a>
                            </div>
                            <div class="">
                                <div class="nickname">{{ $requested_tweet->nickname }}</div>
                                <div class="time"> {{ '@'.$requested_tweet->name }}</div>
                            </div>
                        </div>
                        @if($requested_tweet->replied_user_id == $login_user->id)
                            <div class="icon" class="user-icon" data-toggle="popover" data-bs-trigger="focus" tabindex="0" data-bs-content="<a href='deleteTweet/{{$requested_tweet->id}}'>ツイートを削除</a>">
                                <img src="{{asset('/images/img/icon-menu.svg')}}">
                            </div>
                        @endif
                    </div>

                    <div class="body">
                        @if($requested_tweet->reply_id)
                            <a href="/profile/{{$requested_tweet->replied_user_id}}">{{'@'.$requested_tweet->replied_user_name}}</a>
                        @endif
                        <p>{{ $requested_tweet->body }}</p>
                        @if($requested_tweet->tweet_image)
                        <div class="tweet-image" data-bs-toggle="modal" data-bs-target="#js-modal-image-{{$requested_tweet->id}}">
                                <img src="{{ $requested_tweet->tweet_image }}">
                        </div>
                        @endif

                        @if($requested_tweet->retweet_id)
                        <!-- 引用部分 -->
                        <div class="tweet quote-tweet quote mb-3">
                            <a href="/tweet/{{$requested_tweet->retweet_id}}" class="stretched-link quote-stretched-link"></a>
                            <div class="my-icon quote-icon">
                                <img src="{{ $requested_tweet->retweet_user_image }}" class="user-icon">
                            </div>
                            <div class="tweet-container">
                                <div class="name-area">
                                    <div>
                                        <span>{{ $requested_tweet->retweet_user_nickname }}</span>
                                        <span class="time"> {{ '@'.$requested_tweet->retweet_user_name }}</span>
                                        @inject('time', 'App\Util')
                                        <span class="time"> {{$time->convertToDayTimeAgo($requested_tweet->retweet_created_at)}} </span>
                                    </div>
                                </div>
                                <div class="body">
                                    <p>{{ $requested_tweet->retweet_body }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- 引用部分終わり -->
                        @endif

                        <div class="modal fade" id="js-modal-image-{{$requested_tweet->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <img src="{{ $requested_tweet->tweet_image }}">
                                </div>
                            </div>
                        </div>

                        @inject('time', 'App\Util')
                        <span class="time"> {{$time->convertToDayTimeAgo($requested_tweet->created_at)}} </span>
                    </div>

                    <div class="action-area">
                        <!-- リプライ -->
                        <div class="like icon reply-icon" data-bs-toggle="modal" data-bs-target="#js-modal-reply-{{$requested_tweet->id}}">
                            <i class="fa-regular fa-comment ja-reply-icon"></i>
                            @if($requested_tweet->reply_count > 0)
                                <p class="js-reply-count">{{$requested_tweet->reply_count}}</p>
                            @else
                            @endif
                        </div>

                        <div class="modal fade" id="js-modal-reply-{{$requested_tweet->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="tweet mb-3">
                                            <div class="my-icon">
                                                <a href="/profile/{{$requested_tweet->user_id}}">
                                                    <img src="{{ $requested_tweet->user_image }}" class="user-icon">
                                                </a>
                                            </div>

                                            <div class="tweet-container">
                                                <div class="name-area">
                                                    <div>
                                                        <span>{{ $requested_tweet->nickname }}</span>
                                                        <span class="time"> {{ '@'.$requested_tweet->name }}</span>
                                                        <span class="time"> {{$time->convertToDayTimeAgo($requested_tweet->created_at)}} </span>
                                                    </div>
                                                </div>

                                                <div class="body">
                                                    <p>{{ $requested_tweet->body }}</p>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="tweet-area">
                                            <!-- バリデーションエラーの表示 -->
                                            @include('common.errors')
                                            <div class="my-icon">
                                                <img src="{{ $login_user->user_image }}" class="user-icon">
                                            </div>
                                            <form action="/postTweet" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="text" name="reply_id" value="{{$requested_tweet->id}}" hidden>
                                                <textarea name="body" placeholder="返信をツイート"></textarea>
                                                <div class="file">
                                                    <input type="file" name="tweet_image">
                                                    <button type="submit" class="btn">返信</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- リツイートボタン -->
                        <button type="button" popovertarget="retweet-requested-{{$requested_tweet->id}}" class="icon retweet-icon">
                            <div popover id="retweet-requested-{{$requested_tweet->id}}">
                                @if($requested_tweet->R_id)
                                <div class="js-retweet" data-retweet-id="{{$requested_tweet->R_id}}">リツイートを取り消す</div>
                                @else
                                <div class="js-retweet" data-tweet-id="{{$requested_tweet->id}}">リツイート</div>
                                @endif
                                <div data-bs-toggle="modal" data-bs-target="#js-modal-requested-quote-{{$requested_tweet->id}}">引用</div>
                            </div>
                        
                            @if($requested_tweet->R_id)
                                <i class="fa-solid fa-retweet" style="color:lightgreen;"></i>
                            @else
                                <i class="fa-solid fa-retweet" style="color:black;"></i>
                            @endif
                            <span class="js-retweets-count">{{$requested_tweet->retweet_count}}</span>
                        </button>

                        <div class="modal fade" id="js-modal-requested-quote-{{$requested_tweet->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="tweet-area">
                                            <!-- バリデーションエラーの表示 -->
                                            @include('common.errors')
                                            <div class="my-icon">
                                                <img src="{{ $login_user->user_image }}" class="user-icon">
                                            </div>
                                            <form action="/postTweet" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="text" name="retweet_id" value="{{$requested_tweet->id}}" hidden>
                                                <textarea name="body" placeholder="コメントを追加"></textarea>

                                                <div class="tweet quote-tweet mb-3">
                                                    <div class="my-icon quote-icon">
                                                        <img src="{{ $requested_tweet->user_image }}" class="user-icon">
                                                    </div>

                                                    <div class="tweet-container">
                                                        <div class="name-area">
                                                            <div>
                                                                <span>{{ $requested_tweet->nickname }}</span>
                                                                <span class="time"> {{ '@'.$requested_tweet->name }}</span>
                                                                <span class="time"> {{$time->convertToDayTimeAgo($requested_tweet->created_at)}} </span>
                                                            </div>
                                                        </div>

                                                        <div class="body">
                                                            <p>{{ $requested_tweet->body }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="file">
                                                    <input type="file" name="tweet_image">
                                                    <button type="submit" class="btn">返信</button>
                                                </div>

                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- いいね -->
                        <div class="like js-like icon like-icon" data-tweet-id="{{$requested_tweet->id}}" data-like-id="{{$requested_tweet->like_id}}">
                            @if($requested_tweet->like_id)
                                <img src="{{ asset('/images/img/icon-heart-twitterblue.svg') }}">
                            @else
                                <img src="{{ asset('/images/img/icon-heart.svg') }}">
                            @endif
                            <p class="js-likes-count">{{$requested_tweet->like_count}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--仕切りエリア-->
        <div class="division-area"></div>

        <!--リプライ一覧エリア-->
        @include('common.tweets')

        </div>
    @endif

@endsection