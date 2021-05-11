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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api', 'namespace' => 'API'], function () {
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('logout', 'AuthController@logout');

        /** products */
            Route::get('products', 'ProductsController@products');
            Route::get('product/{id}', 'ProductsController@product');
            Route::post('product/insert', 'ProductsController@insert');
            Route::post('product/delete', 'ProductsController@delete');
        /** products */

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
            Route::post('mytask/insert', 'MyTasksController@insert');
            Route::post('mytask/update', 'MyTasksController@update');
            Route::post('mytask/change-status', 'MyTasksController@change_status');
        /** my-tasks */

        /** order */
            Route::get('orders', 'OrdersController@orders');
            Route::get('orders-pending', 'OrdersController@pending_orders');
            Route::get('orders-comepleted', 'OrdersController@completed_orders');
            Route::get('order/{id}', 'OrdersController@order');
            Route::post('order/insert', 'OrdersController@insert');
            Route::post('order/update', 'OrdersController@update');
            Route::post('order/change-status', 'OrdersController@change_status');
            Route::post('order/item-delete', 'OrdersController@item_delete');
        /** order */

         /** Users */
            Route::get('users', 'UsersController@users');
            Route::post('users/insert', 'UsersController@insert');
            Route::get('users/view/{id?}', 'UsersController@view');
            Route::post('users/update', 'UsersController@update');
            Route::post('users/change-status', 'UsersController@change_status');
        /** Users */

         /** Notice */
            Route::get('notice', 'NoticeController@notice');
            Route::post('notice/insert', 'NoticeController@insert');
            Route::get('notice/view/{id?}', 'NoticeController@view');
            Route::post('notice/update', 'NoticeController@update');
            Route::post('notice/change-status', 'NoticeController@change_status');
        /** Notice */
    });
});

Route::get("{path}", function(){ return response()->json(['status' => 500, 'message' => 'Bad request']); })->where('path', '.+');

Route::get('/unauthenticated', function () {
    return response()->json(['status' => 201, 'message' => 'Unacuthorized Access']);
})->name('api.unauthenticated');