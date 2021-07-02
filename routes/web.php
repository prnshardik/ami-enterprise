<?php

use Illuminate\Support\Facades\Route;

Route::get('clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "config, cache, and view cleared successfully";
});

Route::get('key-generate', function() {
    Artisan::call('key:generate');
    return "Key generate successfully";
});

Route::get('migrate', function() {
    Artisan::call('migrate:refresh');
    return "Database migration generated";
});

Route::get('seed', function() {
    Artisan::call('db:seed');
    return "Database seeding generated";
});

Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');

        Route::get('forget-password', 'AuthController@forget_password')->name('forget.password');
        Route::post('password-forget', 'AuthController@password_forget')->name('password.forget');
        Route::get('reset-password/{string}', 'AuthController@reset_password')->name('reset.password');
        Route::post('recover-password', 'AuthController@recover_password')->name('recover.password');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

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
            Route::get('products/view/{id?}', 'ProductsController@view')->name('products.view');
            Route::get('products/edit/{id?}', 'ProductsController@edit')->name('products.edit');
            Route::patch('products/update', 'ProductsController@update')->name('products.update');
            Route::get('products/delete/{id?}', 'ProductsController@delete')->name('products.delete');
        /** Products */

        /** customers */
            Route::any('customers', 'CustomerController@index')->name('customers');
            Route::get('customers/create', 'CustomerController@create')->name('customers.create');
            Route::post('customers/insert', 'CustomerController@insert')->name('customers.insert');
            Route::get('customers/view/{id?}', 'CustomerController@view')->name('customers.view');
            Route::get('customers/edit/{id?}', 'CustomerController@edit')->name('customers.edit');
            Route::patch('customers/update', 'CustomerController@update')->name('customers.update');
            Route::post('customers/change-status', 'CustomerController@change_status')->name('customers.change.status');

            Route::post('customers/insert-ajax', 'CustomerController@insert_ajax')->name('customers.insert.ajax');
        /** customers */

        /** Notice */
            Route::any('notices', 'NoticesController@index')->name('notices');
            Route::get('notices/create', 'NoticesController@create')->name('notices.create');
            Route::post('notices/insert', 'NoticesController@insert')->name('notices.insert');
            Route::get('notices/edit/{id?}', 'NoticesController@edit')->name('notices.edit');
            Route::patch('notices/update', 'NoticesController@update')->name('notices.update');
            Route::post('notices/change-status', 'NoticesController@change_status')->name('notices.change.status');
        /** Notice */

        /** Notice-Board */
            Route::get('notice-board', 'NoticesController@notices_board')->name('notice.board');
        /** Notice-Board */ 

        /** Tasks */
            Route::any('tasks', 'TasksController@index')->name('tasks');
            Route::get('tasks/create', 'TasksController@create')->name('tasks.create');
            Route::post('tasks/insert', 'TasksController@insert')->name('tasks.insert');
            Route::get('tasks/view/{id?}', 'TasksController@view')->name('tasks.view');
            Route::get('tasks/edit/{id?}', 'TasksController@edit')->name('tasks.edit');
            Route::patch('tasks/update', 'TasksController@update')->name('tasks.update');
            Route::post('tasks/change-status', 'TasksController@change_status')->name('tasks.change.status');
        /** Tasks */

        /** My-Tasks */
            Route::any('mytasks', 'MyTasksController@index')->name('mytasks');
            Route::get('mytasks/create', 'MyTasksController@create')->name('mytasks.create');
            Route::post('mytasks/insert', 'MyTasksController@insert')->name('mytasks.insert');
            Route::get('mytasks/view/{id?}', 'MyTasksController@view')->name('mytasks.view');
            Route::get('mytasks/edit/{id?}', 'MyTasksController@edit')->name('mytasks.edit');
            Route::patch('mytasks/update', 'MyTasksController@update')->name('mytasks.update');
            Route::post('mytasks/change-status', 'MyTasksController@change_status')->name('mytasks.change.status');
        /** My-Tasks */

        /** orders */
            Route::any('orders', 'OrdersController@index')->name('orders');
            Route::get('orders/create/{customer_id?}', 'OrdersController@create')->name('orders.create');
            Route::post('orders/insert', 'OrdersController@insert')->name('orders.insert');
            Route::get('orders/view/{id?}', 'OrdersController@view')->name('orders.view');
            Route::get('orders/edit/{id?}', 'OrdersController@edit')->name('orders.edit');
            Route::patch('orders/update', 'OrdersController@update')->name('orders.update');
            Route::post('orders/change-status', 'OrdersController@change_status')->name('orders.change.status');

            Route::post('orders/delete-detail', 'OrdersController@delete_detail')->name('orders.delete.detail');
            Route::get('orders/select-customer', 'OrdersController@select_customer')->name('orders.select.customer');
        /** orders */

        /** payment */
            Route::any('payments', 'PaymentController@index')->name('payment');
            Route::get('payment/import', 'PaymentController@file_import')->name('payment.import.file');
            Route::post('payment/import', 'PaymentController@import')->name('payment.import');
            Route::post('payment/assign', 'PaymentController@assign')->name('payment.assign');
        /** payment */
    });

    Route::get("{path}", function(){ return redirect()->route('login'); })->where('path', '.+');
});