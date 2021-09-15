<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return response()->json(Game::all(), 200);
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
            $message = array(
                'status' => 'error',
                'message' => 'Must be logged in to create a new game.'
            );

            return response()->json($message, 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            $message = array(
                'status' => 'error',
                'message' => 'User group has no rights to create a new game.'
            );

            return response()->json($message, 403); // Forbidden
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

        $game = Game::create($validated_data);

        $message = array(
            'status' => 'success',
            'message' => 'A new game has been added.',
            'game' => $game
        );

        return response()->json($message, 201); // Created
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $data = Game::find($validated_data['id']);

        if (!$data) {
            $message = array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            $message = array(
                'status' => 'error',
                'message' => 'Must be logged in to update games.'
            );

            return response()->json($message, 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            $message = array(
                'status' => 'error',
                'message' => 'User group has no rights to update games.'
            );

            return response()->json($message, 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required',
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
        $data = Game::find($validated_data['id']);

        if (!$data) {
            $message = array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        if ($data->owner_id != $user->id) {
            $message = array(
                'status' => 'error',
                'message' => 'User has no rights to update specified game.'
            );

            return response()->json($message, 403);
        }
        
        $data->title = $validated_data['title'];
        $data->description = empty($validated_data['description']) ? $data->description : $validated_data['description'];
        $data->image_url = empty($validated_data['image_url']) ? $data->image_url : $validated_data['image_url'];
        $data->genre = empty($validated_data['genre']) ? $data->genre : $validated_data['genre'];
        $data->rating = empty($validated_data['rating']) ? $data->rating : $validated_data['rating'];

        $data->save();
        $message = array(
            'status' => 'success',
            'message' => 'Specified game has been successfully updated.',
            'game' => $data
        );

        return response()->json($message, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            $message = array(
                'status' => 'error',
                'message' => 'Must be logged in to delete a game.'
            );

            return response()->json($message, 401); // Unauthorized
        }

        if ($user->user_group == 0) {
            $message = array(
                'status' => 'error',
                'message' => 'User group has no rights to delete games.'
            );

            return response()->json($message, 403); // Forbidden
        }

    	$validator = Validator::make($request->all(), [
            'id' => 'numeric|required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();
        $data = Game::find($validated_data['id']);

        if (!$data) {
            $message = array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            );

            return response()->json($message, 404);
        }

        if ($data->owner_id != $user->id) {
            $message = array(
                'status' => 'error',
                'message' => 'User has no rights to delete specified games.'
            );

            return response()->json($message, 403);
        }

        Game::destroy($data->id);
        $message = array(
            'status' => 'success',
            'message' => 'Specified game has been successfully deleted.',
            'game' => $data
        );

        return response()->json($message, 200);
    }
}
