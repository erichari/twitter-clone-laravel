@extends('layouts.app')

@section('content')

@include('common.side')
<div class="main">
    <div class="header">
        <p>{{$requested_user->nickname}}</p>
    </div>

    <!-- プロフィールエリア -->
    <div class="profile-area">
        <div class="profile-header">
            <img src="{{ $requested_user->header_image }}">
        </div>
        <div class="user-image-container">
            <div class="profile-icon">
                <img src="{{ $requested_user->user_image }}" class="user-icon">
            </div>
            
            @if($requested_user->id !== $login_user->id)
            <!--相手の画面-->
                @if($requested_user->follow_id)
                    <button class="btn js-follow" data-followed-user-id="{{$requested_user->id}}" data-follow-id="{{$requested_user->follow_id}}">フォローを外す</button>
                @else
                    <button class="btn btn-reverse js-follow" data-followed-user-id="{{$requested_user->id}}">フォローする</button>
                @endif
            @else
            <!--自分の画面-->
                <button class="btn btn-reverse" data-bs-toggle="modal" data-bs-target="#js-modal">プロフィール編集</button>

                <div class="modal fade" id="js-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="/profile" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">プロフィールを編集</h5>
                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="modal-profile-header">
                                        <label class="mb-1" for="header_image">
                                            <img src="{{ $requested_user->header_image }}" class="">
                                        </label>
                                        <input type="file" class="form-control form-control-sm" name="header_image" id="header_image" hidden>
                                    </div>

                                    <div class="mb-3">
                                        <label class="modal-my-icon mb-1" for="user_image">
                                            <img src="{{ $requested_user->user_image }}" class="user-icon">
                                        </label>
                                        <input type="file" class="form-control form-control-sm" name="image" id="user_image" hidden>
                                    </div>

                                    <input type="text" class="form-control mb-4" name="nickname" placeholder="ニックネーム" value="{{$requested_user->nickname}}" required>
                                    <input type="text" class="form-control mb-4" name="name" placeholder="ユーザー名" value="{{$requested_user->name}}" required>
                                    <textarea class="form-control mb-4" name="profile" placeholder="自己紹介文">{{$requested_user->profile}}</textarea>
                                    <input type="email" class="form-control mb-4" name="email" placeholder="メールアドレス" value="{{$requested_user->email}}" required>
                                    <input type="password" class="form-control mb-4" name="password" placeholder="パスワードを変更する場合ご入力ください">
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-reverse" data-bs-dismiss="modal">キャンセル</button>
                                    <button class="btn" type="submit">保存する</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="user-name-container">
            <p class="nickname">{{$requested_user->nickname}}</p>
            <p class="name">{{'@'.$requested_user->name}}</p>
            <p class="name">{{$requested_user->profile}}</p>
        </div>

        <div class="user-follow-container">
            <a href="/followingUsers/{{$requested_user->id}}"><p><span>{{$requested_user->follows_count}}</span> フォロー中</p></a>
            <a href="/followers/{{$requested_user->id}}"><p><span class="js-followers-count">{{$requested_user->followers_count}}</span> フォロワー</p></a>
        </div>
    </div>

    <!--仕切りエリア-->
    <div class="division-area"></div>

    
    <!--タブ見出し-->
    <ul class="nav nav-tabs nav-fill" id="tabs">
        <li class="nav-item"><a href="#tweets" class="nav-link active" data-bs-toggle="tab">ツイート</a></li>
        <li class="nav-item"><a href="#replies" class="nav-link" data-bs-toggle="tab">返信</a></li>
        <li class="nav-item"><a href="#likes" class="nav-link" data-bs-toggle="tab">いいね</a></li>
    </ul>

    <div class="tab-content">
        <div id="tweets" class="tab-pane active">
            <!--つぶやき一覧エリア-->
            @include('common.tweets')
        </div>

        <div id="replies" class="tab-pane">
            <!-- リプライ一覧エリア -->
            <div class="tweets-list">  
                @if(count($replies) == 0)
                    <p>返信はありません。</p>

                @else
                    @foreach($replies as $reply)
                        <div class="tweet">
                            <div class="my-icon">
                                <a href="/tweet/{{$reply->id}}" class="stretched-link"></a>
                                <a href="/profile/{{$reply->user_id}}">
                                    <img src="{{ $reply->user_image }}" class="user-icon">
                                </a>
                            </div>

                            <div class="tweet-container">
                                <p>
                                    <span>{{ $reply->nickname }}</span>
                                    <span class="time"> {{ '@'.$reply->name }}</span>
                                    @inject('time', 'App\Util')
                                    <span class="time">{{$time->convertToDayTimeAgo($reply->created_at)}} </span>
                                </p>

                                <div class="body">
                                    <a href="/profile/{{$reply->replied_user_id}}">{{'@'.$reply->replied_user_name}}</a>
                                    <p>{{ $reply->body }}</p>
                                    @if($reply->tweet_image)
                                        <div class="tweet-image" data-bs-toggle="modal" data-bs-target="#js-modal-image-{{$reply->id}}">
                                            <img src="{{ $reply->tweet_image }}">
                                        </div>
                                    @endif

                                    <div class="modal fade" id="js-modal-image-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <img src="{{ $reply->tweet_image }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="action-area">
                                    <!-- リプライ -->
                                    <div class="like icon reply-icon" data-bs-toggle="modal" data-bs-target="#js-modal-likes-reply-{{$reply->id}}">
                                        <i class="fa-regular fa-comment ja-reply-icon"></i>
                                        @if($reply->reply_count > 0)
                                            <p>{{$reply->reply_count}}</p>
                                        @else
                                            <p>　</p>
                                        @endif
                                    </div>

                                    <div class="modal fade" id="js-modal-likes-reply-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="tweet mb-3">
                                                        <div class="my-icon">
                                                            <a href="/profile/{{$reply->user_id}}">
                                                                <img src="{{ $reply->user_image }}" class="user-icon">
                                                            </a>
                                                        </div>

                                                        <div class="tweet-container">
                                                            <div class="name-area">
                                                                <div>
                                                                    <span>{{ $reply->nickname }}</span>
                                                                    <span class="time"> {{ '@'.$reply->name }}</span>
                                                                    <span class="time"> {{$time->convertToDayTimeAgo($reply->created_at)}} </span>
                                                                </div>
                                                            </div>

                                                            <div class="body">
                                                                <p>{{ $reply->body }}</p>
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
                                                            <input type="text" name="reply_id" value="{{$reply->id}}" hidden>
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
                                    <button type="button" popovertarget="retweet-{{$reply->id}}" class="icon retweet-icon">
                                        <div popover id="retweet-{{$reply->id}}">
                                            @if($reply->R_id)
                                            <div class="js-retweet" data-retweet-id="{{$reply->R_id}}">リツイートを取り消す</div>
                                            @else
                                            <div class="js-retweet" data-tweet-id="{{$reply->id}}">リツイート</div>
                                            @endif
                                            <div data-bs-toggle="modal" data-bs-target="#js-modal-quote-{{$reply->id}}">引用</div>
                                        </div>
                                    
                                        @if($reply->R_id)
                                            <i class="fa-solid fa-retweet" style="color:lightgreen;"></i>
                                        @else
                                            <i class="fa-solid fa-retweet" style="color:black;"></i>
                                        @endif
                                        <span class="js-retweets-count">{{$reply->retweet_count}}</span>
                                    </button>

                                    <div class="modal fade" id="js-modal-quote-{{$reply->id}}" tabindex="-1" aria-hidden="true">
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
                                                            <input type="text" name="retweet_id" value="{{$reply->id}}" hidden>
                                                            <textarea name="body" placeholder="コメントを追加"></textarea>

                                                            <div class="tweet quote-tweet mb-3">
                                                                <div class="my-icon quote-icon">
                                                                    <img src="{{ $reply->user_image }}" class="user-icon">
                                                                </div>

                                                                <div class="tweet-container">
                                                                    <div class="name-area">
                                                                        <div>
                                                                            <span>{{ $reply->nickname }}</span>
                                                                            <span class="time"> {{ '@'.$reply->name }}</span>
                                                                            <span class="time"> {{$time->convertToDayTimeAgo($reply->created_at)}} </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="body">
                                                                        <p>{{ $reply->body }}</p>
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
                                    <div class="like js-like icon like-icon" data-tweet-id="{{$reply->id}}" data-like-id="{{$reply->like_id}}">
                                        @if($reply->like_id)
                                            <img src="{{ asset('/images/img/icon-heart-twitterblue.svg') }}">
                                        @else
                                            <img src="{{ asset('/images/img/icon-heart.svg') }}">
                                        @endif
                                        <p class="js-likes-count">{{$reply->like_count}}</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                    @endforeach
                @endif
            </div>
        </div>





        <div id="likes" class="tab-pane">
            <!--いいねしたツイート一覧エリア-->
            <div class="tweets-list">  
                @if(empty($likes))

                @elseif(count($likes) == 0)
                    <p>いいねしたツイートはありません。</p>

                @else
                    @foreach($likes as $like)
                        <div class="tweet">
                            <div class="my-icon">
                                <a href="/tweet/{{$like->id}}" class="stretched-link"></a>
                                <a href="/profile/{{$like->user_id}}">
                                    <img src="{{ $like->user_image }}" class="user-icon">
                                </a>
                            </div>

                            <div class="tweet-container">
                                <p>
                                    <span>{{ $like->nickname }}</span>
                                    <span class="time"> {{ '@'.$like->name }}</span>
                                    @inject('time', 'App\Util')
                                    <span class="time">{{$time->convertToDayTimeAgo($like->created_at)}} </span>
                                </p>

                                <div class="body">
                                    @if($like->reply_id)
                                        <a href="/profile/{{$like->replied_user_id}}">{{'@'.$like->replied_user_name}}</a>
                                    @endif
                                    <p>{{ $like->body }}</p>
                                    @if($like->tweet_image)
                                    <div class="tweet-image" data-bs-toggle="modal" data-bs-target="#js-modal-image-{{$like->tweet_id}}">
                                            <img src="{{ $like->tweet_image }}">
                                    </div>
                                    @endif

                                    <div class="modal fade" id="js-modal-image-{{$like->tweet_id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <img src="{{ $like->tweet_image }}">
                                            </div>
                                        </div>
                                    </div>

                                    @if($like->retweet_id)
                                    <!-- 引用部分 -->
                                    <div class="tweet quote-tweet quote mb-3">
                                        <a href="/tweet/{{$like->retweet_id}}" class="stretched-link quote-stretched-link"></a>
                                        <div class="my-icon quote-icon">
                                            <img src="{{ $like->retweet_user_image }}" class="user-icon">
                                        </div>
                                        <div class="tweet-container">
                                            <div class="name-area">
                                                <div>
                                                    <span>{{ $like->retweet_user_nickname }}</span>
                                                    <span class="time"> {{ '@'.$like->retweet_user_name }}</span>
                                                    <span class="time"> {{$time->convertToDayTimeAgo($like->retweet_created_at)}} </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <p>{{ $like->retweet_body }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 引用部分終わり -->
                                    @endif
                                    
                                </div>

                                <div class="action-area">
                                    <!-- リプライ -->
                                    <div class="like icon reply-icon" data-bs-toggle="modal" data-bs-target="#js-modal-likes-reply-{{$like->id}}">
                                        <i class="fa-regular fa-comment ja-reply-icon"></i>
                                        @if($like->reply_count > 0)
                                            <p>{{$like->reply_count}}</p>
                                        @else
                                            <p>　</p>
                                        @endif
                                    </div>

                                    <div class="modal fade" id="js-modal-likes-reply-{{$like->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="tweet mb-3">
                                                        <div class="my-icon">
                                                            <a href="/profile/{{$like->user_id}}">
                                                                <img src="{{ $like->user_image }}" class="user-icon">
                                                            </a>
                                                        </div>

                                                        <div class="tweet-container">
                                                            <div class="name-area">
                                                                <div>
                                                                    <span>{{ $like->nickname }}</span>
                                                                    <span class="time"> {{ '@'.$like->name }}</span>
                                                                    <span class="time"> {{$time->convertToDayTimeAgo($like->created_at)}} </span>
                                                                </div>
                                                            </div>

                                                            <div class="body">
                                                                <p>{{ $like->body }}</p>
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
                                                            <input type="text" name="reply_id" value="{{$like->id}}" hidden>
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
                                    <button type="button" popovertarget="likes-retweet-{{$like->id}}" class="icon retweet-icon">
                                        <div popover id="likes-retweet-{{$like->id}}">
                                            @if($like->R_id)
                                            <div class="js-retweet" data-retweet-id="{{$like->R_id}}">リツイートを取り消す</div>
                                            @else
                                            <div class="js-retweet" data-tweet-id="{{$like->id}}">リツイート</div>
                                            @endif
                                            <div data-bs-toggle="modal" data-bs-target="#js-modal-likes-quote-{{$like->id}}">引用</div>
                                        </div>
                                    
                                        @if($like->R_id)
                                            <i class="fa-solid fa-retweet" style="color:lightgreen;"></i>
                                        @else
                                            <i class="fa-solid fa-retweet" style="color:black;"></i>
                                        @endif
                                        <span class="js-retweets-count">{{$like->retweet_count}}</span>
                                    </button>

                                    <div class="modal fade" id="js-modal-likes-quote-{{$like->id}}" tabindex="-1" aria-hidden="true">
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
                                                            <input type="text" name="retweet_id" value="{{$like->id}}" hidden>
                                                            <textarea name="body" placeholder="コメントを追加"></textarea>

                                                            <div class="tweet quote-tweet mb-3">
                                                                <div class="my-icon quote-icon">
                                                                    <img src="{{ $like->user_image }}" class="user-icon">
                                                                </div>

                                                                <div class="tweet-container">
                                                                    <div class="name-area">
                                                                        <div>
                                                                            <span>{{ $like->nickname }}</span>
                                                                            <span class="time"> {{ '@'.$like->name }}</span>
                                                                            <span class="time"> {{$time->convertToDayTimeAgo($like->created_at)}} </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="body">
                                                                        <p>{{ $like->body }}</p>
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
                                    <div class="like js-like icon like-icon" data-tweet-id="{{$like->id}}" data-like-id="{{$like->like_id}}">
                                        @if($like->like_id)
                                            <img src="{{ asset('/images/img/icon-heart-twitterblue.svg') }}">
                                        @else
                                            <img src="{{ asset('/images/img/icon-heart.svg') }}">
                                        @endif
                                        <p class="js-likes-count">{{$like->like_count}}</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                    @endforeach
                @endif
            </div>

        </div>
    </div>

</div>

@endsection





