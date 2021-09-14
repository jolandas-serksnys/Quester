<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Game;

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
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated_data = $validator->validated();

        $data = Game::find($validated_data['id']);

        if (!$data) {
            $error = array(
                'status' => 'error',
                'message' => 'Game with given ID could not be found.'
            );

            return response()->json($error, 404);
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
    }
}
