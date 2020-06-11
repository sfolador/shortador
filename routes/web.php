<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/{hashedId}',"Shortener\ShortenerController@show")->name('get-original-url');


