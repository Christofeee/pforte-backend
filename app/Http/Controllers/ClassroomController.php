<?php

namespace App\Http\Controllers;

use App\Models\Classroom;

use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Log;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get all classrooms
        $classrooms = Classroom::get();
        return response()->json($classrooms, 200);
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
            $classroom = new Classroom();
            $classroom->name = $request->input('name');
            $classroom->description = $request->input('description');
            $classroom->save();

            return response()->json(['message' => "successfully created new class"], 201);
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
        $classData = Classroom::where('classroom_id', $id)->first();
        if (!$classData) {
            return null;
        }
        return $classData;
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
            $classData = $this->find($id);
            if (!$classData) {
                return response()->json(['message' => 'class not found'], 404);
            }
            // Log:info($classData);
            // update user
            if ($request->has('name')) {
                $classData->name = $request->input('name');
            }
            if ($request->has('description')) {
                $classData->description = $request->input('description');
            }
            $classData->save();

            return response()->json(['message' => "successfully updated class"], 200);
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
            $classData = $this->find($id);
            if (!$classData) {
                return response()->json(['message' => 'class not found'], 404);
            }

            // delete user
            $classData->delete();

            return response()->json(['message' => "successfully deleted class"], 200);
        } catch (Throwable $e) {
            return response()->json(['error_message' => $e->getMessage()], 500);
        }
    }
}
