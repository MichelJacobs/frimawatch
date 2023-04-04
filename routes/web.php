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
    Route::get('/notification/{userId}/export','App\Http\Controllers\NotificationController@export')->name('notification.export');
    Route::post('/notification/{userId}/import','App\Http\Controllers\NotificationController@import')->name('notification.import');
    Route::post('/scrape', 'App\Http\Controllers\NotificationController@scrape')->name('scrape');
    Route::resource('/search', 'App\Http\Controllers\SearchController');
    Route::resource('/timeline', 'App\Http\Controllers\TimeLineController');
    Route::resource('/user/setting', 'App\Http\Controllers\UserSettingController');
    Route::post('/timeline/delete', 'App\Http\Controllers\TimeLineController@delete')->name('timeline.delete');
});

Route::group(['middleware' => ['auth','admin']], function () {
    Route::resource('/user', 'App\Http\Controllers\UserController');
    Route::post('/user/disable', 'App\Http\Controllers\UserController@disableUser')->name('user.disable');
    Route::post('/user/enable', 'App\Http\Controllers\UserController@enableUser')->name('user.enable');

    // Route::get('/setting', 'App\Http\Controllers\UserController@setting')->name('user.setting');
    // Route::post('/setting/mailLimitStore', 'App\Http\Controllers\UserController@mailLimitStore')->name('user.mailLimitStore');
 });
