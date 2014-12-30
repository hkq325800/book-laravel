<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: 'GET'");
header("Access-Control-Allow-Methods: 'POST'");
header("Access-Control-Max-Age: '60'");
define("pagesize", 15, true);
define("saltkey", 'nigoule');
define("url",'http://www.flappyant.com/book/API.php');
/**
//----------public----------
**/
Route::get('/', 'HomeController@showWelcome');
Route::get('/show','PublicController@show');
Route::post('/public/passChange','PublicController@passChange');
Route::get('/public/like/{bookKind}/{userId}/{password}','PublicController@setLike');
Route::get('/public/bookSum/{IsAdmin}/{type}/{userId}','PublicController@getBookSum');
Route::get('/public/detail/{bookKind}','PublicController@bookDetail');
Route::get('/public/recentAdd/{userId}','PublicController@recentAdd');
Route::get('/public/batch','PublicController@getBatch');
/**
//----------user----------
**/
Route::post('/normal/login','UserController@toLogin');
Route::post('/normal/register','UserController@toRegister');
Route::get('/normal/borrow/{bookId}/{userId}/{password}','UserController@toBorrow');
Route::get('/normal/search/{userId}/{type}/{keyword}','UserController@toSearch');
Route::get('/normal/showRe/{userId}/{password}','UserController@showRent');
/**
//----------admin----------
**/
Route::post('/admin/login','AdminController@toLogin');
//Route::post('/admin/register','AdminController@toRegister');
Route::get('/admin/search/{userId}/{type}/{keyword}','AdminController@toSearch');
Route::get('/admin/confirm/{bookId}/{userId}/{password}','AdminController@toConfirm');
Route::get('/admin/getId/{actId}/{userId}/{password}','AdminController@getId');
Route::get('/admin/add/{bookIsbn}/{bookType}/{userId}/{password}','AdminController@toAdd');
Route::get('/admin/delete/{bookId}/{userId}/{password}','AdminController@toDelete');
Route::get('/admin/showRe/{userId}/{password}','AdminController@showRent');