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

// Assets.
Route::get('assets', 'AssetsController@index');
Route::get('assets/{id}', 'AssetsController@view')->where(['id' => '[0-9]+']);
Route::get('assets/{id}/edit', 'AssetsController@edit')->where(['id' => '[0-9]+']);
Route::get('assets/create', 'AssetsController@create');
Route::post('assets', 'AssetsController@save');
