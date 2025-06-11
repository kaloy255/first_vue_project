<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]); 
});
//admin
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class , 'index'])->name('admin.dashboard');
    Route::post('/admin/store', [AdminController::class , 'store'])->name('admin.store');
    Route::patch('/admin/{task}/done', [AdminController::class, 'doneTask'])->middleware(['auth', 'verified'])->name('tasks.done');
    Route::patch('/admin/{task}/failed', [AdminController::class, 'failedTask'])->middleware(['auth', 'verified'])->name('tasks.failed');

});
//user
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'index'])->name('dashboard');
    Route::patch('/dashboard/{task}/start', [EmployeeController::class, 'startTask'])->name('tasks.start');
    Route::patch('/dashboard/{task}/check', [EmployeeController::class, 'checkTask'])->name('tasks.check');

});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
