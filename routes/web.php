<?php

use Illuminate\Support\Facades\Route;

Route::get('command', function() {
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    return "Command Successfully";
});

Route::get('key-generate', function() {
    Artisan::call('key:generate');
    return "Key Generate Successfully";
});

Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

        /** Users */
            Route::any('users', 'UsersController@index')->name('users');
            Route::get('users/create', 'UsersController@create')->name('users.create');
            Route::post('users/insert', 'UsersController@insert')->name('users.insert');
            Route::get('users/view/{id?}', 'UsersController@view')->name('users.view');
            Route::get('users/edit/{id?}', 'UsersController@edit')->name('users.edit');
            Route::patch('users/update', 'UsersController@update')->name('users.update');
            Route::post('users/change-status', 'UsersController@change_status')->name('users.change.status');
        /** Users */

        /** Products */
            Route::any('products', 'ProductsController@index')->name('products');
            Route::get('products/create', 'ProductsController@create')->name('products.create');
            Route::post('products/insert', 'ProductsController@insert')->name('products.insert');
            Route::get('products/edit/{id?}', 'ProductsController@edit')->name('products.edit');
            Route::patch('products/update', 'ProductsController@update')->name('products.update');
            Route::get('products/delete/{id?}', 'ProductsController@delete')->name('products.delete');
        /** Products */

        /** Task */
            Route::any('task', 'TaskController@index')->name('task');
            Route::get('task/create', 'TaskController@create')->name('task.create');
            Route::post('task/insert', 'TaskController@insert')->name('task.insert');
            Route::get('task/view/{id?}', 'TaskController@view')->name('task.view');
            Route::get('task/edit/{id?}', 'TaskController@edit')->name('task.edit');
            Route::patch('task/update', 'TaskController@update')->name('task.update');
            Route::get('task/delete/{id?}', 'TaskController@delete')->name('task.delete');
        /** Task */
    });

    Route::get("{path}", function(){ return redirect()->route('login'); })->where('path', '.+');
});