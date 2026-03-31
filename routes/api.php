<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/tasks/report', [TaskController::class, 'report']);
 
Route::apiResource('tasks', TaskController::class)->only([
    'index',   // GET    /api/tasks
    'store',   // POST   /api/tasks
    'destroy', // DELETE /api/tasks/{task}
]);
 
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
/**Route::get('/tasks/report', [TaskController::class, 'report']);
 
Route::apiResource('tasks', TaskController::class)->only([
    'index',   // GET    /api/tasks
    'store',   // POST   /api/tasks
    'update',  // PUT    /api/tasks/{task}  ← edit title/due_date/priority
    'destroy', // DELETE /api/tasks/{task}
]);
 
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);/ */