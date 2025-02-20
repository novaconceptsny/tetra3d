<?php

use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\SurfaceStateController;
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


Auth::routes();
Route::group(['middleware' => 'auth'], function () {

    Route::post('login-as/{user}', [UserController::class, 'loginAs'])->name('login.as.user');
    Route::post('back-to-admin', [UserController::class, 'backToAdmin'])->name('back.to.admin');

    Route::get('/', 'HomeController@index')->name('dashboard');
    Route::get('tours/{tour}', 'TourController@show')->name('tours.show')->withoutMiddleware(['auth']);
    Route::get('tours/{tour}/surfaces', 'TourController@surfaces')->name('tours.surfaces');
    Route::get('artworks', 'ArtworksController@index')->name('artworks.index');
    Route::get('inventory', 'InventoryController@index')->name('inventory.index');
    Route::get('/profile/edit', 'ProfileController@edit')->name('profile.edit');
    Route::post('/profile/edit', 'ProfileController@update')->name('profile.update');
    Route::post('/profile/password', 'ProfileController@updatePassword')->name('profile.password.update');
    Route::get('/activity', 'ActivityController@index')->name('activity.index');

    //shared tours
    Route::get('shared-tours/{shared_tour}', 'SharedTourController@show')->name('shared-tours.show')->withoutMiddleware(['auth']);

    Route::controller(SurfaceStateController::class)->group(function () {
        Route::get('surfaces/{surface}', 'show')->name('surfaces.show');
        Route::post('surfaces/{surface}', 'store')->name('surfaces.store');
        Route::post('surfaces/{surface}', 'update')->name('surfaces.update');

        // surface state
        Route::get('surfaces/{state}/active', 'SurfaceStateController@active')->name('surfaces.active');
        Route::delete('surfaces/{state}', 'destroy')->name('surfaces.destroy');
    });

    Route::get('/surface-states/create', [SurfaceStateController::class, 'create'])->name('surface-states.create');
});


Route::group([
    'middleware' => ['auth', 'can:access-backend'],
    'prefix' => 'backend',
    'namespace' => 'Backend',
    'as' => 'backend.',
], function () {

    Route::redirect('/dashboard', '/backend/projects')->name('dashboard');

    Route::view('/collector-sync-report', 'report')->name('collector.report');

    //Route::resource('spot-configuration', 'SpotConfigurationController');
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
    Route::resource('sculptures', 'SculptureController');
    Route::resource('artworks', 'ArtworkController');
    Route::resource('artwork-collections', 'ArtworkCollectionController')
    ->parameter('artwork-collections', 'collection');
    Route::resource('tours.spots', 'SpotController')->shallow();
    Route::resource('tours.surfaces', 'SurfaceController')
        ->shallow();

    Route::patch('/tours/{tour}/toggle-model', 'TourController@toggleModel')
        ->name('backend.tours.toggle-model');
});
