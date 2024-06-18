<?php

namespace App\Http\Controllers;

use App\Models\ClassroomUser;

use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Log;

class ClassroomUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get all classroom_users
        $classroomUsers = ClassroomUser::get();
        return response()->json($classroomUsers, 200);
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
        //Put new class
        try {
            $classroomUser = new ClassroomUser();
            $classroomUser->classroom_id = $request->input('classroom_id');
            $classroomUser->user_id = $request->input('user_id');
            $classroomUser->save();

            return response()->json(['message' => "successfully created new classroom_user"], 201);
        } catch (Throwable $e) {
            return response()->json(['error_message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function find($id)
    {
        // find user by id
        $classroomUser = ClassroomUser::where('classroom_user_id', $id)->first();
        if (!$classroomUser) {
            return null;
        }
        return $classroomUser;
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
        try {
            // Log::info(string($id));
            $classroomUser = $this->find($id);
            if (!$classroomUser) {
                return response()->json(['message' => 'classroom_user not found'], 404);
            }
            // Log:info($classData);
            // update user
            if ($request->has('classroom_id')) {
                $classroomUser->classroom_id = $request->input('classroom_id');
            }
            if ($request->has('user_id')) {
                $classroomUser->user_id = $request->input('user_id');
            }
            $classroomUser->save();

            return response()->json(['message' => "successfully updated class_user"], 200);
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
        try {
            $classroomUser = $this->find($id);
            if (!$classroomUser) {
                return response()->json(['message' => 'classroom_user not found'], 404);
            }

            // delete user
            $classroomUser->delete();

            return response()->json(['message' => "successfully deleted classroom_user"], 200);
        } catch (Throwable $e) {
            return response()->json(['error_message' => $e->getMessage()], 500);
        }
    }
}
