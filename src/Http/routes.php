<?php

Route::group([
    'namespace'  => 'RecursiveTree\Seat\PushxBlamer\Http\Controllers',
    'middleware' => ['web', 'auth'],
    'prefix' => 'pushxblamer',
], function () {
    Route::get('/', [
        'as'   => 'pushxblamer.main',
        'uses' => 'PushxBlamerController@main',
        'middleware' => 'can:pushxblamer.blamer'
    ]);
});