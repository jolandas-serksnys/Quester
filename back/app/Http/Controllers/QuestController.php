<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Quest;
use App\Models\Game;

class QuestController extends Controller
{
    public function getValidationArray() {
        return [
            'game_id' => 'nullable|numeric',
            'map_id' => 'nullable|numeric',
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'map_coord_x' => 'numeric|nullable',
            'map_coord_y' => 'numeric|nullable'
        ];
    }

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
        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        if($checkExistsParents = $this->checkExistsParents($request))
            return $checkExistsParents;

        return response()->json(Quest::where('map_id', $mapId)->with('tasks')->get(), 200);
    }

    public function createGameMapQuest(Request $request, $gameId, $mapId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        if($checkExistsParents = $this->checkExistsParents($request))
            return $checkExistsParents;

    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        return response()->json(Quest::create($validator->validated()), 201); // Created
    }

    public function getGameMapQuest(Request $request, $gameId, $mapId, $questId)
    {
        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        return response()->json(Quest::where("map_id", $mapId)->with("tasks")->find($questId), 200);
    }

    public function updateGameMapQuest(Request $request, $gameId, $mapId, $questId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        $quest = Quest::where("map_id", $mapId)->find($questId);
        $quest->update($validator->validated());

        return response()->json($quest, 200);
    }

    public function deleteGameMapQuest(Request $request, $gameId, $mapId, $questId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        $request['map_id'] = $mapId;
        $request['quest_id'] = $questId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        $quest = Quest::where("map_id", $mapId)->find($questId);
        Quest::destroy($quest->id);

        return response()->json($quest, 200);
    }

    public function checkExistsParents(Request $request) {
        $validator = Validator::make($request->all(), [
            'game_id' => 'numeric|required|exists:games,id',
            'quest_id' => 'exists:quests,id,map_id,' . $request['map_id']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        return false;
    }

    public function checkExistsConcrete(Request $request) {
        $validator = Validator::make($request->all(), [
            'game_id' => 'numeric|required|exists:games,id',
            'quest_id' => 'exists:quests,id,map_id,' . $request['map_id'],
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
