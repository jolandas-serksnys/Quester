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
    public function getAll()
    {
        return response()->json(Map::all(), 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllHierarchy()
    {
        return response()->json(Map::with("quests.tasks")->get(), 200);
    }

    public function getGameMaps($gameId)
    {
        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        return response()->json(Map::where('maps.game_id', $gameId)->get(), 200);
    }

    public function createGameMap(Request $request, $gameId)
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
                'message' => 'User has no rights to add more maps to specified game.'
            ), 403);
        }

        $validated_data['game_id'] = $gameId;

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new map has been added.',
            'map' => Map::create($validated_data)
        ), 201); // Created
    }
    
    public function getGameMap($gameId, $mapId)
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

        return response()->json($map, 200);
    }
    
    public function updateGameMap(Request $request, $gameId, $mapId)
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
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
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
                'message' => 'User has no rights to update maps of specified game (-s).'
            ), 403);
        }
        
        $map->title = $validated_data['title'];
        $map->description = empty($validated_data['description']) ? null : $validated_data['description'];
        $map->image_url = empty($validated_data['image_url']) ? null : $validated_data['image_url'];

        $map->save();
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified map has been successfully updated.',
            'map' => $map
        ), 200);
    }
    
    public function deleteGameMap($gameId, $mapId)
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

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $game = Game::find($gameId);

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

        Map::destroy($map->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified map has been successfully deleted.',
            'map' => $map
        ), 200);
    }
}
