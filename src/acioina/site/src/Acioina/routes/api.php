<?php

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
Route::group(['namespace' => 'Api'], function () {

    //https://github.com/spatie/laravel-cors/issues/28
    // add OPTIONS route to fire cors middleware for preflight
    // See getRouteForMethods form AbstractRouteCollection for
    Route::options('{any}');

    Route::post('users/login', 'AuthController@login');
    Route::post('users', 'AuthController@register');

    Route::get('user', 'UserController@index');
    Route::match(['put', 'patch'], 'user', 'UserController@update');
    Route::post('user', 'UserController@login');

    Route::get('profiles/{user}', 'ProfileController@show');
    Route::get('tags', 'TagController@index');
    Route::get('version', 'VersionController@index');

    Route::post('articles', 'ArticleController@index');

});

Route::group(['namespace' => 'Api\Admin',
              'prefix' => config('expendable.admin_base_uri'),
            ],function () {

    Route::get('tags', 'AdminController@index');

});