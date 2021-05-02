<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
        Route::get('/page', function () { return view('page'); })->name('page');
    });

    Route::get("{path}", function(){ return redirect()->route('login'); })->where('path', '.+');
});