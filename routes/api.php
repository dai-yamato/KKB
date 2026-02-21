<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\SystemAdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth
Route::post('/login', [SettingController::class, 'login']);

// Invitations
Route::post('/invitations/generate', [InvitationController::class, 'generate']);
Route::get('/invitations/{token}', [InvitationController::class, 'show']);
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

// Transactions
Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

// Budgets
Route::get('/budgets', [BudgetController::class, 'index']);
Route::post('/budgets', [BudgetController::class, 'store']);

// Settings / Household
Route::post('/reset', [SettingController::class, 'reset']);
Route::get('/households', [SettingController::class, 'households']);
Route::put('/households', [SettingController::class, 'updateHousehold']);
Route::get('/households/{household}/users', [SettingController::class, 'users']);
Route::post('/households/users', [SettingController::class, 'addUser']);
Route::put('/users/{user}/role', [SettingController::class, 'updateRole']);
Route::delete('/users/{user}', [SettingController::class, 'deleteUser']);

// System Admin
Route::post('/system-admin/login', [SystemAdminController::class, 'login']);
Route::get('/system-admin/logs', [SystemAdminController::class, 'getLogs']);
Route::get('/system-admin/categories', [SystemAdminController::class, 'getDefaultCategories']);
Route::put('/system-admin/categories', [SystemAdminController::class, 'updateDefaultCategories']);
