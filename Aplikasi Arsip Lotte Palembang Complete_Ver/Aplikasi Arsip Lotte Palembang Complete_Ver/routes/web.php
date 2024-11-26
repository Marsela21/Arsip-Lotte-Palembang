<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\SubFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// All routes for file management
Route::get('/file', [FileController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('file');
Route::post('/upload', [FileController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('file.store');
Route::get('/download/{file}', [FileController::class, 'download'])->name('file.download');
Route::post('/file/update/{id}', [FileController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('file.update');
Route::delete('/delete/{file}', [FileController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('file.destroy');


// All routes for folder management
Route::post('/folders', [FolderController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('folder.create');
Route::get('/folders/{id}', [FolderController::class, 'show'])->name('folder.show');

Route::delete('/folders/{id}', [FolderController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('folder.destroy');
Route::post('/folders/{id}', [FolderController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('folder.update');

Route::post('/folders/{folderId}/files', [SubFileController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('subfile.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
