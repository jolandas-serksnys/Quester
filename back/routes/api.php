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
    Route::post('/update', [AuthController::class, 'update']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'games'
], function ($router) {
    Route::get('/hierarchy', [GameController::class, 'getAllHierarchy']);
    Route::get('/hierarchy/{gameId}', [GameController::class, 'getHierarchy']);
    Route::get('/owned', [GameController::class, 'getOwned']);

    Route::get('/', [GameController::class, 'getAll']);
    Route::post('/', [GameController::class, 'create']);
    Route::get('/{gameId}', [GameController::class, 'get']);
    Route::put('/{gameId}', [GameController::class, 'update']);
    Route::delete('/{gameId}', [GameController::class, 'delete']);

    Route::get('/{gameId}/maps', [MapController::class, 'getGameMaps']);
    Route::post('/{gameId}/maps', [MapController::class, 'createGameMap']);
    Route::get('/{gameId}/maps/{mapId}', [MapController::class, 'getGameMap']);
    Route::put('/{gameId}/maps/{mapId}', [MapController::class, 'updateGameMap']);
    Route::delete('/{gameId}/maps/{mapId}', [MapController::class, 'deleteGameMap']);

    Route::get('/{gameId}/maps/{mapId}/quests', [QuestController::class, 'getGameMapQuests']);
    Route::post('/{gameId}/maps/{mapId}/quests', [QuestController::class, 'createGameMapQuest']);
    Route::get('/{gameId}/maps/{mapId}/quests/{questId}', [QuestController::class, 'getGameMapQuest']);
    Route::put('/{gameId}/maps/{mapId}/quests/{questId}', [QuestController::class, 'updateGameMapQuest']);
    Route::delete('/{gameId}/maps/{mapId}/quests/{questId}', [QuestController::class, 'deleteGameMapQuest']);

    Route::get('/{gameId}/maps/{mapId}/quests/{questId}/tasks/completed', [TaskController::class, 'getTasksCompleted']);
    Route::post('/{gameId}/maps/{mapId}/quests/{questId}/tasks/{taskId}/toggle', [TaskController::class, 'toggleTaskCompleted']);

    Route::get('/{gameId}/maps/{mapId}/quests/{questId}/tasks', [TaskController::class, 'getGameMapQuestTasks']);
    Route::post('/{gameId}/maps/{mapId}/quests/{questId}/tasks', [TaskController::class, 'createGameMapQuestTask']);
    Route::get('/{gameId}/maps/{mapId}/quests/{questId}/tasks/{taskId}', [TaskController::class, 'getGameMapQuestTask']);
    Route::put('/{gameId}/maps/{mapId}/quests/{questId}/tasks/{taskId}', [TaskController::class, 'updateGameMapQuestTask']);
    Route::delete('/{gameId}/maps/{mapId}/quests/{questId}/tasks/{taskId}', [TaskController::class, 'deleteGameMapQuestTask']);
});
