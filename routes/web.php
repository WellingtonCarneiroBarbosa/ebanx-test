<?php

Route::get('/', function () {
    return response()->json([
        'message'         => 'Welcome to the ebanx-test API.',
        'laravel-version' => app()->version(),
        'php-version'     => phpversion(),
        'status'          => 'Connected',
    ]);
});
