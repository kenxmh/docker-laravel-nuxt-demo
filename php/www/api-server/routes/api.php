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
            Route::post('auth/login', 'AuthController@login');
// 统计
Route::get('stats', 'StatController@index');
            // 后台用户
            Route::group(['middleware' => ['jwt.role:admin']], function () {
                Route::get('auth/user', 'AuthController@user');
                Route::post('auth/logout', 'AuthController@logout');

                // 后台用户且拥有相应权限
                Route::group(['middleware' => ['permission.admin']], function () {

                    Route::put('auth/user', 'AuthController@updateUser');

                    

                    // 图片上传
                    Route::post('images', 'ImageController@store');

                    // 后台用户管理
                    Route::get('admins', 'UserController@index');
                    Route::post('admins', 'UserController@store');
                    Route::get('admins/{id}', 'UserController@show');
                    Route::put('admins/{id}', 'UserController@update');
                    Route::delete('admins/{id}', 'UserController@destroy');
                    Route::put('admins/{id}/password', 'UserController@resetPassword');
                    Route::put('admins/{id}/role', 'UserController@resetRole');

                    // 后台角色管理
                    Route::get('roles', 'RoleController@index');
                    Route::post('roles', 'RoleController@store');
                    Route::get('roles/{id}', 'RoleController@show');
                    Route::put('roles/{id}', 'RoleController@update');
                    Route::delete('roles/{id}', 'RoleController@destroy');
                    Route::get('roles/{id}/accesses', 'RoleController@getAccesses');

                    // 后台路由规则管理
                    Route::get('accesses', 'AccessController@index');
                    Route::post('accesses', 'AccessController@store');
                    Route::get('accesses/{id}', 'AccessController@show');
                    Route::put('accesses/{id}', 'AccessController@update');
                    Route::delete('accesses/{id}', 'AccessController@destroy');

                    // 文章管理
                    Route::get('articles', 'ArticleController@index');
                    Route::post('articles', 'ArticleController@store');
                    Route::get('articles/{id}', 'ArticleController@show');
                    Route::put('articles/{id}', 'ArticleController@update');
                    Route::delete('articles/{id}', 'ArticleController@destroy');

                    // 文章分类管理
                    Route::get('categories', 'CategoryController@index');
                    Route::post('categories', 'CategoryController@store');
                    // Route::get('categories/{id}', 'CategoryController@show');
                    Route::put('categories/{id}', 'CategoryController@update');
                    Route::delete('categories/{id}', 'CategoryController@destroy');

                    // 文章评论管理
                    Route::get('comments', 'ArticleCommentController@index');
                    Route::post('comments', 'ArticleCommentController@store');

                });
            });

        });
    });

    Route::prefix('f1')->group(function () {
        Route::group(['namespace' => 'Common'], function () {
            Route::get('articles', 'ArticleController@index');
            Route::get('articles/{uuid}', 'ArticleController@show');
            Route::get('articles/{uuid}/comments', 'ArticleCommentController@index');
            Route::post('articles/{uuid}/comments', 'ArticleCommentController@store');
            Route::get('categories', 'CategoryController@index');
            Route::get('stats', 'StatController@index');
        });
    });
});
