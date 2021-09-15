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
    public function list()
    {
        return response()->json(Quest::all(), 200);
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
            'map_id' => 'numeric|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        // --------- MAP CHECK ---------

        $map = Map::find($validated_data['map_id']);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Map with given ID could not be found.'
            ), 404);
        }

        // --------- GAME OWNER CHECK ---------

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $data = Quest::find($validated_data['id']);

        if (!$data) {
            return response()->json(array(
                'status' => 'error',
                'message' =>  'Quest with given ID could not be found.'
            ), 404);
        }

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
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
            'id' => 'numeric|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        $data = Quest::find($validated_data['id']);

        if (!$data) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Quest with given ID could not be found.'
            ), 404);
        }

        // --------- MAP CHECK ---------

        $map = Map::find($data->map_id);
        $game = Game::find($map->game_id);

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to remove quests from specified map.'
            ), 403);
        }

        // --------- --------- ---------

        Quest::destroy($data->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified quest has been successfully deleted.',
            'quest' => $data
        ), 200);
    }
}
