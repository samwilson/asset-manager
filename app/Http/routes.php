<?php

// Homepage.
Route::get('/', 'DashboardController@home');
Route::get('/tags.json', 'TagsController@json');

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
Route::get('assets/import', 'AssetsController@import');
Route::post('assets/import', 'AssetsController@importPost');

// Contacts.
Route::get('contacts', 'ContactsController@index');
Route::get('contacts/{id}', 'ContactsController@view')->where(['id' => '[0-9]+']);
Route::post('contacts/create-for-asset', 'ContactsController@createForAsset');

// Job Lists.
Route::get('job-lists', 'JobListsController@index');
Route::get('job-lists/create', 'JobListsController@create');
Route::post('job-lists/create', 'JobListsController@create');
Route::post('job-lists/save', 'JobListsController@saveNew');
Route::get('job-lists/{id}', 'JobListsController@view')->where(['id' => '[0-9]+']);
Route::get('job-lists/{id}/edit', 'JobListsController@edit')->where(['id' => '[0-9]+']);
Route::post('job-lists/{id}/edit', 'JobListsController@saveExisting')->where(['id' => '[0-9]+']);

// Scheduled Work Orders.
Route::get('work-orders/{woid}/schedule/create', 'ScheduledWorkOrdersController@form')->where(['woid' => '[0-9]+']);
Route::post('work-orders/{woid}/schedule/save', 'ScheduledWorkOrdersController@save')->where(['woid' => '[0-9]+']);
Route::get('work-orders/{woid}/schedule/{sid}/edit', 'ScheduledWorkOrdersController@form')->where(['woid' => '[0-9]+', 'sid' => '[0-9]+']);
//Route::post('work-orders/{woid}/schedule/{sid}/edit', 'ScheduledWorkOrdersController@save')->where(['woid' => '[0-9]+', 'sid' => '[0-9]+']);
Route::get('scheduled-work-orders', 'ScheduledWorkOrdersController@index');
Route::get('m/{id}', 'ScheduledWorkOrdersController@crew')->where(['id' => '[0-9]+']);
