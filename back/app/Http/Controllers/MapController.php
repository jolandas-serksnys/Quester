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
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            $message = array(
                'status' => 'error',
                'message' => 'Must be logged in to create a new map.'
            );

            return response()->json($message, 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            $message = array(
                'status' => 'error',
                'message' => 'User group has no rights to create a new map.'
            );

            return response()->json($message, 403); // Forbidden
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

        $game = Game::find($validated_data['game_id']);

        if (!$game) {
            $message = array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        if ($game->owner_id != $user->id) {
            $message = array(
                'status' => 'error',
                'message' => 'User has no rights to add more maps to specified game.'
            );

            return response()->json($message, 403);
        }

        $map = Map::create($validated_data);

        $message = array(
            'status' => 'success',
            'message' => 'A new map has been added.',
            'map' => $map
        );

        return response()->json($message, 201); // Created
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
            $message = array(
                'status' => 'error',
                'message' =>  'Map with given ID could not be found.'
            );

            return response()->json($message, 404);
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
            $message = array(
                'status' => 'error',
                'message' => 'Must be logged in to update update maps.'
            );

            return response()->json($message, 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            $message = array(
                'status' => 'error',
                'message' => 'User group has no rights to update maps.'
            );

            return response()->json($message, 403); // Forbidden
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
            $message = array(
                'status' => 'error',
                'message' => 'Map with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        $game = Game::find($validated_data['game_id']);

        if (!$game) {
            $message = array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        if ($game->owner_id != $user->id) {
            $message = array(
                'status' => 'error',
                'message' => 'User has no rights to update maps of specified game.'
            );

            return response()->json($message, 403);
        }

        if (!$data) {
            $message = array(
                'status' => 'error',
                'message' => 'Map with given ID could not be found.'
            );

            return response()->json($message, 404);
        }
        
        $data->title = $validated_data['title'];
        $data->description = empty($validated_data['description']) ? $data->description : $validated_data['description'];
        $data->image_url = empty($validated_data['image_url']) ? $data->image_url : $validated_data['image_url'];
        $data->game_id = $validated_data['game_id'];

        $data->save();
        $message = array(
            'status' => 'success',
            'message' => 'Specified map has been successfully updated.',
            'map' => $data
        );

        return response()->json($message, 200);
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
            $message = array(
                'status' => 'error',
                'message' => 'Must be logged in to delete a map.'
            );

            return response()->json($message, 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            $message = array(
                'status' => 'error',
                'message' => 'User group has no rights to delete maps.'
            );

            return response()->json($message, 403); // Forbidden
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
            $message = array(
                'status' => 'error',
                'message' => 'Map with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        $game = Game::find($data->game_id);

        if (!$game) {
            $message = array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        if ($game->owner_id != $user->id) {
            $message = array(
                'status' => 'error',
                'message' => 'User has no rights to delete maps from specified game.'
            );

            return response()->json($message, 403);
        }

        Map::destroy($data->id);
        $message = array(
            'status' => 'success',
            'message' => 'Specified map has been successfully deleted.',
            'map' => $data
        );

        return response()->json($message, 200);
    }
}
