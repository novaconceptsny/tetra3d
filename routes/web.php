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

Route::view('/', 'dashboard')->name('home');
Route::view('tour', 'tour')->name('tour');
Route::view('editor', 'editor')->name('editor');
Route::view('gallery', 'gallery')->name('gallery');
Route::view('walls', 'walls')->name('walls');
