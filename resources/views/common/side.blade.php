
<div class="side">
    <div class="side-inner">
        <ul class="nav flex-column">
            <li class="nav-item"><a href="/top"><img src="{{asset('/images/img/logo-twitterblue.svg')}}" class="icon logo"></a></li>
            <li class="nav-item"><a href="/top"><img src="{{asset('/images/img/icon-home.svg')}}" class="icon menu-icon"></a></li>
            <li class="nav-item"><a href="/search"><img src="{{asset('/images/img/icon-search.svg')}}" class="icon menu-icon"></a></li>
            <li class="nav-item"><a href="/notification"><img src="{{asset('/images/img/icon-notification.svg')}}" class="icon menu-icon"></a></li>
            <li class="nav-item"><a href="/profile/{{$login_user->id}}"><img src="{{asset('/images/img/icon-profile.svg')}}" class="icon menu-icon"></a></li>
            <li class="nav-item"><a href="/post"><img src="{{asset('/images/img/icon-post-tweet-twitterblue.svg')}}" class="icon tweet-icon"></a></li>
            <li class="nav-item my-icon"><img src="{{ $login_user->user_image }}" class="user-icon" data-toggle="popover" data-bs-trigger="focus" tabindex="0"
                data-bs-content="<a href='/profile/{{$login_user->id}}'>プロフィール</a><br>
                                <a href='{{ route('logout') }}'onclick='event.preventDefault();
                                            document.getElementById('logout-form').submit();'>
                                    {{ __('Logout') }}
                                </a>">
            </li>
        </ul>  
    </div> 
</div>