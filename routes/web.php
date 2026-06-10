<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AdminChecklistController;
Route::middleware(['auth'])->group(function () {
//  Pantalla exclusiva para el formulario de creación
Route::get('/admin/usuarios/crear', [AdminUserController::class, 'create'])->name('admin.users.create');

//  Pantalla principal para ver el listado y gestionar
Route::get('/admin/usuarios', [AdminUserController::class, 'index'])->name('admin.users.index');

//  Acción de actualizar en MySQL
Route::put('/admin/usuarios/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');

// Pantalla del panel de administración para ver y registrar usuarios
    Route::get('/admin/usuarios', [AdminUserController::class, 'index'])->name('admin.users.index');
    
 // Acción para guardar el nuevo usuario en MySQL
    Route::post('/admin/usuarios', [AdminUserController::class, 'store'])->name('admin.users.store');
});
Route::delete('/admin/usuarios/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
// Ruta para actualizar datos o roles del usuario seleccionado
Route::put('/admin/usuarios/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ChecklistController::class, 'index'])->name('dashboard');
    Route::post('/checklist/subir', [ChecklistController::class, 'storeSubmission'])->name('checklist.submit');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [AdminChecklistController::class, 'index'])->name('admin.dashboard');

    Route::post('/admin/submission/{id}/status', [AdminChecklistController::class, 'updateStatus'])->name('admin.submission.status');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/usuarios/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
         ->name('admin.users.reset-password');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
