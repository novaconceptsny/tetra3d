<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Sculpture save/delete/load/canvas-image routes moved to routes/web.php (group "sculpture-placements"),
// because they are called from the logged-in browser and need the web session + CSRF. See CHANGELOG 2026-10-09.

