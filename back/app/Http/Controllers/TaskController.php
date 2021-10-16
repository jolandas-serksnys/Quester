<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Validator;

use App\Models\Task;
use App\Models\Game;
use App\Models\TaskTrackerEntry;

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

    public function getGameMapQuestTasks(Request $request, $gameId, $mapId, $questId)
    {
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'quest_id' => 'exists:quests,id,map_id,' . $mapId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        return response()->json(Task::where("tasks.quest_id", $questId)->get(), 200);
    }

    public function createGameMapQuestTask(Request $request, $gameId, $mapId, $questId)
    {
        if($userCheck = $this->checkAdmin())
            return $userCheck;

        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new task has been added.',
            'task' => task::create($validator->validated())
        ), 201); // Created
    }

    public function getGameMapQuestTask(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'task_id' => 'exists:tasks,id,quest_id,' . $questId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        return response()->json(Task::where('tasks.quest_id', $questId)->find($taskId), 200);
    }

    public function updateGameMapQuestTask(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        if($userCheck = $this->checkAdmin())
            return $userCheck;

        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'task_id' => 'exists:tasks,id,quest_id,' . $questId,

            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (Game::find($gameId)->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'This user has no rights to update tasks for the specified game.'
            ), 403);
        }

        $task = Task::find($taskId);
        $task->update($validator->validated());

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified task has been successfully updated.',
            'task' => $task
        ), 200);
    }

    public function deleteGameMapQuestTask(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        if($userCheck = $this->checkAdmin())
            return $userCheck;

        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
        $validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'task_id' => 'exists:tasks,id,quest_id,' . $questId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (Game::find($gameId)->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'This user has no rights to delete tasks for the specified game.'
            ), 403);
        }

        $task = Task::find($taskId);
        Task::destroy($task->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified task has been successfully deleted.',
            'task' => $task
        ), 200);
    }

    public function toggleTaskCompleted(Request $request, $gameId, $mapId, $questId, $taskId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $request['task_id'] = $taskId;
        $validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'task_id' => 'exists:tasks,id,quest_id,' . $questId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
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
            'status' => 'success',
            'message' => 'Specified task has been successfully toggled.',
            'task_id' => $taskId,
            'is_completed' => $toggleVal
        ), 200);
    }

    public function getTasksCompleted(Request $request, $gameId, $mapId, $questId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        $validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'quest_id' => 'exists:quests,id,map_id,' . $mapId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $taskTrackerEntries = TaskTrackerEntry::where('user_id', auth()->user()->id)->get();
        
        return response()->json($taskTrackerEntries, 200);
    }

    public function checkAdmin() {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to create, update or delete tasks.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create, update or delete tasks.'
            ), 403); // Forbidden
        }

        return false;
    }

    public function checkUser() {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to complete this action.'
            ), 401); // Unauthorized
        }

        return false;
    }
}
