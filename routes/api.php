<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberOtherController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyInforController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\PricingPlanController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me'])->middleware(['auth:sanctum']);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
});

Route::middleware(['auth:sanctum'])->group(function(){
    // User APIs
    Route::post('/user', [UserController::class, 'store']);

    // Blog APIs;
    Route::post('/blog', [BlogController::class, 'store']);
    Route::put('/blog/{id}', [BlogController::class, 'update']);
    Route::delete('/blog/{id}', [BlogController::class, 'destroy']);

     // Company info
    Route::post('/company-info', [CompanyInforController::class, 'store']);
    Route::get('/company-info/{id}', [CompanyInforController::class, 'show']);
    Route::put('/company-info/{id}', [CompanyInforController::class, 'update']);
    Route::delete('/company-info/{id}', [CompanyInforController::class, 'destroy']);

    // Slide APIs
    Route::post('/slide', [SlideController::class, 'store']);
    Route::get('/slide/{id}', [SlideController::class, 'show']);
    Route::put('/slide/{id}', [SlideController::class, 'update']);
    Route::delete('/slide/{id}', [SlideController::class, 'destroy']);

    //About APIs
    Route::post('/about', [AboutController::class, 'store']);
    Route::get('/about/{id}', [AboutController::class, 'show']);
    Route::put('/about/{id}', [AboutController::class, 'update']);
    Route::delete('/about/{id}', [AboutController::class, 'destroy']);

    //Member APIs
    Route::post('/member', [MemberController::class, 'store']);
    Route::get('/member/{id}', [MemberController::class, 'show']);
    Route::put('/member/{id}', [MemberController::class, 'update']);
    Route::delete('/member/{id}', [MemberController::class, 'destroy']);

    //Member_Other APIs
    Route::post('/member_other', [MemberOtherController::class, 'store']);
    Route::get('/member_other/{id}', [MemberOtherController::class, 'show']);
    Route::put('/member_other/{id}', [MemberOtherController::class, 'update']);
    Route::delete('/member_other/{id}', [MemberOtherController::class, 'destroy']);

    //Job APIs
    Route::post('jobs', [JobController::class, 'store']); // Admin thêm công việc
    Route::put('jobs/{id}', [JobController::class, 'update']); // Admin cập nhật công việc
    Route::delete('jobs/{id}', [JobController::class, 'destroy']); // Admin xóa công việc

    // Application APIs
    Route::get('applications', [ApplicationController::class, 'index']); // Admin xem danh sách ứng viên
    Route::get('applications/{id}', [ApplicationController::class, 'show']); // Admin xem chi tiết ứng viên
    Route::get('applications/{id}/download-cv', [ApplicationController::class, 'downloadCV']); // Admin tải CV

    // Achievement APIs
    Route::post('/achievements', [AchievementController::class, 'store']);
    Route::put('/achievements/{id}', [AchievementController::class, 'update']);
    Route::delete('/achievements/{id}', [AchievementController::class, 'destroy']);

    // Pricing Plan APIsPo
    Route::prefix('pricing-plans')->group(function () {
        Route::post('/', [PricingPlanController::class, 'store']);
        Route::put('/{id}', [PricingPlanController::class, 'update']);
        Route::delete('/{id}', [PricingPlanController::class, 'destroy']);
    });
});
    //Slider
    Route::get('/slide', [SlideController::class, 'index']);

    //About
    Route::get('/about', [AboutController::class, 'index']);

    //Member
    Route::get('/member', [MemberController::class, 'index']);

    //Member other
    Route::get('/member_other', [MemberOtherController::class, 'index']);

    //Blog
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/{id}', [BlogController::class, 'show']);

    //Company info
    Route::get('/company-info', [CompanyInforController::class, 'index']);

    //About
    Route::get('/about', [AboutController::class, 'index']);

    //Contact
    Route::post('/contact', [ContactController::class, 'store']);

    //Job
    Route::get('jobs', [JobController::class, 'index']); // Danh sách công việc
    Route::get('jobs/{id}', [JobController::class, 'show']); // Chi tiết công việc

    //Application
    Route::post('applications', [ApplicationController::class, 'store']); // Khách gửi đơn ứng tuyển

    //Achievement
    Route::get('/achievements', [AchievementController::class, 'index']);
    Route::get('/achievements/{id}', [AchievementController::class, 'show']);
    
    // Pricing Plan
    Route::get('/pricing-plans', [PricingPlanController::class, 'index']);
    Route::get('/pricing-plans/{id}', [PricingPlanController::class, 'show']);