<?php

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

Auth::routes();

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function (){
    Route::get('/', 'HomeController@index');

    // Routes for users
    Route::get('/users', 'UsersController@index')->name('users');
    Route::post('/create/user/save', 'UsersController@store')->name('storeUser');
    Route::get('/user/delete/{id}', 'UsersController@destroy');

    // Routes for salemen
    Route::get('/salemen', 'SalemenController@index')->name('salemen');
    Route::post('/create/salemen/save', 'SalemenController@store')->name('storeSalemen');
    Route::get('/salemen/delete/{id}', 'SalemenController@destroy');

    // Routes for stocks
    Route::get('/stock', 'StockController@index')->name('stock');
    Route::post('/create/stock/save', 'StockController@store')->name('storeStock');
    Route::get('/stock/delete/{id}', 'StockController@destroy');

    // Routes for Area
    Route::get('/area', 'AreaController@index')->name('area');
    Route::post('/create/area/save', 'AreaController@store')->name('storeArea');
    Route::get('/area/delete/{id}', 'AreaController@destroy');

    // Routes for Issue Area
    Route::get('/stockIssue', 'StockIssueController@index')->name('stockIssue');
    Route::post('/create/stockIssue/save', 'StockIssueController@store');
    Route::get('/stockIssue/delete/{id}', 'StockIssueController@destroy');

    // Routes for sales
    Route::get('/sales', 'SalesController@index')->name('sales');
    Route::post('/create/sales/save', 'SalesController@store');
    Route::get('/sales/delete/{id}', 'SalesController@destroy');
});

Route::get('/', 'HomeController@index');
Route::get('/logout','HomeController@userlogout')->name('userlogout');