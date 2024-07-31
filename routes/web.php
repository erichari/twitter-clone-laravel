<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/top', [App\Http\Controllers\TweetController::class, 'tweets'])->name('top');
Auth::routes();

Route::get('/post', [App\Http\Controllers\TweetController::class, 'post'])->name('post');
Route::post('/postTweet', [App\Http\Controllers\TweetController::class, 'postTweet']);
Route::get('/deleteTweet/{id}', [App\Http\Controllers\TweetController::class, 'deleteTweet']);
Route::get('/tweet/{id}', [App\Http\Controllers\TweetController::class, 'tweet']);

Route::get('/search', [App\Http\Controllers\TweetController::class, 'search'])->name('search');
Route::post('/searchTweet', [App\Http\Controllers\TweetController::class, 'searchTweet']);

Route::get('/profile/{id}', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
Route::post('/profile', [App\Http\Controllers\UserController::class, 'editProfile']);
Route::post('/deleteFollow', [App\Http\Controllers\FollowController::class, 'deleteFollow']);
Route::post('/followUser', [App\Http\Controllers\FollowController::class, 'followUser']);
Route::get('/followingUsers/{id}', [App\Http\Controllers\FollowController::class, 'followingUsers']);
Route::get('/followers/{id}', [App\Http\Controllers\FollowController::class, 'followers']);

Route::post('/deleteLike', [App\Http\Controllers\LikeController::class, 'deleteLike']);
Route::post('/likeTweet', [App\Http\Controllers\LikeController::class, 'likeTweet']);

Route::get('/notification', [App\Http\Controllers\NotificationController::class, 'notification'])->name('notification');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
