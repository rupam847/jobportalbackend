<?php

use App\Http\Controllers\ApiCompanyController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [ApiController::class, 'categories']);
Route::post('/get-jobs', [ApiController::class, 'getJobs']);
Route::get('/getsinglejob/{id}', [ApiController::class, 'getsinglejob']);
Route::post('/apply-job', [ApiController::class, 'applyJob']);
Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', function () {
        return auth()->user();
    });
    Route::get('/dashboard', [ApiController::class, 'dashboard']);
    Route::get('/applications', [ApiController::class, 'applications']);
    Route::delete('/deleteapplication/{id}', [ApiController::class, 'deleteApplication']);
    Route::get('/user-applications', [ApiController::class, 'userApplications']);
    Route::post('/profile-update', [ApiController::class, 'profileUpdate']);
    Route::post('/password-update', [ApiController::class, 'passwordUpdate']);
    Route::post('/apply-job', [ApiController::class, 'applyJob']);
    Route::resource('postjobs', ApiCompanyController::class);
});