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

//Authenticate
Route::post('register', 'Users\LoginController@register');
Route::post('login', 'Users\LoginController@login');
Route::get('logout', 'Users\LoginController@logout');
Route::post('recover', 'Users\LoginController@recover');
Route::get('user', 'Users\LoginController@user_data');

