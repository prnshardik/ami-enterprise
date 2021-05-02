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


Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');
    });

    Route::group(['middleware' => ['auth'] , 'namespace' => 'Admin', 'as' => 'admin.', 'prefix' => 'admin'], function () {
        Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
        Route::get('/page', function () { return view('page'); })->name('page');
        Route::get('/page', function () { return view('page'); })->name('page');

        /** Users */
            Route::any('users', 'UsersController@index')->name('users');
            Route::get('users/create', 'UsersController@create')->name('users.create');
            Route::post('users/insert', 'UsersController@insert')->name('users.insert');
            Route::get('users/view/{id?}', 'UsersController@view')->name('users.view');
            Route::get('users/edit/{id?}', 'UsersController@edit')->name('users.edit');
            Route::patch('users/update', 'UsersController@update')->name('users.update');
            Route::post('users/change-status', 'UsersController@change_status')->name('users.change.status');
        /** Users */

    });

    Route::get("{path}", function () { return view('dashboard'); })->where('path', '.+');
});