<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/input', function () {
    return view('input');
})->name('input');

Route::get('/list', function () {
    return view('list');
})->name('list');

Route::get('/budget', function () {
    return view('budget');
})->name('budget');

Route::get('/comparison', function () {
    return view('comparison');
})->name('comparison');

Route::get('/stats', function () {
    return view('stats');
})->name('stats');

Route::get('/system-admin', function () {
    return view('system_admin');
})->name('system_admin');

Route::get('/system-admin/login', function () {
    return view('system_admin_login');
})->name('system_admin_login');

Route::get('/calendar', function () {
    return view('calendar');
})->name('calendar');

Route::get('/categories', function () {
    return view('categories');
})->name('categories');

Route::get('/settings', function () {
    return view('settings');
})->name('settings');
