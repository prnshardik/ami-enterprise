<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api', 'namespace' => 'API'], function () {
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('logout', 'AuthController@logout');
        
        /** products */
            Route::get('products', 'ProductsController@products');
            Route::get('product/{id}', 'ProductsController@product');
            Route::post('product/insert', 'ProductsController@insert');
            Route::post('product/update', 'ProductsController@update');
            Route::post('product/delete', 'ProductsController@delete');
        /** products */

        /** customers */
            Route::get('customers', 'CustomersController@customers');
            Route::get('customer/{id}', 'CustomersController@customer');
            Route::post('customer/insert', 'CustomersController@insert');
            Route::post('customer/update', 'CustomersController@update');
            Route::post('customer/change-status', 'CustomersController@change_status');
        /** customers */

        /** tasks */
            Route::get('tasks', 'TasksController@tasks');
            Route::get('task/{id}', 'TasksController@task');
            Route::post('task/insert', 'TasksController@insert');
            Route::post('task/update', 'TasksController@update');
            Route::post('task/change-status', 'TasksController@change_status');
        /** tasks */

        /** my-tasks */
            Route::get('mytasks', 'MyTasksController@tasks');
            Route::get('mytask/{id}', 'MyTasksController@task');
            Route::post('mytask/change-status', 'MyTasksController@change_status');
        /** my-tasks */

        /** order */
            Route::get('orders', 'OrdersController@orders');
            Route::get('orders-pending', 'OrdersController@pending_orders');
            Route::get('orders-comepleted', 'OrdersController@completed_orders');
            Route::get('orders-delivered', 'OrdersController@delivered_orders');
            Route::get('order/{id}', 'OrdersController@order');
            Route::post('order/insert', 'OrdersController@insert');
            Route::post('order/update', 'OrdersController@update');
            Route::post('order/change-status', 'OrdersController@change_status');
            Route::post('order/item-delete', 'OrdersController@item_delete');
            Route::post('order/deliver', 'OrdersController@deliver');

            Route::post('order/customer', 'OrdersController@customer');
        /** order */

        /** users */
            Route::get('users', 'UsersController@users');
            Route::post('users/insert', 'UsersController@insert');
            Route::get('users/view/{id?}', 'UsersController@view');
            Route::post('users/update', 'UsersController@update');
            Route::post('users/change-status', 'UsersController@change_status');
        /** users */

        /** notice */
            Route::get('notice', 'NoticeController@notice');
            Route::post('notice/insert', 'NoticeController@insert');
            Route::get('notice/view/{id?}', 'NoticeController@view');
            Route::post('notice/update', 'NoticeController@update');
            Route::post('notice/change-status', 'NoticeController@change_status');
        /** notice */

        /** payments */
            Route::get('payments', 'PaymentController@index');
            Route::get('payments/detail/{party_name?}', 'PaymentController@detail');
            Route::get('payments/users', 'PaymentController@users');
            Route::post('payment/assign', 'PaymentController@assign');
        /** payments */

        /** payments-reminder */
            Route::get('payments-reminder', 'PaymentReminderController@index');
            Route::get('payments-reminder/followup-detail/{party_name?}', 'PaymentReminderController@followup_detail');
            Route::get('payments-reminder/payment-detail/{party_name?}', 'PaymentReminderController@payment_detail');
            Route::post('payments-reminder/insert', 'PaymentReminderController@insert');
            Route::post('payments-reminder/change-status', 'PaymentReminderController@change_status');
        /** payments-reminder */
    });
});

Route::get('/unauthenticated', function () {
    return response()->json(['status' => 201, 'message' => 'Unacuthorized Access']);
})->name('api.unauthenticated');

Route::get("{path}", function(){ return response()->json(['status' => 500, 'message' => 'Bad request']); })->where('path', '.+');
