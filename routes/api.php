<?php

use Illuminate\Support\Facades\Route;

Route::get('/version', function () {
    return response()->json([
        'version' => config('version.app'),
        'name' => config('app.name'),
    ]);
});
