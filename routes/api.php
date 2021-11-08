<?php

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
use App\Http\Controllers\General\ConversatorController;
use App\Http\Controllers\General\NotificationController;
use App\Http\Controllers\General\FollowSuggestionController;
use App\Http\Controllers\General\ReviewController;
use App\Http\Controllers\General\StoryController;
use App\Http\Controllers\General\SearchController;
use App\Http\Controllers\General\SettingController;

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
Route::post('/auth/settings/create', [SettingController::class, 'create']);
Route::post('/auth/recovery', [ForgotPasswordController::class, 'store']);
Route::post('/auth/reset-password/', [ResetPasswordController::class, 'store']);
Route::get('/auth/reviews/index', [ReviewController::class, 'index']);
/*
|--------------------------------------------------------------------------
| Authentication Required Routes
|--------------------------------------------------------------------------
|
*/
Route::get('/auth/profile/{profileId}/edit', [ProfileController::class, 'edit'])->middleware('auth:sanctum');
Route::get('/auth/profile/{profileId}/about', [ProfileController::class, 'showAbout'])->middleware('auth:sanctum');
Route::patch('/auth/profile/{profileId}/update', [ProfileController::class, 'update'])->middleware('auth:sanctum');
Route::get('/auth/profile/{id}', [ProfileController::class, 'show'])->middleware('auth:sanctum');
Route::post('/auth/profile', [ProfileController::class, 'store'])->middleware('auth:sanctum');
Route::post('/auth/logout', [LogoutController::class, 'store'])->middleware('auth:sanctum');
Route::patch('/auth/stats/follow/{userId}/update', [StatsController::class, 'updateFollowStats'])->middleware('auth:sanctum');
Route::patch('/auth/stats/unfollow/{userId}/update', [StatsController::class, 'updateUnfollowStats'])->middleware('auth:sanctum');
Route::get('/auth/network/following/show/{userId}', [NetworkController::class, 'showFollowing'])->middleware('auth:sanctum');
Route::get('/auth/network/followers/show/{userId}', [NetworkController::class, 'showFollowers'])->middleware('auth:sanctum');
Route::patch('/auth/user/status/{userId}/update', [UserController::class, 'updateUserStatus'])->middleware('auth:sanctum');
Route::patch('/auth/user/{userId}/update', [UserController::class, 'updateColumn'])->middleware('auth:sanctum');
Route::post('/auth/posts/store', [PostController::class, 'store'])->middleware('auth:sanctum');
Route::get('/auth/posts', [PostController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/auth/posts/{id}/delete', [PostController::class, 'delete'])->middleware('auth:sanctum');
Route::post('/auth/post-likes/store', [PostLikeController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/auth/post-likes/{id}/delete', [PostLikeController::class, 'delete'])->middleware('auth:sanctum');
Route::post('/auth/flagged-posts/store', [FlaggedPostController::class, 'store'])->middleware('auth:sanctum');
Route::post('/auth/comments/store', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/auth/comments/{commentID}/delete', [CommentController::class, 'delete'])->middleware('auth:sanctum');
Route::get('/auth/posts/{postId}/comments/show', [CommentController::class, 'show'])->middleware('auth:sanctum');
Route::post('/auth/comment-likes/store', [CommentLikeController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/auth/comment-likes/{commentLikeId}/delete', [CommentLikeController::class, 'delete'])->middleware('auth:sanctum');
Route::post('/auth/comments/reply/store', [CommentController::class, 'replyStore'])->middleware('auth:sanctum');
Route::get('/auth/posts/{postId}/comments/reply/show', [CommentController::class, 'showReply'])->middleware('auth:sanctum');
Route::delete('/auth/comments/reply/{commentID}/delete', [CommentController::class, 'deleteReply'])->middleware('auth:sanctum');
Route::post('/auth/follow-requests/store', [FollowRequestController::class, 'store'])->middleware('auth:sanctum');
Route::get('/auth/follow-requests/index', [FollowRequestController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/auth/follow-requests/{requestId}/delete', [FollowRequestController::class, 'delete'])->middleware('auth:sanctum');
Route::get('/auth/messages/{recipientId}/show', [ConversatorController::class, 'showChatMessages'])->middleware('auth:sanctum');
Route::post('/auth/messages', [ConversatorController::class, 'store'])->middleware('auth:sanctum');
Route::get('/auth/conversator/{userId}/show', [ConversatorController::class, 'show'])->middleware('auth:sanctum');
Route::get('/auth/user/notifications/messages/{userId}/show', [NotificationController::class, 'showMessageNotifications'])->middleware('auth:sanctum');
Route::patch('/auth/user/notifications/messages/{userId}/update', [NotificationController::class, 'updateMessageNotifications'])->middleware('auth:sanctum');
Route::delete('/auth/user/notifications/messages/{userId}/delete', [NotificationController::class, 'deleteMessageNotifications'])->middleware('auth:sanctum');
Route::get('/auth/user/notifications/alerts/{userId}/show', [NotificationController::class, 'showNotificationAlerts'])->middleware('auth:sanctum');
Route::get('/auth/user/notifications/interactions/{userId}/show', [NotificationController::class, 'showInteractionNotifications'])->middleware('auth:sanctum');
Route::delete('/auth/user/notifications/interactions/{notificationId}/delete', [NotificationController::class, 'deleteInteractionNotification'])->middleware('auth:sanctum');
Route::get('/auth/newsfeed/{slug}/show', [NewsFeedController::class, 'show'])->middleware('auth:sanctum');
Route::get('/auth/follow-suggestions/{userId}/show', [FollowSuggestionController::class, 'show'])->middleware('auth:sanctum');
Route::patch('/auth/follow-suggestions/{suggestionId}/update', [FollowSuggestionController::class, 'update'])->middleware('auth:sanctum');
Route::post('/auth/reviews/create', [ReviewController::class, 'store'])->middleware('auth:sanctum');
Route::get('/auth/reviews/{userId}/show', [ReviewController::class, 'show'])->middleware('auth:sanctum');
Route::patch('/auth/reviews/{reviewId}/update', [ReviewController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/auth/reviews/{reviewId}/delete', [ReviewController::class, 'delete'])->middleware('auth:sanctum');
Route::post('/auth/stories/create', [StoryController::class, 'store'])->middleware('auth:sanctum');
Route::get('/auth/stories/{userId}/show', [StoryController::class, 'show'])->middleware('auth:sanctum');
Route::get('/auth/stories/{userId}/count/show', [StoryController::class, 'count'])->middleware('auth:sanctum');
Route::get('/auth/stories/index', [StoryController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/auth/stories/{storyId}/delete', [StoryController::class, 'delete'])->middleware('auth:sanctum');
Route::post('/auth/searches/search', [SearchController::class, 'search'])->middleware('auth:sanctum');
Route::post('/auth/searches/store', [SearchController::class, 'store'])->middleware('auth:sanctum');
Route::get('/auth/searches/{userId}/show', [SearchController::class, 'show'])->middleware('auth:sanctum');
Route::delete('/auth/searches/{searchId}/delete', [SearchController::class, 'delete'])->middleware('auth:sanctum');
Route::post('/auth/settings/block/search', [SettingController::class, 'search'])->middleware('auth:sanctum');
Route::post('/auth/settings/block/store', [SettingController::class, 'store'])->middleware('auth:sanctum');
Route::get('/auth/settings/block/{userId}/show', [SettingController::class, 'show'])->middleware('auth:sanctum');
Route::patch('/auth/settings/block/{privacyId}/update', [SettingController::class, 'updateBlockedUser'])->middleware('auth:sanctum');
Route::delete('/auth/settings/block/{privacyId}/delete', [SettingController::class, 'deleteBlockedUser'])->middleware('auth:sanctum');
Route::patch('/auth/settings/remember-me/{settingId}/update', [SettingController::class, 'updateRememberMe'])->middleware('auth:sanctum');
Route::get('/auth/settings/security/{settingId}/show', [SettingController::class, 'retrieveSecuritySettings'])->middleware('auth:sanctum');
Route::post('/auth/settings/remember-me', [SettingController::class, 'validateRememberMe'])->middleware('auth:sanctum');
Route::patch('/auth/settings/password/{settingId}/update', [SettingController::class, 'updatePassword'])->middleware('auth:sanctum');
Route::delete('/auth/settings/account/{settingId}/delete', [SettingController::class, 'deleteAccount'])->middleware('auth:sanctum');
