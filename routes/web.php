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

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::middleware(['auth', 'user'])->group(function () {
    Route::resource('/notification', 'App\Http\Controllers\NotificationController');
    Route::resource('/search', 'App\Http\Controllers\SearchController');
});

Route::group(['middleware' => ['auth','admin']], function () {
    Route::resource('/user', 'App\Http\Controllers\UserController');
    Route::post('/user/disable', 'App\Http\Controllers\UserController@disableUser')->name('user.disable');
    Route::post('/user/enable', 'App\Http\Controllers\UserController@enableUser')->name('user.enalbe');
 });
