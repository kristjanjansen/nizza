<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'FrontController@index');

Route::resource('user', 'UserController');
Route::resource('forum', 'ForumController');
Route::resource('travelmate', 'TravelmateController');
Route::resource('topic', 'TopicController');
Route::resource('blog', 'BlogController');
Route::resource('image', 'ImageController');
Route::resource('news', 'NewsController');
Route::resource('flight', 'FlightController');
Route::resource('offer', 'OfferController');
Route::resource('destination', 'DestinationController');
Route::resource('expat', 'ExpatController');
Route::resource('buysell', 'BuysellController');
Route::resource('misc', 'MiscController');
Route::resource('editor', 'EditorController');