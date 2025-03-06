<?php

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TimesheetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:api')->group(function () {
    
    Route::post('/logout',[AuthController::class,'logout']);

    Route::apiResource('/project',ProjectController::class);

    Route::apiResource('/timesheet',TimesheetController::class);

    Route::apiResource('/attribute',AttributeController::class)->except('index');
});


