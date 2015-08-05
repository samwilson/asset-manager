<?php

// Homepage.
Route::get('/', 'ScheduleController@index');

// Users.
Route::get('login', 'UsersController@loginGet');
Route::post('login', 'UsersController@loginPost');
Route::get('logout', 'UsersController@logout');
Route::get('users/{username}', 'UsersController@profileGet');
Route::post('users/{username}', 'UsersController@profilePost');
Route::get('users', 'UsersController@index');

// Data.
//Route::resource('assets', 'AssetsController');
//Route::resource('asset-categories', 'AssetCategoriesController');
//Route::resource('job-types', 'JobTypesController');

// Assets.
Route::any('assets', 'AssetsController@index');
Route::get('assets/{id}', 'AssetsController@view')->where(['id' => '[0-9]+']);
Route::get('assets/{id}/edit', 'AssetsController@edit')->where(['id' => '[0-9]+']);
Route::get('assets/create', 'AssetsController@create');
Route::post('assets', 'AssetsController@save');
