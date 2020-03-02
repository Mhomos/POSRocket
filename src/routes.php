<?php
/*
|--------------------------------------------------------------------------
| POSRocket Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'posrocket'], function () {
    Route::get('/connect', 'msh\posrocket\PosRocketController@connect');
    Route::get('/connectListener', 'msh\posrocket\PosRocketController@connectListener');
    Route::get('/business', 'msh\posrocket\PosRocketController@getUserData');
    Route::get('/menu', 'msh\posrocket\PosRocketController@getMenuItems');
});
