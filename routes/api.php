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

Route::post('/sculpture_save', 'SculptureController@save')->name('sculpture_save');
Route::post('/sculpture_delete', 'SculptureController@delete')->name('sculpture_delete');
Route::post('/sculpture_load','SculptureController@load')->name('sculpture_load');
Route::post('/sculpture_store_canvas_image', 'Backend/SculptureController@store_canvas_image')->name('store_canvas_image');

