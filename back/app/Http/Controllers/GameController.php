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
    public function getAll()
    {
        return response()->json(Game::all(), 200);
    }

    public function getAllHierarchy()
    {
        return response()->json(Game::with('maps.quests.tasks')->get(), 200);
    }

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
}
