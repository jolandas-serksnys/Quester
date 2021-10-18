<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Game;

class GameController extends Controller
{
    public function getAll()
    {
        return response()->json(Game::with('owner')->withCount('maps')->get(), 200);
    }

    public function getAllHierarchy()
    {
        return response()->json(Game::with('maps.quests.tasks')->get(), 200);
    }

    public function create(Request $request)
    {
        if($userCheck = $this->checkAdmin())
            return $userCheck;

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
        $validated_data['owner_id'] = auth()->user()->id;

        return response()->json(array(
            'status' => 'success',
            'message' => 'A new game has been added.',
            'game' => Game::create($validated_data)
        ), 201); // Created
    }

    public function get(Request $request, $gameId)
    {
        $request['id'] = $gameId;
    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required|exists:games,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json(Game::with('owner')->withCount('maps')->find($gameId), 200);
    }

    public function getHierarchy(Request $request, $gameId)
    {
        $request['id'] = $gameId;
    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required|exists:games,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json(Game::with('maps.quests.tasks')->find($gameId), 200);
    }

    public function update(Request $request, $gameId)
    {
        if($userCheck = $this->checkAdmin())
            return $userCheck;

        $request['id'] = $gameId;
    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required|exists:games,id',
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'genre' => 'string|max:255|nullable',
            'rating' => 'string|max:255|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $game = Game::find($gameId);

        if ($game->owner_id != auth()->user()->id) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'User has no rights to update specified game.'
            ), 403);
        }
        
        $game->update($validator->validated());

        return response()->json(array(
            'status' => 'success',
            'message' => 'Specified game has been successfully updated.',
            'game' => $game
        ), 200);
    }

    public function delete(Request $request, $gameId)
    {
        if($userCheck = $this->checkAdmin())
            return $userCheck;

        $request['id'] = $gameId;
        $validator = Validator::make($request->all(), [
            'id' => 'numeric|required|exists:games,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $game = Game::find($gameId);

        if ($game->owner_id != auth()->user()->id) {
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

    public function checkAdmin() {
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
                'message' => 'User group has no rights to create, update or delete games.'
            ), 403); // Forbidden
        }

        return false;
    }
}
