<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ClassroomUserController;
use App\Http\Controllers\PdfUploadController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\SubmissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');          //fetch all users
    Route::get('/users/{id}', 'show');      //fetch user by id
    Route::put('/users', 'store');          //put new user
    Route::patch('/users/{id}', 'update');  //modify user by id
    Route::delete('/users/{id}', 'destroy');  //remove user by id
});

Route::controller(ClassroomController::class)->group(function () {
    Route::get('/classrooms', 'index');
    Route::put('/classrooms', 'store');
    Route::patch('/classrooms/{id}', 'update');
    Route::delete('/classrooms/{id}', 'destroy');
});

Route::controller(ClassroomUserController::class)->group(function () {
    Route::get('/classroom-users', 'index');
    Route::put('/classroom-users', 'store');
    Route::patch('/classroom-users/{id}', 'update');
    Route::delete('/classroom-users/{id}', 'destroy');
});

Route::controller(PdfUploadController::class)->group(function () {
    Route::get('/pdf/{id}', 'getFileById');
    Route::post('/pdf', 'upload');
    Route::post('/pdfs', 'getFilesByIds'); // New endpoint to fetch multiple PDFs
});


// Route::resource('users', UserController::class);
// Route::resource('classrooms', ClassroomController::class);
// Route::resource('announcements', AnnouncementController::class);
// Route::resource('modules', ModuleController::class);
// Route::resource('assessments', AssessmentController::class);
// Route::resource('grades', GradeController::class);
// Route::resource('submissions', SubmissionController::class);