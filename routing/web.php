<?php

use Core\Route;

// --- Rutas Públicas / Login ---
Route::get('login', 'AuthController@login')->name('login')->middleware('guest');
Route::post('login', 'AuthController@login')->middleware('guest');
Route::post('logout', 'AuthController@logout')->name('logout')->middleware('auth');

// --- RUTAS SUPERADMIN ---
Route::group(['prefix' => 'superadmin', 'middleware' => ['auth', 'superadmin']], function () {

    Route::get('dashboard', 'Superadmin\\DashboardController@index')->name('superadmin.dashboard');
    Route::get('client/edit', 'Superadmin\\ClientController@edit')->name('superadmin.client.edit');
    Route::post('client/update', 'Superadmin\\ClientController@update')->name('superadmin.client.update');

    // Route::get('admins', 'Superadmin\\AdminManagementController@index')->name('superadmin.admins.index');
    // Route::get('admins/create', 'Superadmin\\AdminManagementController@create')->name('superadmin.admins.create');
    // Route::post('admins', 'Superadmin\\AdminManagementController@store')->name('superadmin.admins.store');

});


// --- RUTAS ADMIN ---

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    
    Route::get('dashboard', 'Admin\\AdminController@dashboard')->name('admin.dashboard');
    Route::get('wikis/create', 'App\Controllers\Admin\WikiController@create')->name('admin.wikis.create'); 
    Route::post('wikis', 'App\Controllers\Admin\WikiController@store')->name('admin.wikis.store');
    Route::get('wikis', 'App\Controllers\Admin\WikiController@index')->name('admin.wikis.index');
    Route::get('wikis/{uuid}/edit', 'App\Controllers\Admin\WikiController@edit')->name('admin.wikis.edit');
    Route::post('wikis/{uuid}', 'App\Controllers\Admin\WikiController@update')->name('admin.wikis.update');
    Route::post('wikis/{uuid}/delete', 'App\Controllers\Admin\WikiController@delete')->name('admin.wikis.delete');
    Route::post('wikis/{uuid}/restore', 'App\Controllers\Admin\WikiController@restore')->name('admin.wikis.restore');

});


// --- RUTAS USER ---
Route::group(['prefix' => 'user', 'middleware' => ['auth']], function () {

    Route::get('dashboard', 'UserController@index')->name('user.dashboard');
    // Route::get('dashboard', 'User\\DashboardController@index')->name('user.dashboard');
    // Route::get('wikis', 'WikiController@userIndex')->name('user.wikis.index');
    // Route::get('wikis/{uuid}', 'WikiController@show')->name('user.wikis.show');

});

// --- Ruta de Inicio Pública ---
// Esta ruta es a donde se redirige por defecto si no hay sesión activa.
Route::get('/', 'HomeController@index')->name('home');