<?php

// Homepage.
Route::get('', 'DashboardController@home');

// Files.
Route::get('files/{id}/{format?}', 'FilesController@view');

// Search.
Route::get('search', 'SearchController@search');

// Users.
Route::get('login', 'UsersController@login');
Route::post('login', 'UsersController@loginPost');
Route::get('logout', 'UsersController@logout');
Route::post('users/create', 'UsersController@create');
Route::get('users/{username}', 'UsersController@profile');
Route::post('users/{username}', 'UsersController@profilePost');
Route::get('users', 'UsersController@index');
Route::get('users.json', 'UsersController@json');
Route::get('remind', 'UsersController@remind');
Route::post('remind', 'UsersController@remindPost');

// Crews.
Route::get('crews', 'CrewsController@index');
Route::get('crews/create', 'CrewsController@create');
Route::post('crews/create', 'CrewsController@save');
Route::get('crews/{id}/edit', 'CrewsController@edit');
Route::post('crews/{id}/edit', 'CrewsController@save');

// Assets.
Route::get('assets', 'AssetsController@index');
Route::post('assets', 'AssetsController@index');
Route::any('assets/map', 'AssetsController@map');
Route::get('assets.geojson', 'AssetsController@mapJson');
Route::get('assets/{id}', 'AssetsController@view')->where(['id' => '[0-9]+']);
Route::get('assets/{id}/edit', 'AssetsController@edit')->where(['id' => '[0-9]+']);
Route::get('assets/create', 'AssetsController@create');
Route::post('assets/save', 'AssetsController@save');
Route::get('assets/import', 'AssetsController@import');
Route::post('assets/import', 'AssetsController@importPost');

// Contacts.
Route::get('contacts', 'ContactsController@index');
Route::get('contacts/{id}', 'ContactsController@view')->where(['id' => '[0-9]+']);
Route::get('contacts/create', 'ContactsController@create');
Route::post('contacts/create', 'ContactsController@createPost');
Route::post('contacts/create-for-asset', 'ContactsController@createForAsset');

// Job Lists.
Route::get('job-lists', 'JobListsController@index');
Route::get('job-lists/create', 'JobListsController@create');
Route::post('job-lists/create', 'JobListsController@create');
Route::post('job-lists/save', 'JobListsController@saveNew');
Route::get('job-lists/{id}', 'JobListsController@view')->where(['id' => '[0-9]+']);
Route::get('job-lists/{id}.geojson', 'JobListsController@geojson')->where(['id' => '[0-9]+']);
Route::get('job-lists/{id}/edit', 'JobListsController@edit')->where(['id' => '[0-9]+']);
Route::post('job-lists/{id}/edit', 'JobListsController@saveExisting')->where(['id' => '[0-9]+']);
Route::get('job-lists/scheduled', 'JobListsController@scheduled');

// Job Types.
Route::get('job-types', 'JobTypesController@index');
Route::get('job-types/{id}', 'JobTypesController@edit')->where(['id' => '[0-9]+']);
Route::post('job-types', 'JobTypesController@save');

// Jobs.
Route::get('jobs', 'JobsController@index');
Route::get('jobs/{id}', 'JobsController@view')->where(['id' => '[0-9]+']);
Route::get('jobs/{id}/edit', 'JobsController@edit')->where(['id' => '[0-9]+']);
Route::post('jobs/{id}/edit', 'JobsController@save')->where(['id' => '[0-9]+']);

// Tags.
Route::get('tags', 'TagsController@index');
Route::get('tags.json', 'TagsController@json');
Route::post('tags', 'TagsController@save');

// Mobile.
Route::get('m', 'JobListsController@mobileUser');
Route::get('m/{id}', 'JobsController@mobile');
