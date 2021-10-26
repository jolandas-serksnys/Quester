<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Map;
use App\Models\Game;

class MapController extends Controller
{
    public function getValidationArray() {
        return [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable'
        ];
    }

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
        $request['game_id'] = $gameId;
        if($checkExistsParents = $this->checkExistsParents($request))
            return $checkExistsParents;

        return response()->json(Map::where('maps.game_id', $gameId)->get(), 200);
    }

    public function createGameMap(Request $request, $gameId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;
            
        $request['game_id'] = $gameId;
        if($checkExistsParents = $this->checkExistsParents($request))
            return $checkExistsParents;

    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        return response()->json(Map::create($validator->validated()), 201); // Created
    }
    
    public function getGameMap(Request $request, $gameId, $mapId)
    {
        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        return response()->json(Map::where('maps.game_id', $gameId)->find($mapId), 200);
    }
    
    public function updateGameMap(Request $request, $gameId, $mapId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        $map = Map::where('maps.game_id', $gameId)->find($mapId);
        $map->update($validator->validated());
        
        return response()->json($map, 200);
    }
    
    public function deleteGameMap(Request $request, $gameId, $mapId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        $map = Map::where('maps.game_id', $gameId)->find($mapId);
        Map::destroy($map->id);
        
        return response()->json($map, 200);
    }

    public function checkExistsParents(Request $request) {
        $validator = Validator::make($request->all(), [
            'game_id' => 'numeric|required|exists:games,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        return false;
    }

    public function checkExistsConcrete(Request $request) {
        $validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $request['game_id']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
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
