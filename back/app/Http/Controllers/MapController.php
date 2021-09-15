<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Map;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return response()->json(Map::all(), 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_hierarchy()
    {
        return response()->json(Map::with("quests.tasks")->get(), 200);
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
                'message' => 'Must be logged in to create a new map.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create a new map.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'game_id' => 'numeric|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        // --------- GAME EXISTS CHECK ---------

        $game = Game::find($validated_data['game_id']);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            ), 404);
        }

        // --------- GAME OWNER CHECK ---------

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to add more maps to specified game.'
            ), 403);
        }

        // --------- --------- ---------

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new map has been added.',
            'map' => Map::create($validated_data)
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

        $data = Map::find($validated_data['id']);

        if (!$data) {
            return response()->json(array(
                'status' => 'error',
                'message' =>  'Map with given ID could not be found.'
            ), 404);
        }

        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function read_hierarchy(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $data = Map::with("quests.tasks")->find($validated_data['id']);

        if (!$data) {
            return response()->json(array(
                'status' => 'error',
                'message' =>  'Map with given ID could not be found.'
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
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to update update maps.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to update maps.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required',
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'game_id' => 'numeric|required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        $data = Map::find($validated_data['id']);

        if (!$data) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Map with given ID could not be found.'
            ), 404);
        }

        $game = Game::find($validated_data['game_id']);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            ), 404);
        }

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to update maps of specified game.'
            ), 403);
        }

        if (!$data) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Map with given ID could not be found.'
            ), 404);
        }
        
        $data->title = $validated_data['title'];
        $data->description = empty($validated_data['description']) ? $data->description : $validated_data['description'];
        $data->image_url = empty($validated_data['image_url']) ? $data->image_url : $validated_data['image_url'];
        $data->game_id = $validated_data['game_id'];

        $data->save();
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified map has been successfully updated.',
            'map' => $data
        ), 200);
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
                'message' => 'Must be logged in to delete a map.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to delete maps.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        $data = Map::find($validated_data['id']);

        if (!$data) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Map with given ID could not be found.'
            ), 404);
        }

        $game = Game::find($data->game_id);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            ), 404);
        }

        if ($game->owner_id != $user->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to delete maps from specified game.'
            ), 403);
        }

        Map::destroy($data->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified map has been successfully deleted.',
            'map' => $data
        ), 200);
    }
}
