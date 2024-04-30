<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

use App\Http\Controllers\UtilController;

use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Fetch users
        try {
            $queryParams = $request->query();
            if (!empty($queryParams)) {
                $queryParamKey = key($queryParams);
                $queryParamValue = $request->input($queryParamKey);
                $userData = User::where($queryParamKey, 'like', "%$queryParamValue%")->get();
            } else {
                $userData = User::get();
            }
            if (count($userData) === 0) {
                return response()->json(['message' => 'user not found'], 404);
            }
            return response()->json($userData, 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Put new user
        try {
            // validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'user_type' => 'required|int|in:0,1'
            ]);
            if ($validator->fails()) {
                return response()->json(['error_message' => "bad request"], 400);
            }

            // check if the user is already exist or not
            if (User::where('email', $request->email)->first()) {
                return response()->json(['message' => 'user with the same email is already in the system'], 400);
            }

            // create new user
            $user = new User();
            $user->name = $request->input('name');
            $user->phone = $request->input('phone');
            $user->email = $request->input('email');
            $user->user_type = $request->input('user_type');
            $user->save();

            return response()->json(['message' => "successfully created new user"], 200);
        } catch (Throwable $e) {
            return response()->json(['error_message' => $e->getMessage()], 500);
        }
    }

    public function find($id)
    {
        // find user by id
        $userData = User::where('user_id', $id)->first();
        if (!$userData) {
            return null;
        }
        return $userData;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Fetch the user by ID
        try {
            $userData = $this->find($id);
            if (!$userData) {
                return response()->json(['message' => 'user not found'], 404);
            }
            return response()->json($userData, 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //update existing user data
        try {
            // validate input
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|max:255',
                'user_type' => 'sometimes|int|in:0,1'
            ]);
            if ($validator->fails()) {
                return response()->json(['error_message' => "bad request"], 400);
            }

            // check if the user is exist or not
            $userData = $this->find($id);
            if (!$userData) {
                return response()->json(['message' => 'user not found'], 404);
            }
            // check if the new input user email is taken or not
            if (User::where('email', $request->email)->first()) {
                return response()->json(['message' => 'user with the same email is already in the system'], 400);
            }

            // update user
            if ($request->has('name')) {
                $userData->name = $request->input('name');
            }
            if ($request->has('phone')) {
                $userData->phone = $request->input('phone');
            }
            if ($request->has('email')) {
                $userData->email = $request->input('email');
            }
            if ($request->has('user_type')) {
                $userData->user_type = $request->input('user_type');
            }
            $userData->save();

            return response()->json(['message' => "successfully created new user"], 200);
        } catch (Throwable $e) {
            return response()->json(['error_message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // remove user by id
        try {
            // check if the user is exist or not
            $userData = $this->find($id);
            if (!$userData) {
                return response()->json(['message' => 'user not found'], 404);
            }

            // delete user
            $userData->delete();

            return response()->json(['message' => "successfully deleted user"], 200);
        } catch (Throwable $e) {
            return response()->json(['error_message' => $e->getMessage()], 500);
        }
    }
}