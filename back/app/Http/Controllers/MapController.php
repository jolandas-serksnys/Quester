<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Map;
use App\Models\Game;

class MapController extends Controller
{
    public function getAll()
    {
        return response()->json(Map::all(), 200);
    }
    
    public function getAllHierarchy()
    {
        return response()->json(Map::with("quests.tasks")->get(), 200);
    }

    public function getGameMaps(Request $request, $gameId)
    {
        $request['id'] = $gameId;
        $validator = Validator::make($request->all(), [
            'id' => 'numeric|required|exists:games,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json(Map::where('maps.game_id', $gameId)->get(), 200);
    }

    public function createGameMap(Request $request, $gameId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['game_id'] = $gameId;
    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'game_id' => 'numeric|required|exists:games,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $game = Game::find($gameId);

        if ($game->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to add more maps to specified game.'
            ), 403);
        }

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new map has been added.',
            'map' => Map::create($validator->validated())
        ), 201); // Created
    }
    
    public function getGameMap(Request $request, $gameId, $mapId)
    {
        $request['map_id'] = $mapId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json(Map::where('maps.game_id', $gameId)->find($mapId), 200);
    }
    
    public function updateGameMap(Request $request, $gameId, $mapId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['map_id'] = $mapId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
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
                'message' => 'User has no rights to update maps of specified game (-s).'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);
        
        $map->update($validator->validated());
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified map has been successfully updated.',
            'map' => $map
        ), 200);
    }
    
    public function deleteGameMap(Request $request, $gameId, $mapId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['map_id'] = $mapId;
        $validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (Game::find($gameId)->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to delete maps from specified game.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);
        Map::destroy($map->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified map has been successfully deleted.',
            'map' => $map
        ), 200);
    }

    public function checkUser() {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to create a new game.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create, update or delete maps.'
            ), 403); // Forbidden
        }

        return false;
    }
}
