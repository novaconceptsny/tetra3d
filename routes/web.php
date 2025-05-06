<?php

use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\SurfaceStateController;
use App\Http\Controllers\PhotoStateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;

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
    Route::post('artworks/destroy/{id}', 'ArtworksController@destroyCollection')->name('artworks.destroyCollection');
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
        Route::post('surfaces/destroy/{id}', 'SurfaceStateController@destroySurface')->name('surfaces.destroy');
    });

    Route::controller(PhotoStateController::class)->group(function () {
        Route::get('photos/{photo}', 'show')->name('photos.show');
        Route::post('photos/{photo}', 'update')->name('photos.update');
    });

    Route::get('/tour-360', 'Tour360Controller@index')->name('tour-360.index');

    Route::controller(PhotoController::class)->group(function () {
        Route::get('/photo', 'index')->name('photo.index');
        Route::post('photo/destroy/{id}', 'destroy')->name('photo.destroy');
        Route::post('/photo/store', 'store')->name('photo.store');
        Route::post('/photo/{photo}', 'update')->name('photo.update');
        Route::post('/photo-state/store', 'storePhotoState')->name('photo.state.store');
        Route::post('/projects/{project}/collections/update', 'updateCollections')->name('projects.collections.update');
        Route::post('/photo/surface/store', 'storeSurface')->name('photo.surface.store');
        Route::post('/photo/{id}/edit', 'edit')->name('photo.edit');
    });

    Route::post('project/update/{id}', 'ProjectController@update')->name('project.update');

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


