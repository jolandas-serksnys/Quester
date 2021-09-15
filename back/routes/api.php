<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MapController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'game'
], function ($router) {
    Route::get('/list', [GameController::class, 'list']);
    
    Route::post('/create', [GameController::class, 'create']);
    Route::get('/read', [GameController::class, 'read']);
    Route::put('/update', [GameController::class, 'update']);
    Route::delete('/delete', [GameController::class, 'delete']);    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'map'
], function ($router) {
    Route::get('/list', [MapController::class, 'list']);
    
    Route::post('/create', [MapController::class, 'create']);
    Route::get('/read', [MapController::class, 'read']);
    Route::put('/update', [MapController::class, 'update']);
    Route::delete('/delete', [MapController::class, 'delete']);    
});