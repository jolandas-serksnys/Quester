<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Validator;

use App\Models\Task;
use App\Models\Game;
use App\Models\TaskTrackerEntry;

class TaskController extends Controller
{
    public function getValidationArray() {
        return [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
        ];
    }
    
    public function getAll()
    {
        return response()->json(Task::all(), 200);
    }

    public function getGameMapQuestTasks(Request $request, $gameId, $mapId, $questId)
    {
        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        if($checkExistsParents = $this->checkExistsParents($request))
            return $checkExistsParents;
        
        return response()->json(Task::where("tasks.quest_id", $questId)->get(), 200);
    }

    public function createGameMapQuestTask(Request $request, $gameId, $mapId, $questId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        if($checkExistsParents = $this->checkExistsParents($request))
            return $checkExistsParents;
            
    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json(task::create($validator->validated()), 201); // Created
    }

    public function getGameMapQuestTask(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;
        
        return response()->json(Task::where('tasks.quest_id', $questId)->find($taskId), 200);
    }

    public function updateGameMapQuestTask(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;
        
    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        $task = Task::find($taskId);
        $task->update($validator->validated());

        return response()->json($task, 200);
    }

    public function deleteGameMapQuestTask(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        $task = Task::find($taskId);
        Task::destroy($task->id);
        
        return response()->json($task, 200);
    }

    public function toggleTaskCompleted(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;
        
        $userId = auth()->user()->id;
        $toggleVal = false;

        $taskTrackerEntry = TaskTrackerEntry::where('user_id', $userId)->where('task_id', $taskId)->first();

        if($taskTrackerEntry) {
            TaskTrackerEntry::destroy($taskTrackerEntry->id);
        } else {
            $toggleVal = true;
            TaskTrackerEntry::create([
                'user_id' => $userId,
                'task_id' => $taskId
            ]);
        }
        
        return response()->json(array(
            'task_id' => $taskId,
            'is_completed' => $toggleVal
        ), 200);
    }

    public function getTasksCompleted(Request $request, $gameId, $mapId, $questId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        if($checkExistsParents = $this->checkExistsParents($request))
            return $checkExistsParents;
        
        $taskTrackerEntries = TaskTrackerEntry::where('user_id', auth()->user()->id)->get();
        
        return response()->json($taskTrackerEntries, 200);
    }

    public function checkExistsConcrete(Request $request) {
        $validator = Validator::make($request->all(), [
            'game_id' => 'numeric|required|exists:games,id',
            'quest_id' => 'exists:quests,id,map_id,' . $request['map_id'],
            'map_id' => 'exists:maps,id,game_id,' . $request['game_id'],
            'task_id' => 'exists:tasks,id,quest_id,' . $request['quest_id']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        return false;
    }

    public function checkUser() {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'message' => 'Must be logged in to complete this action.'
            ), 401); // Unauthorized
        }

        return false;
    }

    public function checkAdmin() {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'message' => 'Must be logged in to create, update or delete games.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'message' => 'User group has no rights to create, update or delete games.'
            ), 403); // Forbidden
        }

        return false;
    }

    public function checkOwner(Request $request) {
        $game = Game::find($request['game_id']);

        if ($game->owner_id != auth()->user()->id) {
            return response()->json(array(
                'message' => 'User has no rights to delete specified games.'
            ), 403); // Forbidden
        }

        return false;
    }
}
