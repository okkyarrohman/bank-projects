<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/get/category', [CategoryController::class, 'getData']);
Route::get('/get/project', [ProjectController::class, 'getData']);



Route::apiResource('project', ProjectController::class);
