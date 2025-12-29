<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/prestations', function () {
    return Inertia::render('Services', [
        'services' => \App\Models\Service::orderBy('order_index')->get()
    ]);
})->name('services');

Route::get('/a-propos', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    // Get Services for the contact page dropdown/accordion
    $services = \App\Models\Service::orderBy('category')->get();
    return Inertia::render('Contact', [
        'services' => $services
    ]);
})->name('contact');

Route::get('/portfolio', function () {
    return Inertia::render('Portfolio');
})->name('portfolio');

Route::get('/studio', function () {
    return Inertia::render('Studio');
})->name('studio');

Route::post('/appointments/slots', [App\Http\Controllers\AppointmentController::class, 'getSlots'])->name('appointments.slots');
Route::post('/appointments', [App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');

// Temporary Debug Route for Email - REMOVED

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
