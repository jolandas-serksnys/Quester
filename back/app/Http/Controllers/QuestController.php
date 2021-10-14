<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Quest;
use App\Models\Map;
use App\Models\Game;

class QuestController extends Controller
{
    
    public function getAll()
    {
        return response()->json(Quest::all(), 200);
    }
    
    public function getAllHierarchy()
    {
        return response()->json(Quest::with("tasks")->get(), 200);
    }

    public function getGameMapQuests(Request $request, $gameId, $mapId)
    {
        $request['map_id'] = $mapId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        return response()->json(Quest::where('map_id', $mapId)->with('tasks')->get(), 200);
    }

    public function createGameMapQuest(Request $request, $gameId, $mapId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['map_id'] = $mapId;
    	$validator = Validator::make($request->all(), [
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'map_coord_x' => 'numeric|nullable',
            'map_coord_y' => 'numeric|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        if (Game::find($gameId)->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to create quests for the specified map.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);
        $validated_data['map_id'] = $map->id;

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new quest has been added.',
            'quest' => Quest::create($validated_data)
        ), 201); // Created
    }

    public function getGameMapQuest(Request $request, $gameId, $mapId, $questId)
    {
        $request['quest_id'] = $questId;
        $request['map_id'] = $mapId;
    	$validator = Validator::make($request->all(), [
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'map_id' => 'exists:maps,id,game_id,' . $gameId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        return response()->json(Quest::where("map_id", $mapId)->with("tasks")->find($questId), 200);
    }

    public function updateGameMapQuest(Request $request, $gameId, $mapId, $questId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['quest_id'] = $questId;
        $request['map_id'] = $mapId;
    	$validator = Validator::make($request->all(), [
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'map_id' => 'exists:maps,id,game_id,' . $gameId,
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'map_coord_x' => 'numeric|nullable',
            'map_coord_y' => 'numeric|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (Game::find($gameId)->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to update specified game (-s).'
            ), 403);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);
        $quest->update($validator->validated());

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified quest has been successfully updated.',
            'quest' => $quest
        ), 200);
    }
    
    public function deleteGameMapQuest(Request $request, $gameId, $mapId, $questId)
    {
        if($userCheck = $this->checkUser())
            return $userCheck;

        $request['quest_id'] = $questId;
        $request['map_id'] = $mapId;
    	$validator = Validator::make($request->all(), [
            'quest_id' => 'exists:quests,id,map_id,' . $mapId,
            'map_id' => 'exists:maps,id,game_id,' . $gameId
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (Game::find($gameId)->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to update specified game (-s).'
            ), 403);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);
        Quest::destroy($quest->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified quest has been successfully deleted.',
            'quest' => $quest
        ), 200);
    }

    public function checkUser() {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to create a quests.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create, update or delete quests.'
            ), 403); // Forbidden
        }

        return false;
    }
}
