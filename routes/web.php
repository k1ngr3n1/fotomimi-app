<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PhotographyController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/gallery/{category?}', [App\Http\Controllers\MediaController::class, 'index'])->name('gallery');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/booking', function () {
    return Inertia::render('Booking');
})->name('booking');

Route::post('/contact', [PhotographyController::class, 'sendContact'])->name('contact.send');
Route::post('/booking', [PhotographyController::class, 'sendBooking'])->name('booking.send');

// Media management routes
Route::post('/media/import', [App\Http\Controllers\MediaController::class, 'importFromDirectory'])->name('media.import');
Route::post('/media/bulk-import', [App\Http\Controllers\MediaController::class, 'bulkImport'])->name('media.bulk-import');
Route::put('/media/{media}', [App\Http\Controllers\MediaController::class, 'update'])->name('media.update');
Route::delete('/media/{media}', [App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
    
    // Media management
    Route::get('/media', [App\Http\Controllers\MediaController::class, 'adminIndex'])->name('media');
    Route::get('/media/upload', [App\Http\Controllers\MediaController::class, 'adminUpload'])->name('upload');
    Route::post('/media', [App\Http\Controllers\MediaController::class, 'store'])->name('media.store');
    Route::get('/media/{media}/edit', [App\Http\Controllers\MediaController::class, 'edit'])->name('media.edit');
    Route::put('/media/{media}', [App\Http\Controllers\MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{media}', [App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
