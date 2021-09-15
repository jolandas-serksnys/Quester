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
        // 200 -> (OK), collection

        $data = Game::all();
        $status_code = 200;
        $headers = array();

        return response()->json(
            $data, $status_code, $headers
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // 404 -> (Not Found)
        // 409 -> (Conflict) if resource already exists

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
        // 200 -> (OK), single entry
        // 404 -> (Not Found), if ID not found or invalid.

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

        $status_code = 200;
        $headers = array();

        return response()->json(
            $data, $status_code, $headers
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // 200 -> (OK), single entry / 204 -> (No Content)
        // 404 -> (Not Found), if ID not found or invalid.

        $user = auth()->user();

        if (!$user) {
            $message = array(
                'status' => 'error',
                'message' => 'Must be logged in to update a game.'
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
                'message' => 'User has no rights to update specified games.'
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
        // 200 -> (OK) / 204 -> (No Content)
        // 404 -> (Not Found), if ID not found or invalid

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
