<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Clean, academic route definitions:
| 1. '/' -> Central Split Dashboard (Tasks on Left, Notes on Right)
| 2. Tasks & Subtasks: Store, Update, Toggle Complete, Delete
| 3. Notes / Notebooks: Store, Update, Delete
*/

// Main Dashboard
Route::get('/', [NoteController::class, 'index'])->name('dashboard');
Route::redirect('/notes', '/');

// Task Routes (Left Panel)
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Note Routes (Right Panel)
Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
