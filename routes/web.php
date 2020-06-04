<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    return view('welcome');
});


Route::get('/{hashedId}',"Shortener\ShortenerController@show")->name('get-original-url');


