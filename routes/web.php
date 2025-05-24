<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TableauController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskColumnController;
use Illuminate\Support\Facades\Route;

Route::post('/post_message', [WallController::class, 'postMessage'])->name('message.post');
Route::post('/post_message', [WallController::class, 'postMessage'])->name('message.post');


Route::delete('/delete_message/{id}', [WallController::class, 'deleteMessage'])->name('message.delete');

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    //route login
    //route login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    //route register
    //route register
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/projet/{projet}', [HomeController::class, 'show'])->name('projet.show');
    Route::post('/tableau/create', [TableauController::class, 'create'])->name('tableau.create');
    Route::delete('/projet/{projet}', [TableauController::class, 'destroy'])->name('projet.destroy');
    // Gestion des membres de projet
    Route::post('/projet/{projet}/members', [ProjectMemberController::class, 'addMember'])->name('projet.members.add');
    Route::delete('/projet/{projet}/members/{user}', [ProjectMemberController::class, 'removeMember'])->name('projet.members.remove');
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
