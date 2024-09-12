<!--つぶやき一覧エリア-->
<div class="tweets-list">  
    @if(empty($tweets))

    @elseif(count($tweets) == 0)
        <p>ツイートはありません。</p>

    @else
        @foreach($tweets as $tweet)
            
            <!-- 引用ツイートの場合 -->
            @if($tweet->retweet_id && ($tweet->body || $tweet->tweet_image))
                @if($tweet->retweet_status == 'deleted')
                    @continue
                @endif
            <div class="tweet">
                <a href="/tweet/{{$tweet->id}}" class="stretched-link"></a>
                <div class="my-icon">
                    <a href="/profile/{{$tweet->user_id}}">
                        <img src="{{ $tweet->user_image }}" class="user-icon">
                    </a>
                </div>

                <div class="tweet-container">
                    <div class="name-area">
                        <div>
                            <span>{{ $tweet->nickname }}</span>
                            <span class="time"> {{ '@'.$tweet->name }}</span>
                            @inject('time', 'App\Util')
                            <span class="time"> {{$time->convertToDayTimeAgo($tweet->created_at)}} </span>
                        </div>
                        @if($tweet->user_id == $login_user->id)
                        <div class="icon" class="user-icon" data-toggle="popover" data-bs-trigger="focus" tabindex="0" data-bs-content="<a href='/deleteTweet/{{$tweet->id}}'>ツイートを削除</a>">
                            <img src="{{asset('/images/img/icon-menu.svg')}}">
                        </div>
                        @endif
                    </div>

                    <div class="body">
                        <p>{{ $tweet->body }}</p>
                        @if($tweet->tweet_image)
                        <div class="tweet-image" data-bs-toggle="modal" data-bs-target="#js-modal-image-{{$tweet->id}}">
                            <img src="{{ $tweet->tweet_image }}">
                        </div>
                        @endif

                        <div class="modal fade" id="js-modal-image-{{$tweet->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <img src="{{ $tweet->tweet_image }}">
                                </div>
                            </div>
                        </div>

                        <!-- 引用部分 -->
                        <div class="tweet quote-tweet quote mb-3">
                            <a href="/tweet/{{$tweet->retweet_id}}" class="stretched-link quote-stretched-link"></a>
                            <div class="my-icon quote-icon">
                                <img src="{{ $tweet->retweet_user_image }}" class="user-icon">
                            </div>
                            <div class="tweet-container">
                                <div class="name-area">
                                    <div>
                                        <span>{{ $tweet->retweet_user_nickname }}</span>
                                        <span class="time"> {{ '@'.$tweet->retweet_user_name }}</span>
                                        <span class="time"> {{$time->convertToDayTimeAgo($tweet->retweet_created_at)}} </span>
                                    </div>
                                </div>
                                <div class="body">
                                    <p>{{ $tweet->retweet_body }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- 引用部分終わり -->
                    </div>

                    <div class="action-area">
                        <!-- リプライボタン -->
                        <div class="like icon reply-icon" data-bs-toggle="modal" data-bs-target="#js-modal-reply-{{$tweet->id}}">
                            <i class="fa-regular fa-comment js-reply-icon"></i>
                            @if($tweet->reply_count > 0)
                                <p>{{$tweet->reply_count}}</p>
                            @else
                                <p>　</p>
                            @endif
                        </div>

                        <div class="modal fade" id="js-modal-reply-{{$tweet->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="tweet mb-3">
                                            <div class="my-icon">
                                                <a href="/profile/{{$tweet->user_id}}">
                                                    <img src="{{ $tweet->user_image }}" class="user-icon">
                                                </a>
                                            </div>

                                            <div class="tweet-container">
                                                <div class="name-area">
                                                    <div>
                                                        <span>{{ $tweet->nickname }}</span>
                                                        <span class="time"> {{ '@'.$tweet->name }}</span>
                                                        <span class="time"> {{$time->convertToDayTimeAgo($tweet->created_at)}} </span>
                                                    </div>
                                                </div>

                                                <div class="body">
                                                    <p>{{ $tweet->body }}</p>
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
                                                <input type="text" name="reply_id" value="{{$tweet->id}}" hidden>
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
                        <button type="button" popovertarget="retweet-{{$tweet->id}}" class="icon retweet-icon">
                            <div popover id="retweet-{{$tweet->id}}">
                                @if($tweet->R_id)
                                <div class="js-retweet" data-retweet-id="{{$tweet->R_id}}">リツイートを取り消す</div>
                                @else
                                <div class="js-retweet" data-tweet-id="{{$tweet->id}}">リツイート</div>
                                @endif
                                <div data-bs-toggle="modal" data-bs-target="#js-modal-quote-{{$tweet->id}}">引用</div>
                            </div>
                        
                            @if($tweet->R_id)
                                <i class="fa-solid fa-retweet" style="color:lightgreen;"></i>
                            @else
                                <i class="fa-solid fa-retweet" style="color:black;"></i>
                            @endif
                            <span class="js-retweets-count">{{$tweet->retweet_count}}</span>
                        </button>

                        <div class="modal fade" id="js-modal-quote-{{$tweet->id}}" tabindex="-1" aria-hidden="true">
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
                                                <input type="text" name="retweet_id" value="{{$tweet->id}}" hidden>
                                                <textarea name="body" placeholder="コメントを追加"></textarea>

                                                <div class="tweet quote-tweet mb-3">
                                                    <div class="my-icon quote-icon">
                                                        <img src="{{ $tweet->user_image }}" class="user-icon">
                                                    </div>

                                                    <div class="tweet-container">
                                                        <div class="name-area">
                                                            <div>
                                                                <span>{{ $tweet->nickname }}</span>
                                                                <span class="time"> {{ '@'.$tweet->name }}</span>
                                                                <span class="time"> {{$time->convertToDayTimeAgo($tweet->created_at)}} </span>
                                                            </div>
                                                        </div>

                                                        <div class="body">
                                                            <p>{{ $tweet->body }}</p>
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

                        <!-- いいねボタン -->
                        <div class="like js-like icon like-icon" data-tweet-id="{{$tweet->id}}" data-like-id="{{$tweet->like_id}}">
                                @if($tweet->like_id)
                                    <img src="{{ asset('/images/img/icon-heart-twitterblue.svg') }}">
                                @else
                                    <img src="{{ asset('/images/img/icon-heart.svg') }}">
                                @endif
                            <p class="js-likes-count">{{$tweet->like_count}}</p>
                        </div>
                    </div>
                </div>
            </div>





            <!-- リツイートの場合 -->
            @elseif($tweet->retweet_id)
            
                @if($tweet->retweet_status == 'deleted')
                    @continue
                @endif

            <p><a href="/profile/{{$tweet->user_id}}" class="retweet-comment"><i class="fa-solid fa-retweet"></i> {{$tweet->nickname}}さんがリツイートしました</p></a>
            <div class="tweet">
                <div class="my-icon">
                    <a href="/tweet/{{$tweet->retweet_id}}" class="stretched-link"></a>
                    <a href="/profile/{{$tweet->retweet_user_id}}">
                        <img src="{{ $tweet->retweet_user_image }}" class="user-icon">
                    </a>
                </div>

                <div class="tweet-container">
                    <div class="name-area">
                        <div>
                            <span>{{ $tweet->retweet_user_nickname }}</span>
                            <span class="time"> {{ '@'.$tweet->retweet_user_name }}</span>
                            @inject('time', 'App\Util')
                            <span class="time"> {{$time->convertToDayTimeAgo($tweet->retweet_created_at)}} </span>
                        </div>
                        @if($tweet->retweet_user_id == $login_user->id)
                        <div class="icon" class="user-icon" data-toggle="popover" data-bs-trigger="focus" tabindex="0" data-bs-content="<a href='/deleteTweet/{{$tweet->retweet_id}}'>ツイートを削除</a>">
                            <img src="{{asset('/images/img/icon-menu.svg')}}">
                        </div>
                        @endif
                    </div>

                    <div class="body">
                        @if($tweet->retweet_reply_id)
                            <p><a href="/profile/{{$tweet->replied_user_id}}">{{'@'.$tweet->replied_user_name}}</a></p>
                        @endif
                        <p>{{ $tweet->retweet_body }}</p>
                        @if($tweet->retweet_tweet_image)
                        <div class="tweet-image" data-bs-toggle="modal" data-bs-target="#js-modal-image-{{$tweet->retweet_id}}">
                                <img src="{{ $tweet->retweet_tweet_image }}">
                        </div>
                        @endif

                        <div class="modal fade" id="js-modal-image-{{$tweet->retweet_id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <img src="{{ $tweet->retweet_tweet_image }}">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="action-area">
                        <!-- リプライボタン -->
                        <div class="like icon reply-icon" data-bs-toggle="modal" data-bs-target="#js-modal-reply-{{$tweet->retweet_id}}">
                            <i class="fa-regular fa-comment js-reply-icon"></i>
                            @if($tweet->retweet_reply_count > 0)
                                <p>{{$tweet->retweet_reply_count}}</p>
                            @else
                                <p>　</p>
                            @endif
                        </div>

                        <div class="modal fade" id="js-modal-reply-{{$tweet->retweet_id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="tweet mb-3">
                                            <div class="my-icon">
                                                <a href="/profile/{{$tweet->retweet_user_id}}">
                                                    <img src="{{ $tweet->retweet_user_image }}" class="user-icon">
                                                </a>
                                            </div>

                                            <div class="tweet-container">
                                                <div class="name-area">
                                                    <div>
                                                        <span>{{ $tweet->retweet_nickname }}</span>
                                                        <span class="time"> {{ '@'.$tweet->retweet_name }}</span>
                                                        <span class="time"> {{$time->convertToDayTimeAgo($tweet->retweet_created_at)}} </span>
                                                    </div>
                                                </div>

                                                <div class="body">
                                                    <p>{{ $tweet->retweet_body }}</p>
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
                                                <input type="text" name="reply_id" value="{{$tweet->retweet_id}}" hidden>
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
                        <button type="button" popovertarget="retweet-{{$tweet->retweet_id}}" class="icon retweet-icon">
                            <div popover id="retweet-{{$tweet->retweet_id}}">
                                @if($tweet->retweet_R_id)
                                <div class="js-retweet" data-retweet-id="{{$tweet->retweet_R_id}}">リツイートを取り消す</div>
                                @else
                                <div class="js-retweet" data-tweet-id="{{$tweet->retweet_id}}">リツイート</div>
                                @endif
                                <div data-bs-toggle="modal" data-bs-target="#js-modal-quote-{{$tweet->retweet_id}}">引用</div>
                            </div>
                        
                            @if($tweet->retweet_R_id)
                                <i class="fa-solid fa-retweet" style="color:lightgreen;"></i>
                            @else
                                <i class="fa-solid fa-retweet" style="color:black;"></i>
                            @endif
                            <span class="js-retweets-count">{{$tweet->retweet_retweet_count}}</span>
                        </button>

                        <div class="modal fade" id="js-modal-quote-{{$tweet->retweet_id}}" tabindex="-1" aria-hidden="true">
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
                                                <input type="text" name="retweet_id" value="{{$tweet->retweet_id}}" hidden>
                                                <textarea name="body" placeholder="コメントを追加"></textarea>

                                                <div class="tweet quote-tweet mb-3">
                                                    <div class="my-icon quote-icon">
                                                        <img src="{{ $tweet->retweet_user_image }}" class="user-icon">
                                                    </div>

                                                    <div class="tweet-container">
                                                        <div class="name-area">
                                                            <div>
                                                                <span>{{ $tweet->retweet_user_nickname }}</span>
                                                                <span class="time"> {{ '@'.$tweet->retweet_user_name }}</span>
                                                                <span class="time"> {{$time->convertToDayTimeAgo($tweet->retweet_created_at)}} </span>
                                                            </div>
                                                        </div>

                                                        <div class="body">
                                                            <p>{{ $tweet->retweet_body }}</p>
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

                        <!-- いいねボタン -->
                        <div class="like js-like icon like-icon" data-tweet-id="{{$tweet->retweet_id}}" data-like-id="{{$tweet->retweet_like_id}}">
                                @if($tweet->retweet_like_id)
                                    <img src="{{ asset('/images/img/icon-heart-twitterblue.svg') }}">
                                @else
                                    <img src="{{ asset('/images/img/icon-heart.svg') }}">
                                @endif
                            <p class="js-likes-count">{{$tweet->retweet_like_count}}</p>
                        </div>
                    </div>
                </div>
            </div>
            







            @else
            <div class="tweet">
                <div class="my-icon">
                    <a href="/tweet/{{$tweet->id}}" class="stretched-link"></a>
                    <a href="/profile/{{$tweet->user_id}}">
                        <img src="{{ $tweet->user_image }}" class="user-icon">
                    </a>
                </div>

                <div class="tweet-container">
                    <div class="name-area">
                        <div>
                            <span>{{ $tweet->nickname }}</span>
                            <span class="time"> {{ '@'.$tweet->name }}</span>
                            @inject('time', 'App\Util')
                            <span class="time"> {{$time->convertToDayTimeAgo($tweet->created_at)}} </span>
                        </div>
                        @if($tweet->user_id == $login_user->id)
                        <div class="icon" class="user-icon" data-toggle="popover" data-bs-trigger="focus" tabindex="0" data-bs-content="<a href='/deleteTweet/{{$tweet->id}}'>ツイートを削除</a>">
                            <img src="{{asset('/images/img/icon-menu.svg')}}">
                        </div>
                        @endif
                    </div>

                    <div class="body">
                        @if($tweet->reply_id)
                            <a href="/profile/{{$tweet->replied_user_id}}">{{'@'.$tweet->replied_user_name}}</a>
                        @endif
                        <p>{{ $tweet->body }}</p>
                        @if($tweet->tweet_image)
                        <div class="tweet-image" data-bs-toggle="modal" data-bs-target="#js-modal-image-{{$tweet->id}}">
                                <img src="{{ $tweet->tweet_image }}">
                        </div>
                        @endif

                        <div class="modal fade" id="js-modal-image-{{$tweet->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <img src="{{ $tweet->tweet_image }}">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="action-area">
                        <!-- リプライボタン -->
                        <div class="like icon reply-icon" data-bs-toggle="modal" data-bs-target="#js-modal-reply-{{$tweet->id}}">
                            <i class="fa-regular fa-comment js-reply-icon"></i>
                            @if($tweet->reply_count > 0)
                                <p>{{$tweet->reply_count}}</p>
                            @else
                                <p>　</p>
                            @endif
                        </div>

                        <div class="modal fade" id="js-modal-reply-{{$tweet->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="tweet mb-3">
                                            <div class="my-icon">
                                                <a href="/profile/{{$tweet->user_id}}">
                                                    <img src="{{ $tweet->user_image }}" class="user-icon">
                                                </a>
                                            </div>

                                            <div class="tweet-container">
                                                <div class="name-area">
                                                    <div>
                                                        <span>{{ $tweet->nickname }}</span>
                                                        <span class="time"> {{ '@'.$tweet->name }}</span>
                                                        <span class="time"> {{$time->convertToDayTimeAgo($tweet->created_at)}} </span>
                                                    </div>
                                                </div>

                                                <div class="body">
                                                    <p>{{ $tweet->body }}</p>
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
                                                <input type="text" name="reply_id" value="{{$tweet->id}}" hidden>
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
                        <!-- <div class="icon retweet-icon js-retweet" data-tweet-id='{{$tweet->id}}' data-retweet-id='{{$tweet->R_id}}'> -->
                        <!-- <div class="icon retweet-icon" data-toggle="popover" data-bs-trigger="focus" tabindex="0"
                            data-bs-content="<div class='js-retweet' data-tweet-id='{{$tweet->id}}' data-retweet-id='{{$tweet->R_id}}'>リツイート</div>
                                            <div data-bs-toggle='modal' data-bs-target='#js-modal-quote-{{$tweet->id}}'>引用</div>"> -->
                        <button type="button" popovertarget="retweet-{{$tweet->id}}" class="icon retweet-icon">
                            <div popover id="retweet-{{$tweet->id}}">
                                @if($tweet->R_id)
                                <div class="js-retweet" data-retweet-id="{{$tweet->R_id}}">リツイートを取り消す</div>
                                @else
                                <div class="js-retweet" data-tweet-id="{{$tweet->id}}">リツイート</div>
                                @endif
                                <div data-bs-toggle="modal" data-bs-target="#js-modal-quote-{{$tweet->id}}">引用</div>
                            </div>
                        
                            @if($tweet->R_id)
                                <i class="fa-solid fa-retweet" style="color:lightgreen;"></i>
                            @else
                                <i class="fa-solid fa-retweet" style="color:black;"></i>
                            @endif
                            <span class="js-retweets-count">{{$tweet->retweet_count}}</span>
                        <!-- </div> -->
                        </button>

                        <div class="modal fade" id="js-modal-quote-{{$tweet->id}}" tabindex="-1" aria-hidden="true">
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
                                                <input type="text" name="retweet_id" value="{{$tweet->id}}" hidden>
                                                <textarea name="body" placeholder="コメントを追加"></textarea>

                                                <div class="tweet quote-tweet mb-3">
                                                    <div class="my-icon quote-icon">
                                                        <img src="{{ $tweet->user_image }}" class="user-icon">
                                                    </div>

                                                    <div class="tweet-container">
                                                        <div class="name-area">
                                                            <div>
                                                                <span>{{ $tweet->nickname }}</span>
                                                                <span class="time"> {{ '@'.$tweet->name }}</span>
                                                                <span class="time"> {{$time->convertToDayTimeAgo($tweet->created_at)}} </span>
                                                            </div>
                                                        </div>

                                                        <div class="body">
                                                            <p>{{ $tweet->body }}</p>
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

                        <!-- いいねボタン -->
                        <div class="like js-like icon like-icon" data-tweet-id="{{$tweet->id}}" data-like-id="{{$tweet->like_id}}">
                                @if($tweet->like_id)
                                    <img src="{{ asset('/images/img/icon-heart-twitterblue.svg') }}">
                                @else
                                    <img src="{{ asset('/images/img/icon-heart.svg') }}">
                                @endif
                            <p class="js-likes-count">{{$tweet->like_count}}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif
</div>