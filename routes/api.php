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

Route::get('dashboard/list', 'UsersController@index')->middleware('api');

Route::get('dashboard/stockList', 'StockController@index')->middleware('api');

Route::get('dashboard/stockIssue', 'StockIssueController@index')->middleware('api');
Route::get('dashboard/getSalesMen', 'StockIssueController@getSalesMen')->middleware('api');
Route::get('dashboard/getStock', 'StockIssueController@getStock')->middleware('api');
Route::get('dashboard/getStockbyID/{id}', 'StockIssueController@getStockbyID')->middleware('api');


Route::get('dashboard/sales', 'SalesController@index')->middleware('api');
Route::get('dashboard/getsalesMen', 'SalesController@getSalesMen')->middleware('api');
Route::get('dashboard/getstock', 'SalesController@getStock')->middleware('api');
Route::get('dashboard/getStockbyId/{id}', 'SalesController@getStockbyID')->middleware('api');

Route::get('dashboard/salemenList', 'SalemenController@index')->middleware('api');
Route::get('dashboard/getArea', 'SalemenController@getArea')->middleware('api');

Route::get('dashboard/areaList', 'AreaController@index')->middleware('api');
