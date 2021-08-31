<?php

use App\Helpers\Messenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\General\NewsFeedController;
use App\Http\Controllers\Auth\refreshTokenController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\General\CommentController;
use App\Http\Controllers\General\ProfileController;
use App\Http\Controllers\general\StatsController;
use App\Http\Controllers\General\NetworkController;
use App\Http\Controllers\General\UserController;
use App\Http\Controllers\General\PostController;
use App\Http\Controllers\General\PostLikeController;
use App\Http\Controllers\General\FlaggedPostController;
use App\Http\Controllers\General\CommentLikeController;
use App\Http\Controllers\General\FollowRequestController;
use App\Http\Controllers\General\MessengerController;
use App\Http\Controllers\General\NotificationController;
use App\Http\Controllers\General\FollowSuggestionController;
use App\Http\Controllers\General\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
/*
/*
|--------------------------------------------------------------------------
| Unauthenticated Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/auth/users/count', [UserController::class, 'count']);
Route::post('/auth/register', [RegisterController::class, 'store']);
Route::post('/auth/login', [LoginController::class, 'store']);
Route::post('/auth/token/refresh', [refreshTokenController::class, 'store']);
Route::post('/auth/recovery', [ForgotPasswordController::class, 'store']);
Route::post('/auth/reset-password/', [ResetPasswordController::class, 'store']);
Route::get('/auth/reviews/index', [ReviewController::class, 'index']);
/*
|--------------------------------------------------------------------------
| Authentication Required Routes
|--------------------------------------------------------------------------
|
*/
Route::get('/auth/profile/{profileId}/edit', [ProfileController::class, 'edit'])->middleware('auth:api');
Route::get('/auth/profile/{profileId}/about', [ProfileController::class, 'showAbout'])->middleware('auth:api');
Route::patch('/auth/profile/{profileId}/update', [ProfileController::class, 'update'])->middleware('auth:api');
Route::get('/auth/profile/{id}', [ProfileController::class, 'show'])->middleware('auth:api');
Route::post('/auth/profile', [ProfileController::class, 'store'])->middleware('auth:api');
Route::post('/auth/logout', [LogoutController::class, 'store'])->middleware('auth:api');
Route::patch('/auth/stats/follow/{userId}/update', [StatsController::class, 'updateFollowStats'])->middleware('auth:api');
Route::patch('/auth/stats/unfollow/{userId}/update', [StatsController::class, 'updateUnfollowStats'])->middleware('auth:api');
Route::get('/auth/network/following/show/{userId}', [NetworkController::class, 'showFollowing'])->middleware('auth:api');
Route::get('/auth/network/followers/show/{userId}', [NetworkController::class, 'showFollowers'])->middleware('auth:api');
Route::patch('/auth/user/status/{userId}/update', [UserController::class, 'updateUserStatus'])->middleware('auth:api');
Route::patch('/auth/user/{userId}/update', [UserController::class, 'updateColumn'])->middleware('auth:api');
Route::post('/auth/posts/store', [PostController::class, 'store'])->middleware('auth:api');
Route::get('/auth/posts', [PostController::class, 'index'])->middleware('auth:api');
Route::delete('/auth/posts/{id}/delete', [PostController::class, 'delete'])->middleware('auth:api');
Route::post('/auth/post-likes/store', [PostLikeController::class, 'store'])->middleware('auth:api');
Route::delete('/auth/post-likes/{id}/delete', [PostLikeController::class, 'delete'])->middleware('auth:api');
Route::post('/auth/flagged-posts/store', [FlaggedPostController::class, 'store'])->middleware('auth:api');
Route::post('/auth/comments/store', [CommentController::class, 'store'])->middleware('auth:api');
Route::delete('/auth/comments/{commentID}/delete', [CommentController::class, 'delete'])->middleware('auth:api');
Route::get('/auth/posts/{postId}/comments/show', [CommentController::class, 'show'])->middleware('auth:api');
Route::post('/auth/comment-likes/store', [CommentLikeController::class, 'store'])->middleware('auth:api');
Route::delete('/auth/comment-likes/{commentLikeId}/delete', [CommentLikeController::class, 'delete'])->middleware('auth:api');
Route::post('/auth/comments/reply/store', [CommentController::class, 'replyStore'])->middleware('auth:api');
Route::get('/auth/posts/{postId}/comments/reply/show', [CommentController::class, 'showReply'])->middleware('auth:api');
Route::delete('/auth/comments/reply/{commentID}/delete', [CommentController::class, 'deleteReply'])->middleware('auth:api');
Route::post('/auth/follow-requests/store', [FollowRequestController::class, 'store'])->middleware('auth:api');
Route::get('/auth/follow-requests/index', [FollowRequestController::class, 'index'])->middleware('auth:api');
Route::delete('/auth/follow-requests/{requestId}/delete', [FollowRequestController::class, 'delete'])->middleware('auth:api');
Route::get('/auth/messages/{recipientId}/show', [MessengerController::class, 'showChatMessages'])->middleware('auth:api');
Route::post('/auth/messages', [MessengerController::class, 'store'])->middleware('auth:api');
Route::get('/auth/messenger/{userId}/show', [MessengerController::class, 'show'])->middleware('auth:api');
Route::get('/auth/user/notifications/messages/{userId}/show', [NotificationController::class, 'showMessageNotifications'])->middleware('auth:api');
Route::patch('/auth/user/notifications/messages/{userId}/update', [NotificationController::class, 'updateMessageNotifications'])->middleware('auth:api');
Route::delete('/auth/user/notifications/messages/{userId}/delete', [NotificationController::class, 'deleteMessageNotifications'])->middleware('auth:api');
Route::get('/auth/user/notifications/alerts/{userId}/show', [NotificationController::class, 'showNotificationAlerts'])->middleware('auth:api');
Route::get('/auth/user/notifications/interactions/{userId}/show', [NotificationController::class, 'showInteractionNotifications'])->middleware('auth:api');
Route::delete('/auth/user/notifications/interactions/{notificationId}/delete', [NotificationController::class, 'deleteInteractionNotification'])->middleware('auth:api');
Route::get('/auth/newsfeed/{slug}/show', [NewsFeedController::class, 'show'])->middleware('auth:api');
Route::get('/auth/follow-suggestions/{userId}/show', [FollowSuggestionController::class, 'show'])->middleware('auth:api');
Route::patch('/auth/follow-suggestions/{suggestionId}/update', [FollowSuggestionController::class, 'update'])->middleware('auth:api');
Route::post('/auth/reviews/create', [ReviewController::class, 'store'])->middleware('auth:api');
Route::get('/auth/reviews/{userId}/show', [ReviewController::class, 'show'])->middleware('auth:api');
