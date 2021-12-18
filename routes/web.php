<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::post('tasks', [TaskController::class, 'store']);
Route::patch('tasks/{task}', [TaskController::class, 'update']);
