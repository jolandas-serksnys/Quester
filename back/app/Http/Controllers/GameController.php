<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Game;

class GameController extends Controller
{
    public function getValidationArray() {
        return [
            'title' => 'string|required|max:255',
            'description' => 'string|max:65535|nullable',
            'image_url' => 'string|max:255|nullable',
            'genre' => 'string|max:255|nullable',
            'rating' => 'string|max:255|nullable'
        ];
    }

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
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        $validated_data['owner_id'] = auth()->user()->id;

        return response()->json(Game::create($validated_data), 201); // Created
    }

    public function get(Request $request, $gameId)
    {
        $request['game_id'] = $gameId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        return response()->json(Game::with('owner')->withCount('maps')->find($gameId), 200);
    }

    public function getHierarchy(Request $request, $gameId)
    {
        $request['game_id'] = $gameId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        return response()->json(Game::with('maps.quests.tasks')->find($gameId), 200);
    }

    public function update(Request $request, $gameId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

    	$validator = Validator::make($request->all(), $this->getValidationArray());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;
        
        $game = Game::find($request['game_id']);
        $game->update($validator->validated());

        return response()->json($game, 200);
    }

    public function delete(Request $request, $gameId)
    {
        if($adminCheck = $this->checkAdmin())
            return $adminCheck;

        $request['game_id'] = $gameId;
        if($checkExistsConcrete = $this->checkExistsConcrete($request))
            return $checkExistsConcrete;

        if($checkOwner = $this->checkOwner($request))
            return $checkOwner;

        $game = Game::find($request['game_id']);
        Game::destroy($gameId);

        return response()->json($game, 200);
    }

    public function checkExistsConcrete(Request $request) {
        $validator = Validator::make($request->all(), [
            'game_id' => 'numeric|required|exists:games,id'
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
