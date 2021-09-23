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

    public function getGameMapQuests($gameId, $mapId)
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

        $quest = Quest::where("map_id", $mapId)->with("tasks")->get();

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }
        
        return response()->json($quest, 200);
    }

    public function createGameMapQuest(Request $request, $gameId, $mapId)
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
                'message' => 'User has no rights to create quests for the specified map.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $validated_data['map_id'] = $map->id;

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new quest has been added.',
            'quest' => Quest::create($validated_data)
        ), 201); // Created
    }

    public function getGameMapQuest($gameId, $mapId, $questId)
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

        $quest = Quest::where("map_id", $mapId)->with("tasks")->find($questId);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }
        
        return response()->json($quest, 200);
    }

    public function updateGameMapQuest(Request $request, $gameId, $mapId, $questId)
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
                'message' => 'This user has no rights to update quests for the specified game.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);

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
            'map_coord_y' => 'numeric|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        if ($game->owner_id != $user->id) {
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

        $quest->save();

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified quest has been successfully updated.',
            'quest' => $quest
        ), 200);
    }
    
    public function deleteGameMapQuest($gameId, $mapId, $questId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to delete quests.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to delete quests.'
            ), 403); // Forbidden
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
                'message' => 'This user has no rights to delete quests from the specified game.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        $quest = Quest::where("map_id", $mapId)->find($questId);

        if (!$quest) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid quest ID.'
            ), 422);
        }

        Quest::destroy($quest->id);
        
        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified quest has been successfully deleted.',
            'quest' => $quest
        ), 200);
    }
}
