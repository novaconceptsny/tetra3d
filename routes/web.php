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
Route::view('walls', 'pages.walls')->name('walls');

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'HomeController@index')->name('dashboard');
    Route::get('tour/{id}', 'TourController@index')->name('tour.index');
    Route::get('editor', 'CanvasController@index')->name('editor');
    Route::get('artworks', 'ArtworksController@index')->name('artworks.index');
});


Route::group([
    'middleware' => 'auth',
    'prefix' => 'backend',
    'namespace' => 'Backend',
    'as' => 'backend.',
], function () {

    Route::controller('SpotConfigurationController')->group(function () {
        Route::get('spot-configuration/{spot}', 'show')
            ->name('spot-configuration.show');
        Route::get('spot-configuration/{spot}/edit', 'edit')
            ->name('spot-configuration.edit');
        Route::put('spot-configuration/{spot}', 'update')
            ->name('spot-configuration.update');
    });


    Route::resource('projects', 'ProjectController');
    Route::resource('tours', 'TourController');
    Route::resource('tours.spots', 'SpotController')->shallow();
    Route::resource('tours.surfaces', 'SurfaceController')
        ->shallow();
});
