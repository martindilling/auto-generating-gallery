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

Route::get('/', 'GalleryController@showAlbums');
Route::get('all', 'GalleryController@showAllImages');
Route::get('import', 'GalleryController@import');

Route::get('{album_folder}', 'GalleryController@showAlbum');
Route::get('{album_folder}/{image_file}', 'GalleryController@showImage');
