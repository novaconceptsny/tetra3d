<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SculptureController;
use App\Http\Controllers\Backend\SculptureController as BackendSculptureController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Company API routes for registration form
Route::get('/companies/search', [App\Http\Controllers\Api\CompanyController::class, 'search']);
Route::post('/companies', [App\Http\Controllers\Api\CompanyController::class, 'store']);

Route::middleware(['auth:sanctum', 'can:perform-admin-actions'])->group(function () {
    Route::post('/sculpture_save', [SculptureController::class, 'save'])->name('sculpture_save');
    Route::post('/sculpture_delete', [SculptureController::class, 'delete'])->name('sculpture_delete');
    Route::post('/sculpture_load', [SculptureController::class, 'load'])->name('sculpture_load');
    Route::post('/sculpture_store_canvas_image', [BackendSculptureController::class, 'store_canvas_image'])->name('store_canvas_image');
});

