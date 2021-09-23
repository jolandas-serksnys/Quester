<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Game;
use App\Models\Map;
use App\Models\Quest;
use App\Models\Task;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Http\Controllers\MapController;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return response()->json(Game::all(), 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllHierarchy()
    {
        return response()->json(Game::with('maps.quests.tasks')->get(), 200);
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
                'message' => 'Must be logged in to create a new game.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to create a new game.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'genre' => 'string|max:255|nullable',
            'rating' => 'string|max:255|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        $validated_data['owner_id'] = $user->id;

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new game has been added.',
            'game' => Game::create($validated_data)
        ), 201); // Created
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($gameId)
    {
        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        return response()->json($game, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getHierarchy($gameId)
    {
        $game = Game::with('maps.quests.tasks')->find($gameId);

        if (!$game) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid game ID.'
            ), 422);
        }

        return response()->json($game, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $gameId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to update games.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to update games.'
            ), 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'genre' => 'string|max:255|nullable',
            'rating' => 'string|max:255|nullable'
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
                'message' => 'User has no rights to update specified game.'
            ), 403);
        }
        
        $game->title = $validated_data['title'];
        $game->description = empty($validated_data['description']) ? null : $validated_data['description'];
        $game->image_url = empty($validated_data['image_url']) ? null : $validated_data['image_url'];
        $game->genre = empty($validated_data['genre']) ? null : $validated_data['genre'];
        $game->rating = empty($validated_data['rating']) ? null : $validated_data['rating'];

        $game->save();

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified game has been successfully updated.',
            'game' => $game
        ), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete($gameId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Must be logged in to delete a game.'
            ), 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User group has no rights to delete games.'
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
                'message' => 'User has no rights to delete specified games.'
            ), 403);
        }

        Game::destroy($gameId);

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified game has been successfully deleted.',
            'game' => $game
        ), 200);
    }

    // -------------------------------------------------

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

        // --------- GAME OWNER CHECK ---------
        
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

        // --------- --------- ---------

        $validated_data['game_id'] = $gameId;

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new map has been added.',
            'map' => Map::create($validated_data)
        ), 201); // Created
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  int  $mapId
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    // -----------------------------------------------------------

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

        // --------- GAME OWNER CHECK ---------

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
                'message' => 'User has no rights to add more quests to specified map.'
            ), 403);
        }

        $map = Map::where('maps.game_id', $gameId)->find($mapId);

        if (!$map) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Invalid map ID.'
            ), 422);
        }

        // --------- --------- ---------

        $validated_data['map_id'] = $mapId;

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

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteGameMapQuest($gameId, $mapId, $questId)
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
