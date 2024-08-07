<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ClassroomUserController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissionFileController;
use App\Http\Controllers\MarkController;

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
    Route::get('/classroom/{id}', 'show');
    Route::put('/classrooms', 'store');
    Route::patch('/classrooms/{id}', 'update');
    Route::delete('/classrooms/{id}', 'destroy');
});

Route::controller(ClassroomUserController::class)->group(function () {
    Route::get('/classroom-users', 'index');
    Route::get('/classroom-users/{classroom_id}', 'getByClassroomId');
    Route::put('/classroom-users', 'store');
    Route::patch('/classroom-users/{id}', 'update');
    Route::delete('/classroom-users/{id}', 'destroy');
});

Route::controller(PdfController::class)->group(function () {
    Route::get('/pdfs/{moduleId}', 'getPdfFilesByModuleId');
    Route::get('/pdfs/download/{pdfId}', 'downloadPdf');
    Route::post('/pdf', 'upload');
    Route::delete('/pdfs', 'deletePdfs');
});

Route::controller(AssessmentController::class)->group(function () {
    Route::post('/assessment', 'store');
    Route::get('/assessments', 'index');
    Route::get('/assessments/{classroom_id}', 'getByClassroomId');
    Route::get('/assessment/{moduleId}', 'getByModuleId');
    Route::delete('/assessment/{assessmentId}', 'delete');
    Route::get('assessment/file/download/{id}', 'downloadFile');
});

// Route::get('/pdfs/{moduleId}', [PdfController::class, 'getPdfFilesByModuleId']);
// Route::get('/pdfs/download/{pdfId}', [PdfController::class, 'downloadPdf']);
// Route::post('/pdf', [PdfController::class, 'upload']);

Route::controller(ModuleController::class)->group(function () {
    Route::get('/modules', 'index');
    Route::get('/module/{id}', 'show');
    Route::put('/module', 'store');
    Route::patch('/module/{id}', 'update');
    Route::delete('/module/{id}', 'destroy');
});

Route::controller(SubmissionFileController::class)->group(function () {
    Route::post('/get-submission-files', 'getByStudentAndAssessment');
    Route::post('/submission-file', 'upload');
    Route::post('/submissions/download', 'downloadFileByStudentAndAssessment');
});

Route::controller(SubmissionController::class)->group(function () {
    Route::get('/submission', 'index');
    Route::post('/submission', 'store');
    Route::get('/get-submission/{assessment_id}', 'getByAssessmentId');
});

Route::controller(MarkController::class)->group(function () {
    Route::get('/marks', 'getAllMarks');
    Route::get('/marks/student/{student_id}/assessment/{assessment_id}', 'getMarksByStudentAndAssessment');
    Route::get('/marks/assessment/{assessment_id}', 'getMarksByAssessment');
    Route::get('/marks/classroom/{classroom_id}', 'getMarksByClassroom');
    Route::get('/marks/module/{module_id}', 'getMarksByModule');
    Route::post('/marks/assessment/{assessment_id}/students', 'getMarksByAssessmentAndStudents');
    Route::post('/marks/save', 'saveMarks');
});

// Route::post('/marks/save', [MarkController::class, 'saveMarks']);
// Route::post('/marks/assessment/{assessment_id}/students', [MarkController::class, 'getMarksByAssessmentAndStudents']);
// Route::get('/marks', [MarkController::class, 'getAllMarks']);
// Route::get('/marks/student/{student_id}/assessment/{assessment_id}', [MarkController::class, 'getMarksByStudentAndAssessment']);
// Route::get('/marks/assessment/{assessment_id}', [MarkController::class, 'getMarksByAssessment']);
// Route::get('/marks/classroom/{classroom_id}', [MarkController::class, 'getMarksByClassroom']);
// Route::get('/marks/module/{module_id}', [MarkController::class, 'getMarksByModule']);
// Route::resource('users', UserController::class);
// Route::resource('classrooms', ClassroomController::class);
// Route::resource('announcements', AnnouncementController::class);
// Route::resource('modules', ModuleController::class);
// Route::resource('assessments', AssessmentController::class);
// Route::resource('grades', GradeController::class);
// Route::resource('submissions', SubmissionController::class);
