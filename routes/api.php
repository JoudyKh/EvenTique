<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
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
    Route::get('/allUsers', 'allUsers');
    Route::post('/postUser', 'postUser');
});
Route::apiResource('/categories', CategoryController::class)->middleware('locale');
Route::apiResource('/event-type', EventTypeController::class)->middleware('locale');
Route::get('/insertadmin',[AdminController::class ,'admin']);
Route::post('/insertcompany', [CompanyController::class ,'insertcompany'] )->middleware('locale');

Route::middleware(['auth:sanctum'])->group(function() {
    Route::controller(UserController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/logout', 'logout');
        Route::post('/changePass', 'changePass');
        Route::post('/resetName', 'resetName');
        Route::post('/resetImage', 'resetImage');
        Route::get('/loggedUser', 'logged_user');
        Route::post('/changeEmailOTP', 'changeEmailOTP');
        //Route::apiResource('/categories', CategoryController::class)->middleware('locale');
    });
    Route::middleware('auth:company')->prefix('company')->group(function () {
        Route::controller(CompanyController::class)->group(function () {
            Route::post('/companies/sendOTP', 'sendOTP');
            Route::post('/companies/verAuthOTP', 'verAuthOTP');
            Route::post('/companies/resetPass', 'resetPass');
            Route::post('/companies/login', 'login');
            Route::get('/companies/logout', 'logout');
        });
    });
    Route::middleware('auth:company')->prefix('company')->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/admin/sendOTP', 'sendOTP');
            Route::post('/admin/verAuthOTP', 'verAuthOTP');
            Route::post('/admin/resetPass', 'resetPass');
            Route::post('/admin/login', 'login');
            Route::get('/admin/logout', 'logout');
        });
    });


        Route::apiResource('/favorites', FavoriteController::class);

        Route::apiResource('/wallets', WalletController::class);

        Route::apiResource('/reviews', ReviewController::class);

        Route::middleware(['locale'])->group(function () {

            Route::apiResource('/companies', CompanyController::class);

            Route::apiResource('/services', ServiceController::class);
            Route::delete('/services/{service}/delete', [ServiceController::class, 'destroy']);
            Route::post('/services/{service}/update-activation', [ServiceController::class, 'updateActivation']);
        });
    });



