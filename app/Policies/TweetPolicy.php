<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tweet;

class TweetPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function deleteTweet(User $user, Tweet $tweet){
        return $user->id === $tweet->user_id;
    }
}
