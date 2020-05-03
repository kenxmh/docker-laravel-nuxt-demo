<?php

use Illuminate\Http\Request;

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

Route::prefix('api')->group(function () {
    Route::prefix('b1')->group(function () {
        Route::group(['namespace' => 'Admin'], function () {
            // Route::post('auth/register', 'UserController@register');
            Route::post('auth/login', 'UserController@login');
            Route::post('auth/logout', 'UserController@logout');
            
            Route::group(['middleware' => ['jwt.role:admin']], function () {
                Route::get('auth/user', 'UserController@user');

                Route::group(['middleware' => ['permission.admin']], function () {
                    // 图片上传
                    Route::post('images', 'ImageController@store');

                    Route::get('admins', 'UserController@index');
                    Route::post('admins', 'UserController@store');
                    Route::get('admins/{id}', 'UserController@show');
                    Route::put('admins/{id}', 'UserController@update');
                    Route::delete('admins/{id}', 'UserController@destroy');
                    Route::put('admins/{id}/password', 'UserController@resetPassword');
                    Route::put('admins/{id}/role', 'UserController@resetRole');

                    Route::get('roles', 'RoleController@index');
                    Route::post('roles', 'RoleController@store');
                    Route::get('roles/{id}', 'RoleController@show');
                    Route::put('roles/{id}', 'RoleController@update');
                    Route::delete('roles/{id}', 'RoleController@destroy');
                    Route::get('roles/{id}/accesses', 'RoleController@getAccesses');

                    Route::get('accesses', 'AccessController@index');
                    Route::post('accesses', 'AccessController@store');
                    Route::get('accesses/{id}', 'AccessController@show');
                    Route::put('accesses/{id}', 'AccessController@update');
                    Route::delete('accesses/{id}', 'AccessController@destroy');

                    Route::get('articles', 'ArticleController@index');
                    Route::post('articles', 'ArticleController@store');
                    Route::get('articles/{id}', 'ArticleController@show');
                    Route::put('articles/{id}', 'ArticleController@update');
                    Route::delete('articles/{id}', 'ArticleController@destroy');

                    Route::get('categories', 'CategoryController@index');
                    Route::post('categories', 'CategoryController@store');
                    // Route::get('categories/{id}', 'CategoryController@show');
                    Route::put('categories/{id}', 'CategoryController@update');
                    Route::delete('categories/{id}', 'CategoryController@destroy');
                });
            });
            
        });
    });

    Route::prefix('f1')->group(function () {
        Route::group(['namespace' => 'Common'], function () {
            Route::get('articles', 'ArticleController@index');
            Route::get('articles/{uuid}', 'ArticleController@show');
            Route::get('articles/{uuid}/comments', 'CommentController@index');
            Route::post('articles/{uuid}/comments', 'CommentController@store');
            Route::get('categories', 'CategoryController@index');
            Route::get('stats', 'StatController@index');
        });
    });
});