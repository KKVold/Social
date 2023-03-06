<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TagController;
use App\Models\Media;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    //auth
    Route::get('/logout', [AuthController::class, 'logout']);

    //friend relation
    Route::post('/friend_relation/send_request', [FriendController::class, 'send_request_friend']);
    Route::post('/friend_relation/accept_friend', [FriendController::class, 'accept_friend']);
    Route::get('/friend_relation/my_recived_requests', [FriendController::class, 'show_my_recived_friend_requests']);
    Route::get('/friend_relation/my_send_requests', [FriendController::class, 'show_my_send_friend_requests']);
    Route::get('/friend_relation/my_friends', [FriendController::class, 'show_my_friends']);
    Route::post('/friend_relation/block_user', [FriendController::class, 'block_user']);
    Route::post('/friend_relation/unblock_user', [FriendController::class, 'unblock_user']);
    Route::get('/friend_relation/my_block_users', [FriendController::class, 'my_block_users']);
    //post routes
    Route::post('/post/new', [PostController::class, 'publish_post']);
    Route::get('/post/{post_id}', [PostController::class, 'show_post']);
    Route::put('/post/{post_id}', [PostController::class, 'update_post']);
    Route::delete('media/{media_id}', [MediaController::class, 'delete']);
    Route::get('/post/tag/{tag_id}', [PostController::class, 'show_tag_posts']);
    Route::get('/post/user/{user_id}', [PostController::class, 'show_user_posts']);
    Route::delete('post/{post_id}', [PostController::class, 'delete']);
    //Tag routes
    Route::get('/tag', [TagController::class, 'get_all_tags']);
    Route::get('/tag/{tag_id}', [TagController::class, 'get_tag']);
    //media routes 
    Route::get('/media/{media_id}', [MediaController::class, 'get_media']);
    Route::get('/media/post/{post_id}', [MediaController::class, 'get_post_media']);
});
