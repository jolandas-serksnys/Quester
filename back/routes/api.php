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
    'prefix' => 'games'
], function ($router) {
    Route::get('/', [GameController::class, 'getAll']);
    Route::get('/hierarchy', [GameController::class, 'getAllHierarchy']);
    Route::get('/hierarchy/{id}', [GameController::class, 'getHierarchy']);

    Route::post('/', [GameController::class, 'create']);
    Route::get('/{id}', [GameController::class, 'get']);
    Route::put('/{id}', [GameController::class, 'update']);
    Route::delete('/{id}', [GameController::class, 'delete']);    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'maps'
], function ($router) {
    Route::get('/', [MapController::class, 'getAll']);
    Route::get('/hierarchy', [MapController::class, 'getAllHierarchy']);
    Route::get('/hierarchy/{id}', [MapController::class, 'getHierarchy']);

    Route::post('/', [MapController::class, 'create']);
    Route::get('/{id}', [MapController::class, 'get']);
    Route::put('/{id}', [MapController::class, 'update']);
    Route::delete('/{id}', [MapController::class, 'delete']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'quests'
], function ($router) {
    Route::get('/', [QuestController::class, 'getAll']);
    Route::get('/hierarchy', [QuestController::class, 'getAllHierarchy']);
    Route::get('/hierarchy/{id}', [QuestController::class, 'getHierarchy']);

    Route::post('/', [QuestController::class, 'create']);
    Route::get('/{id}', [QuestController::class, 'get']);
    Route::put('/{id}', [QuestController::class, 'update']);
    Route::delete('/{id}', [QuestController::class, 'delete']); 
});