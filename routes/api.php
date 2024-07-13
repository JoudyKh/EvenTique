<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/admin',[AdminController::class ,'admin']);
Route::controller(UserController::class)->group(function() {
    Route::post('/register', 'register');
    Route::post('/verRegistereOTP', 'verRegistereOTP');
    Route::post('/sendOTP', 'sendOTP');
    Route::post('/verAuthOTP' , 'verAuthOTP');
    Route::post('resetPass','resetPass');
});

Route::middleware(['auth:sanctum'])->group(function() {
    Route::controller(UserController::class)->group(function() {
        Route::post('/login','login');
        Route::post('/logout','logout');
        Route::post('/changePass','changePass');
        Route::get('/loggedUser','logged_user');
        Route::post('/changeEmailOTP' , 'changeEmailOTP');
    });
    Route::controller(EventController::class)->group(function() {
        Route::post('/insertEventType','insertEventType');
        Route::post('/deleteEventType','deleteEventType');
        Route::get('/showAllEventType','showAllEventType');
    });
    Route::controller(FavoriteController::class)->group(function() {
        Route::post('/addFavorite','addFavorite');
        Route::post('/deleteFavorite','deleteFavorite');
        Route::get('/showfavorite','showfavorites');
    });
    Route::controller(CategoryController::class)->group(function() {
        Route::post('/addCategory','addCategory');
        Route::post('/deleteCategory','deleteCategory');
        Route::post('/catServices','catServices');
        Route::get('/showCategory','showCategory');
    });
    Route::controller(ServiceController::class)->group(function() {
        Route::post('/addService','addService');
        Route::post('/deleteService','deleteService');
        Route::get('/showService/{id}','showService');
    });
});

