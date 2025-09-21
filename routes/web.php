<?php

use Illuminate\Support\Facades\Route;

Route::any('/{path?}', function() {
    return view('app');
})->where('path', '.*');
