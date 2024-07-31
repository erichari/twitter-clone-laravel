$(function(){
    //////////////////////////////////
    //フォロー用JavaScript
    //////////////////////////////////

    $('.js-follow').click(function(){
        const this_obj = $(this);
        const followed_user_id = $(this).data('followed-user-id');
        const follow_id = $(this).data('follow-id');
        let followers_count = Number($('.js-followers-count').text());
        cache: false
        if(follow_id){
            //フォロー取り消し
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/deleteFollow',
                type: 'POST',
                data: {
                    'follow_id': follow_id
                },
                timeout: 10000
            })
                //取り消し成功
                .done(() => {
                    //フォローボタンを白にする
                    this_obj.addClass('btn-reverse');
                    //フォローボタンの文言変更
                    this_obj.text('フォローする');
                    //フォローID削除
                    this_obj.data('follow-id', null);
                    //フォロワーカウントを減らす
                    $('.js-followers-count').text(--followers_count);
                })
                //取り消し失敗
                .fail((data) => {
                    alert('処理中にエラーが発生しました。');
                    console.log(data);
                });
        }else {
            //フォローする
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/followUser',
                type: 'POST',
                data: {
                    'followed_user_id': followed_user_id
                },
                timeout: 10000
            })
                //フォロー成功
                .done((data) => {
                    //フォローボタンを青にする
                    this_obj.removeClass('btn-reverse');
                    //フォローボタンの文言変更
                    this_obj.text('フォローを外す');
                    //フォローID付与
                    this_obj.data('follow-id', data['follow_id']);
                    //フォロワーカウントを増やす
                    $('.js-followers-count').text(++followers_count);
                })
                //フォロー失敗
                .fail((data) => {
                    alert('処理中にエラーが発生しました。');
                    console.log(data);
                });
        }
    })



    //////////////////////////////////
    //いいね用JavaScript
    //////////////////////////////////
    $('.js-like').click(function(){
        const this_obj = $(this);
        const tweet_id = $(this).data('tweet-id');
        const like_id = $(this).data('like-id');
        let likes_count = Number(this_obj.find('.js-likes-count').text());
        cache: false
        if(like_id){
            //いいね取り消し
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/deleteLike',
                type: 'POST',
                data: {
                    'like_id': like_id
                },
                timeout: 10000
            })
                //取り消し成功
                .done(() => {
                    //いいねボタンの画像変更
                    this_obj.find('img').attr('src', "/images/img/icon-heart.svg");
                    //ライクID削除
                    this_obj.data('like-id', null);
                    //いいねカウントを減らす
                    this_obj.find('.js-likes-count').text(--likes_count);
                })
                //取り消し失敗
                .fail((data) => {
                    alert('処理中にエラーが発生しました。');
                    console.log(data);
                });
        }else {
            //いいねする
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/likeTweet',
                type: 'POST',
                data: {
                    'tweet_id': tweet_id
                },
                timeout: 10000
            })
                //いいね成功
                .done((data) => {
                    //いいねボタンの画像変更
                    this_obj.find('img').attr('src', "/images/img/icon-heart-twitterblue.svg");
                    //ライクID付与
                    this_obj.data('like-id', data['like_id']);
                    //いいねカウントを増やす
                    this_obj.find('.js-likes-count').text(++likes_count);
                })
                //いいね失敗
                .fail((data) => {
                    alert('処理中にエラーが発生しました。');
                    console.log(data);
                });
        }
    })

    //////////////////////////////////
    //リツイート用JavaScript
    //////////////////////////////////
    $('.js-retweet').click(function(){
        const this_obj = $(this);
        const tweet_id = $(this).data('tweet-id');
        const retweet_id = $(this).data('retweet-id');
        const grandparent = $(this).closest('.retweet-icon');
        let retweets_count = Number(grandparent.find('.js-retweets-count').text());
        
        cache: false
        if(retweet_id){
            //リツイート取り消し
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/deleteTweet/'+retweet_id,
                type: 'GET',
                timeout: 10000
            })
                .done(() => {
                    console.log(retweet_id);
                    grandparent.find('i').attr('style', 'color:black;');
                    this_obj.data('retweet-id', null);
                    grandparent.find('.js-retweets-count').text(--retweets_count);
                })
                .fail((data) => {
                    alert('処理中にエラーが発生しました。');
                    console.log(data);
                });
        }else {
            //リツイートする
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/postTweet',
                type: 'POST',
                data: {
                    'tweet_id': tweet_id
                },
                timeout: 10000
            })
                .done((data) => {
                    grandparent.find('i').attr('style', 'color:lightgreen;');
                    this_obj.data('retweet-id', data['retweet_id']);
                    grandparent.find('.js-retweets-count').text(++retweets_count);
                })
                .fail((data) => {
                    alert('処理中にエラーが発生しました。');
                    console.log(data);
                });
        }
    })

    //////////////////////////////////
    //ポップオーバー用JavaScript
    //////////////////////////////////

    $('[data-toggle="popover"]').popover({html:true})

    const popover = new bootstrap.Popover('.popover-dismiss', {
        trigger: 'focus'
    })
});