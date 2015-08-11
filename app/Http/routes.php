<?php

// Homepage.
Route::get('/', 'DashboardController@home');

// Users.
Route::get('login', 'UsersController@login');
Route::post('login', 'UsersController@loginPost');
Route::get('logout', 'UsersController@logout');
Route::get('users/{username}', 'UsersController@profileGet');
Route::post('users/{username}', 'UsersController@profilePost');
Route::get('users', 'UsersController@index');

// Assets.
Route::get('assets', 'AssetsController@index');
Route::post('assets', 'AssetsController@index');
Route::get('assets/{id}', 'AssetsController@view')->where(['id' => '[0-9]+']);
Route::get('assets/{id}/edit', 'AssetsController@edit')->where(['id' => '[0-9]+']);
Route::get('assets/create', 'AssetsController@create');
Route::post('assets/save', 'AssetsController@save');
Route::get('contacts', 'ContactsController@index');
Route::get('contacts/{id}', 'ContactsController@view')->where(['id' => '[0-9]+']);

// Work Orders.
Route::get('work-orders', 'WorkOrdersController@index');
Route::get('work-orders/create', 'WorkOrdersController@create');
Route::post('work-orders/create', 'WorkOrdersController@create');
Route::post('work-orders/save', 'WorkOrdersController@saveNew');
Route::get('work-orders/{id}', 'WorkOrdersController@view')->where(['id' => '[0-9]+']);
Route::get('work-orders/{id}/edit', 'WorkOrdersController@edit')->where(['id' => '[0-9]+']);
Route::post('work-orders/{id}/edit', 'WorkOrdersController@saveExisting')->where(['id' => '[0-9]+']);

// Scheduled Work Orders.
Route::get('work-orders/{woid}/schedule/create', 'ScheduledWorkOrdersController@form')->where(['woid' => '[0-9]+']);
Route::post('work-orders/{woid}/schedule/save', 'ScheduledWorkOrdersController@save')->where(['woid' => '[0-9]+']);
Route::get('work-orders/{woid}/schedule/{sid}/edit', 'ScheduledWorkOrdersController@form')->where(['woid' => '[0-9]+', 'sid' => '[0-9]+']);
//Route::post('work-orders/{woid}/schedule/{sid}/edit', 'ScheduledWorkOrdersController@save')->where(['woid' => '[0-9]+', 'sid' => '[0-9]+']);
Route::get('scheduled-work-orders', 'ScheduledWorkOrdersController@index');
Route::get('m/{id}', 'ScheduledWorkOrdersController@crew')->where(['id' => '[0-9]+']);
