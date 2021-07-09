<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/survey/get/{id}', [\App\Http\Controllers\ApiController::class,'getSurvey']);
Route::get('v1/answers/get/{id}', [\App\Http\Controllers\ApiController::class,'getAnswer']);
Route::get('v1/answers/get-list/{qid}', [\App\Http\Controllers\ApiController::class,'getAnswers']);
Route::get('v1/questions/get/{id}', [\App\Http\Controllers\ApiController::class,'getQuestion']);
Route::post('v1/client/save', [\App\Http\Controllers\ApiController::class,'saveAnswers']);
