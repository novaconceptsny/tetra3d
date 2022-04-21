<?php

use Illuminate\Support\Facades\Route;

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

Route::view('editor', 'pages.editor')->name('editor');
Route::view('gallery', 'pages.gallery')->name('gallery');
Route::view('walls', 'pages.walls')->name('walls');

Auth::routes();

Route::group(['middleware' => 'auth'], function (){
    Route::get('/', 'HomeController@index')->name('dashboard');
    Route::get('tour/{id}', 'TourController@index')->name('tour.index');
    Route::get('editor', 'CanvasController@index')->name('editor');
});
