<?php

use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SurfaceStateController;
use App\Http\Controllers\PhotoStateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\Tour360Controller;
use App\Http\Controllers\SharePageController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\ArtworksController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SharedTourController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Backend\SpotConfigurationController;
use App\Http\Controllers\Backend\CompanyController;
use App\Http\Controllers\Backend\ProjectController as BackendProjectController;
use App\Http\Controllers\Backend\TourController as BackendTourController;
use App\Http\Controllers\Backend\SculptureController;
use App\Http\Controllers\Backend\ArtworkController as BackendArtworkController;
use App\Http\Controllers\Backend\ArtworkCollectionController;
use App\Http\Controllers\Backend\SpotController;
use App\Http\Controllers\Backend\SurfaceController;

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

// Email Verification Routes
Route::get('/verify-email', [EmailVerificationController::class, 'showVerificationForm'])->name('verification.notice');
Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/verify-email/resend', [EmailVerificationController::class, 'resendVerificationCode'])->name('verification.resend');
Route::post('/verify-email/send', [EmailVerificationController::class, 'sendVerificationCode'])->name('verification.send');

Route::group(['middleware' => 'auth'], function () {

    Route::post('login-as/{user}', [UserController::class, 'loginAs'])->name('login.as.user');
    Route::post('back-to-admin', [UserController::class, 'backToAdmin'])->name('back.to.admin');

    // Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::redirect('/', '/tour-360')->name('dashboard');
    Route::get('tours/{tour}', [TourController::class, 'show'])->name('tours.show')->withoutMiddleware(['auth']);
    Route::get('tours/{tour}/surfaces', [TourController::class, 'surfaces'])->name('tours.surfaces');
    Route::get('artworks', [ArtworksController::class, 'index'])->name('artworks.index');
    Route::get('projects/{project}/artworks', [ArtworkController::class, 'getArtworks'])->name('artworks.get');
    Route::post('artworks/destroy/{id}', [ArtworksController::class, 'destroyCollection'])->name('artworks.destroyCollection');
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
    Route::get('inventory/export-pdf', [InventoryController::class, 'exportPdf'])->name('inventory.export-pdf');

    Route::get('inventory/datatable', [InventoryController::class, 'datatable'])->name('inventory.datatable');
    Route::get('inventory/data', [InventoryController::class, 'getData'])->name('inventory.data');
    Route::post('inventory/editor', [InventoryController::class, 'editor'])->name('inventory.editor');
    Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::post('inventory/bulk-store', [InventoryController::class, 'bulkStore'])->name('inventory.bulk-store');
    Route::post('inventory/bulk-copy', [InventoryController::class, 'bulkCopy'])->name('inventory.bulk-copy');
    Route::post('inventory/bulk-delete', [InventoryController::class, 'bulkDelete'])->name('inventory.bulk-delete');
    Route::post('inventory/bulk-update', [InventoryController::class, 'bulkUpdate'])->name('inventory.bulk-update');
    Route::put('inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    Route::post('inventory/collections/add', [InventoryController::class, 'addCollection'])->name('inventory.collections.add');
    Route::put('inventory/collections/{id}/edit', [InventoryController::class, 'editCollection'])->name('inventory.collections.edit');
    Route::delete('inventory/collections/{id}/delete', [InventoryController::class, 'deleteCollection'])->name('inventory.collections.delete');
    Route::get('inventory/collections/by-company', [InventoryController::class, 'getCollectionsByCompany'])->name('inventory.collections.by-company');
    Route::post('inventory/artworks/bulk-delete', [InventoryController::class, 'bulkDelete'])->name('inventory.artworks.bulk-delete');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

    //shared tours
    Route::get('shared-tours/{shared_tour}', [SharedTourController::class, 'show'])->name('shared-tours.show')->withoutMiddleware(['auth']);

    Route::controller(SurfaceStateController::class)->group(function () {
        Route::get('surfaces/{surface}', 'show')->name('surfaces.show');
        Route::post('surfaces/{surface}', 'store')->name('surfaces.store');
        Route::post('surfaces/{surface}', 'update')->name('surfaces.update');

        // surface state
        Route::get('surfaces/{state}/active', 'active')->name('surfaces.active');
        Route::delete('surfaces/{state}', 'destroy')->name('surfaces.destroy');
        Route::post('surfaces/destroy/{id}', 'destroySurface')->name('surfaces.destroy');
    });

    Route::controller(PhotoStateController::class)->group(function () {
        Route::get('photos/{photo}', 'show')->name('photos.show');
        Route::post('photos/{photo}', 'update')->name('photos.update');
    });

    Route::get('/resource', [ResourceController::class, 'index'])->name('resource.index');
    Route::post('/resource/assign-tour-to-companies', [ResourceController::class, 'assignTourToCompanies'])->name('resource.assignTourToCompanies');
    Route::post('/resource/remove-gallery', [ResourceController::class, 'removeGallery'])->name('resource.removeGallery');
    Route::post('/resource/videos/upload', [ResourceController::class, 'uploadVideo'])->name('resource.videos.upload');
    Route::delete('/resource/videos/{id}', [ResourceController::class, 'deleteVideo'])->name('resource.videos.delete');

    Route::controller(Tour360Controller::class)->group(function () {
        Route::get('/tour-360', 'index')->name('tour-360.index');
        Route::get('/tour360/create/{companyId}', 'create')->name('tour360.create');
        Route::get('/tour360/edit/{id}', 'edit')->name('tour360.edit');
        Route::post('/tour360/store', 'store')->name('tour360.store');
        Route::post('/tour360/update/{id}', 'update')->name('tour360.update');
        Route::post('/tour360/destroy/{id}', 'destroy')->name('tour360.destroy');
        Route::post('/tour360/toggle-favorite/{id}', 'toggleFavorite')->name('tour360.toggle-favorite');
    });

    Route::controller(PhotoController::class)->group(function () {
        Route::get('/photo', 'index')->name('photo.index');
        Route::post('/photo/{module}/destroy/{id}', 'destroy')->name('photo.destroy');
        Route::post('/photo/store', 'store')->name('photo.store');
        Route::post('/photo/{photo}', 'update')->name('photo.update');
        Route::post('/photo-state/store', 'storePhotoState')->name('photo.state.store');
        Route::post('/photo/collections/update', 'updateCollections')->name('photo.collections.update');
        Route::post('/photo/surface/store', 'storeSurface')->name('photo.surface.store');
        Route::post('/photo/{id}/edit', 'edit')->name('photo.edit');
        Route::post('/photo/{id}/toggle-favorite', 'toggleFavorite')->name('photo.toggle-favorite');
        Route::get('/photo/projects/{id}', 'getProject')->name('photo.projects.get');
        Route::post('/photos-store-project', 'storeProject')->name('photo.store-project');
        Route::post('/photos-update-project', 'updateProject')->name('photo.update-project');
    });

    Route::post('project/update/{id}', [ProjectController::class, 'update'])->name('project.update');

    Route::post('inventory/artworks/add', [InventoryController::class, 'addArtworks'])->name('inventory.artworks.add');

    Route::get('/share', [SharePageController::class, 'index'])->name('share.index');
    Route::post('/share/store', [SharePageController::class, 'store'])->name('share.store');
    Route::post('/share/{id}/toggle', [SharePageController::class, 'toggle'])->name('share.toggle');
    Route::post('/share/{id}/edit', [SharePageController::class, 'edit'])->name('share.edit');
    Route::delete('/share/{id}/delete', [SharePageController::class, 'destroy'])->name('share.delete');

});


Route::group([
    'middleware' => ['auth', 'can:access-backend'],
    'prefix' => 'backend',
    'as' => 'backend.',
], function () {

    Route::redirect('/dashboard', '/backend/projects')->name('dashboard');

    Route::view('/collector-sync-report', 'report')->name('collector.report');

    Route::controller(SpotConfigurationController::class)->group(function () {
        Route::get('spot-configuration/{spot}', 'show')
            ->name('spot-configuration.show');
        Route::get('spot-configuration/{spot}/edit', 'edit')
            ->name('spot-configuration.edit');
        Route::put('spot-configuration/{spot}', 'update')
            ->name('spot-configuration.update');
    });

    Route::resource('companies', CompanyController::class);
    Route::resource('users', UserController::class);
    Route::resource('projects', BackendProjectController::class);
    Route::resource('tours', BackendTourController::class);
    Route::resource('sculptures', SculptureController::class);
    Route::resource('artworks', BackendArtworkController::class);
    Route::resource('artwork-collections', ArtworkCollectionController::class)
        ->parameter('artwork-collections', 'collection');
    Route::resource('tours.spots', SpotController::class)->shallow();
    Route::resource('tours.surfaces', SurfaceController::class)->shallow();

    Route::post('/tours/regenerate-xml', [BackendTourController::class, 'reGenerateXML'])
        ->name('backend.tours.regenerate-xml');

    Route::patch('/tours/{tour}/toggle-model', [BackendTourController::class, 'toggleModel'])
        ->name('backend.tours.toggle-model');
});
