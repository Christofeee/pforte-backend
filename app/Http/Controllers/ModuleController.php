<?php

namespace App\Http\Controllers;

use App\Models\Module;

use Illuminate\Http\Request;
use Throwable;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Retrieve query parameters
            $queryParams = $request->query();

            // Check if classId parameter is present
            if (isset($queryParams['classId'])) {
                $classId = $queryParams['classId'];

                // Query modules based on classroom_id
                $modules = Module::where('classroom_id', $classId)->get();
            } else {
                // Fetch all modules
                $modules = Module::all();
            }

            // Check if modules are found
            if ($modules->isEmpty()) {
                return response()->json(['message' => 'Modules not found'], 404);
            }

            // Return JSON response with modules
            return response()->json($modules, 200);
        } catch (\Throwable $e) {
            // Handle exceptions and return error response
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
        try {
            $module = new Module();
            $module->name = $request->input('name');
            $module->description = $request->input('description');
            $module->isComplete = $request->input('isComplete');
            $module->classroom_id = $request->input('classroom_id');
            $module->save();

            return response()->json(['message' => "successfully created new module"], 201);
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

    public function find($id)
    {
        $modueData = Module::where('id', $id)->first();
        if (!$modueData) {
            return null;
        }
        return $modueData;
    }
    public function show($id)
    {
        try {
            $moduleData = $this->find($id);
            if (!$moduleData) {
                return response()->json(['message' => 'module not found'], 404);
            }
            return response()->json($moduleData, 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
