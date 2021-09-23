<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\TaskController;

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
    Route::get('/hierarchy', [GameController::class, 'getAllHierarchy']);
    Route::get('/hierarchy/{gameId}', [GameController::class, 'getHierarchy']);

    Route::get('/', [GameController::class, 'getAll']);
    Route::post('/', [GameController::class, 'create']);
    Route::get('/{gameId}', [GameController::class, 'get']);
    Route::put('/{gameId}', [GameController::class, 'update']);
    Route::delete('/{gameId}', [GameController::class, 'delete']);  

    Route::get('/{gameId}/maps', [GameController::class, 'getGameMaps']);  
    Route::post('/{gameId}/maps', [GameController::class, 'createGameMap']);  
    Route::get('/{gameId}/maps/{mapId}', [GameController::class, 'getGameMap']);  
    Route::put('/{gameId}/maps/{mapId}', [GameController::class, 'updateGameMap']);  
    Route::delete('/{gameId}/maps/{mapId}', [GameController::class, 'deleteGameMap']);  

    Route::get('/{gameId}/maps/{mapId}/quests', [GameController::class, 'getGameMapQuests']);  
    Route::post('/{gameId}/maps/{mapId}/quests', [GameController::class, 'createGameMapQuest']);  
    Route::get('/{gameId}/maps/{mapId}/quests/{questId}', [GameController::class, 'getGameMapQuest']);  
    Route::put('/{gameId}/maps/{mapId}/quests/{questId}', [GameController::class, 'updateGameMapQuest']);  
    Route::delete('/{gameId}/maps/{mapId}/quests/{questId}', [GameController::class, 'deleteGameMapQuest']);  
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'tasks'
], function ($router) {
    Route::get('/', [TaskController::class, 'getAll']);

    Route::post('/', [TaskController::class, 'create']);
    Route::get('/{id}', [TaskController::class, 'get']);
    Route::put('/{id}', [TaskController::class, 'update']);
    Route::delete('/{id}', [TaskController::class, 'delete']); 
});