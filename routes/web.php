<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;

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

/*$process = new Process(['D:\krpano-1.20.11\krpanotools', 'makepano', 'D:\krpano-1.20.11\templates\krpano.config', 'D:\krpano-1.20.11\360\p48003.JPG']);
$process->run();
dd($process->getOutput());*/
/*\App\Helpers\ShellCommand::execute('D:\krpano-1.20.11\krpanotools makepano D:\krpano-1.20.11\templates\krpano.config D:\krpano-1.20.11\360\p48003.JPG');*/

Route::view('editor', 'pages.editor')->name('editor');
Route::view('walls', 'pages.walls')->name('walls');

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'HomeController@index')->name('dashboard');
    Route::get('tours/{tour}', 'TourController@show')->name('tours.show');
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


    Route::resource('companies', 'CompanyController');
    Route::resource('users', 'UserController');
    Route::resource('projects', 'ProjectController');
    Route::resource('tours', 'TourController');
    Route::resource('tours.spots', 'SpotController')->shallow();
    Route::resource('tours.surfaces', 'SurfaceController')
        ->shallow();
});
