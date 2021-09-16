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

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to create a new task.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create a new task.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'quest_id' => 'numeric|required|exists:quests,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        return response()->json(array(
            'status' => 'success',
            'message' => 'A new task has been added.',
            'task' => Task::create($validated_data)
        ), 201); // Created
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid task ID.'
            ), 422);
        }

        return response()->json($task, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
            'quest_id' => 'numeric|required|exists:quests,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        $task = Task::find($id);

        if (!$task) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid task ID.'
            ), 422);
        }

        $quest_source = Quest::find($task->quest_id);
        $map_source = Map::find($quest_source->map_id);
        $game_source = Game::find($map_source->game_id);

        $quest_destination = Quest::find($validated_data['quest_id']);
        $map_destination = Map::find($quest_destination->map_id);
        $game_destination = Game::find($map_destination->game_id);

        if ($game_source->owner_id != $user->id || $game_destination->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to update specified game (-s).'
            ), 403);
        }

        $task->title = $validated_data['title'];
        $task->description = empty($validated_data['description']) ? null : $validated_data['description'];
        $task->image_url = empty($validated_data['image_url']) ? null : $validated_data['image_url'];
        $task->quest_id = $validated_data['quest_id'];

        $task->save();

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified task has been successfully updated.',
            'task' => $task
        ), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
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

        $task = Task::find($id);

        if (!$task) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid task ID.'
            ), 422);
        }

        $quest = Quest::find($task->quest_id);
        $map = Map::find($quest->map_id);
        $game = Game::find($map->game_id);

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to update specified game (-s).'
            ), 403);
        }

        Task::destroy($task->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified task has been successfully deleted.',
            'task' => $task
        ), 200);
    }
}
