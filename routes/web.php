<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskColumnController;
use Illuminate\Support\Facades\Route;



Route::post('/post_message', [WallController::class, 'postMessage'])->name('message.post');


Route::delete('/delete_message/{id}', [WallController::class, 'deleteMessage'])->name('message.delete');

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    //route login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    //route register
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/columns', [TaskColumnController::class, 'store'])->name('columns.store');
    Route::resource('task-columns', TaskColumnController::class)->only(['store']);

});



require __DIR__ . '/auth.php';
