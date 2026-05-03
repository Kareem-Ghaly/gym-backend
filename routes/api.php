<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('ai')->group(function () {
        Route::post('/chat', [AIController::class, 'chat']);
        Route::get('/history', [AIController::class, 'getHistory']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    
    Route::prefix('user')->middleware('role:user')->group(function () {
        Route::get('/free-workout-plans', [UserController::class, 'getFreeWorkoutPlans']);
        Route::get('/free-diet-plans', [UserController::class, 'getFreeDietPlans']);
        Route::post('/upgrade-to-vip', [UserController::class, 'upgradeToVip']);
        Route::get('/coaches', [UserController::class, 'getCoaches']);
        Route::get('/coach-plan', [UserController::class, 'getAcceptedCoachPlan']);
    });


    Route::prefix('coach')->middleware('role:coach')->group(function () {
        Route::get('/vip-requests', [CoachController::class, 'getPendingVipRequests']);
        Route::post('/vip-requests/{vipRequestId}/accept', [CoachController::class, 'acceptVipRequest']);
        Route::post('/vip-requests/{vipRequestId}/decline', [CoachController::class, 'declineVipRequest']);
        Route::put('/plans/{planId}', [CoachController::class, 'updatePlan']);
        Route::post('/plans/{planId}/regenerate', [CoachController::class, 'regeneratePlan']);
    });


    Route::prefix('admin')->middleware('role:admin')->group(function () {

        Route::get('/pending-coaches', [AdminController::class, 'getPendingCoaches']);
        Route::post('/coaches/{coachId}/approve', [AdminController::class, 'approveCoach']);
        Route::post('/coaches/{coachId}/decline', [AdminController::class, 'declineCoach']);


        Route::get('/pending-posts', [AdminController::class, 'getPendingPosts']);
        Route::post('/posts/{postId}/approve', [AdminController::class, 'approvePost']);
        Route::post('/posts/{postId}/decline', [AdminController::class, 'declinePost']);


        Route::post('/free-workout-plans', [AdminController::class, 'createFreeWorkoutPlan']);
        Route::post('/free-diet-plans', [AdminController::class, 'createFreeDietPlan']);
    });


    Route::prefix('forum')->group(function () {
        Route::post('/posts', [ForumController::class, 'createPost']);
        Route::get('/posts', [ForumController::class, 'getPosts']);
        Route::get('/posts/{postId}', [ForumController::class, 'getPost']);
        Route::post('/posts/{postId}/comments', [ForumController::class, 'createComment']);
        Route::post('/posts/{postId}/like', [ForumController::class, 'toggleLike']);
    });
});


