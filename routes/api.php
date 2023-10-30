<?php

use App\Http\Controllers\Api\Admin\ApartaturController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\PageController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Controllers\Api\Admin\PhotoController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\LoginContoller;
use App\Http\Controllers\Api\Public\AparaturController as PublicAparaturController;
use App\Http\Controllers\Api\Public\PageController as PublicPageController;
use App\Http\Controllers\Api\Public\PhotoController as PublicPhotoController;
use App\Http\Controllers\Api\Public\PostController as PublicPostController;
use App\Http\Controllers\Api\Public\ProductController as PublicProductController;
use App\Http\Controllers\Api\Public\SliderController as PublicSliderController;
use Illuminate\Support\Facades\Route;


// Route login
Route::post('/login',           [LoginContoller::class, 'index']);

Route::group(['middleware' => 'auth:api'], function () {
    // Logout
    Route::post('/logout',      [LoginContoller::class, 'logout']);
});

// Route with prefix "admin"
Route::prefix('admin')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/dashboard', DashboardController::class);


        // Permissions
        Route::get('/permissions',              [PermissionController::class, 'index'])->middleware('permission:permissions.index');
        Route::get('/permissions/all',          [PermissionController::class, 'all'])->middleware('permission:permissions.index');

        // Roles
        Route::get('/roles/all',                [RoleController::class, 'all'])->middleware('permission:roles.index');
        Route::apiResource('/roles',            RoleController::class)->middleware('permission:roles.index|roles.store|roles.update|roles.delete');

        // users
        Route::apiResource('/users',            UserController::class)->middleware('permission:users.index|users.store|users.update|users.delete');

        // categories
        Route::get('categories/all',            [CategoryController::class, 'all'])->middleware('permission:categories.index');
        Route::apiResource('/categories',       CategoryController::class)->middleware('permission:categories.index|categories.store|categories.update|categories.delete');

        // posts
        Route::apiResource('/posts',            PostController::class)->middleware('permission:posts.index|posts.store|posts.update|posts.delete');

        // products
        Route::apiResource('/products',         ProductController::class)->middleware('permission:products.index|products.store|products.update|products.delete');

        // pages
        Route::apiResource('/pages',            PageController::class)->middleware('permission:pages.index|pages.store|pages.update|pages.delete');

        // photos
        Route::apiResource('/photos',           PhotoController::class, ['except' => ['show', 'update']])->middleware('permission:photos.index|photos.store|photos.delete');

        // slider
        Route::apiResource('/sliders',          SliderController::class, ['except' => ['show', 'update']])->middleware('permission:sliders.index|sliders.store|slider.delete');

        // aparaturs
        Route::apiResource('/aparaturs',        ApartaturController::class)->middleware('permission:aparaturs.index|aparaturs.store|aparaturs.update|aparaturs.delete');
    });
});

// Route with prefix "public"
Route::prefix('public')->group(function () {
    // public posts
    Route::get('/posts',                [PublicPostController::class, 'index']);
    Route::get('/posts/{slug}',         [PublicPostController::class, 'show']);
    Route::get('/posts_home',           [PublicPostController::class, 'homePage']);

    // public products
    Route::get('/products',             [PublicProductController::class, 'index']);
    Route::get('/products/{slug}',      [PublicProductController::class, 'show']);
    Route::get('/products_home',        [PublicProductController::class, 'homePage']);

    // public page
    Route::get('/pages',                [PublicPageController::class, 'index']);
    Route::get('/pages/{slug}',         [PublicPageController::class, 'show']);
    Route::get('/pages_home',           [PublicPageController::class, 'homePage']);

    // public aparaturs
    Route::get('/aparaturs',            [PublicAparaturController::class, 'index']);

    // public photos
    Route::get('/photos',               [PublicPhotoController::class, 'index']);

    // public slider
    Route::get('/sliders',              [PublicSliderController::class, 'index']);
});
