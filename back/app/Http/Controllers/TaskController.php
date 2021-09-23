<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Validator;

use App\Models\Task;
use App\Models\Quest;
use App\Models\Map;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return response()->json(Task::all(), 200);
    }

    public function getGameMapQuestTasks($gameId, $mapId, $questId)
    {
        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $task = Task::where("tasks.quest_id", $questId)->get();
        
        return response()->json($task, 200);
    }

    public function createGameMapQuestTask(Request $request, $gameId, $mapId, $questId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to create tasks.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create tasks.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'This user has no rights to create tasks for the specified game.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $validated_data['quest_id'] = $quest->id;

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new task has been added.',
            'task' => task::create($validated_data)
        ), 201); // Created
    }

    public function getGameMapQuestTask($gameId, $mapId, $questId, $taskId)
    {
        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $task = Task::where("tasks.quest_id", $questId)->find($taskId);

        if (!$task) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid task ID.'
            ), 422);
        }
        
        return response()->json($task, 200);
    }

    public function updateGameMapQuestTask(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to update tasks.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to update tasks.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'This user has no rights to update tasks for the specified game.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $task = Task::where('tasks.quest_id', $quest->id)->find($taskId);

        if (!$task) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid task ID.'
            ), 422);
        }

        $task->title = $validated_data['title'];
        $task->description = empty($validated_data['description']) ? null : $validated_data['description'];
        $task->image_url = empty($validated_data['image_url']) ? null : $validated_data['image_url'];

        $task->save();

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified task has been successfully updated.',
            'task' => $task
        ), 200);
    }

    public function deleteGameMapQuestTask($gameId, $mapId, $questId, $taskId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to update tasks.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to update tasks.'
            ), 403); // Forbidden
        }

        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'This user has no rights to update tasks for the specified game.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $task = Task::where('tasks.quest_id', $quest->id)->find($taskId);

        if (!$task) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid task ID.'
            ), 422);
        }

        Task::destroy($task->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified task has been successfully deleted.',
            'task' => $task
        ), 200);
    }
}
