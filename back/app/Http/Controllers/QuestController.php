<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Quest;
use App\Models\Map;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class QuestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return response()->json(Quest::all(), 200);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllHierarchy()
    {
        return response()->json(Quest::with("tasks")->get(), 200);
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
                'message' => 'Must be logged in to create a new quest.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create a new quest.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'map_coord_x' => 'numeric|nullable',
            'map_coord_y' => 'numeric|nullable',
            'map_id' => 'numeric|required|exists:maps,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        // --------- GAME OWNER CHECK ---------

        $map = Map::find($validated_data['map_id']);
        $game = Game::find($map->game_id);

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to add more quests to specified map.'
            ), 403);
        }

        // --------- --------- ---------

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new quest has been added.',
            'quest' => Quest::create($validated_data)
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
        $quest = Quest::find($id);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid quest ID.'
            ), 422);
        }

        return response()->json($quest, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getHierarchy($id)
    {
        $quest = Quest::with("tasks")->find($id);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid quest ID.'
            ), 422);
        }

        return response()->json($quest, 200);
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
                'message' => 'Must be logged in to update quests.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to update quests.'
            ), 403); // Forbidden
        }

        $quest = Quest::find($id);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid quest ID.'
            ), 422);
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'map_coord_x' => 'numeric|nullable',
            'map_coord_y' => 'numeric|nullable',
            'map_id' => 'numeric|required|exists:maps,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $map_source = Map::find($quest->map_id);
        $game_source = Game::find($map_source->game_id);

        $map_destination = Map::find($validated_data['map_id']);
        $game_destination = Game::find($map_destination->game_id);

        if ($game_source->owner_id != $user->id || $game_destination->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to update specified game (-s).'
            ), 403);
        }

        $quest->title = $validated_data['title'];
        $quest->description = empty($validated_data['description']) ? null : $validated_data['description'];
        $quest->image_url = empty($validated_data['image_url']) ? null : $validated_data['image_url'];
        $quest->map_coord_x = empty($validated_data['map_coord_x']) ? null : $validated_data['map_coord_x'];
        $quest->map_coord_y = empty($validated_data['map_coord_y']) ? null : $validated_data['map_coord_y'];
        $quest->map_id = $validated_data['map_id'];

        $quest->save();

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified quest has been successfully updated.',
            'quest' => $quest
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
                'message' => 'Must be logged in to create a new quest.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create a new quest.'
            ), 403); // Forbidden
        }

        $quest = Quest::find($id);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid quest ID.'
            ), 422);
        }

        // --------- GAME OWNER CHECK ---------

        $map = Map::find($quest->map_id);
        $game = Game::find($map->game_id);

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to remove quests from specified map.'
            ), 403);
        }

        // --------- --------- ---------

        Quest::destroy($quest->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified quest has been successfully deleted.',
            'quest' => $quest
        ), 200);
    }
}
