<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\QuestController;

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
    Route::get('/list-hierarchy', [GameController::class, 'list_hierarchy']);
    
    Route::post('/create', [GameController::class, 'create']);
    Route::get('/read', [GameController::class, 'read']);
    Route::get('/read-hierarchy', [GameController::class, 'read_hierarchy']);
    Route::put('/update', [GameController::class, 'update']);
    Route::delete('/delete', [GameController::class, 'delete']);    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'map'
], function ($router) {
    Route::get('/list', [MapController::class, 'list']);
    Route::get('/list-hierarchy', [MapController::class, 'list_hierarchy']);
    
    Route::post('/create', [MapController::class, 'create']);
    Route::get('/read', [MapController::class, 'read']);
    Route::get('/read-hierarchy', [MapController::class, 'read_hierarchy']);
    Route::put('/update', [MapController::class, 'update']);
    Route::delete('/delete', [MapController::class, 'delete']);    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'quest'
], function ($router) {
    Route::get('/list', [QuestController::class, 'list']);
    
    Route::post('/create', [QuestController::class, 'create']);
    Route::get('/read', [QuestController::class, 'read']);
    //Route::get('/read-hierarchy', [QuestController::class, 'read_hierarchy']);
    Route::put('/update', [QuestController::class, 'update']);
    Route::delete('/delete', [QuestController::class, 'delete']);    
});