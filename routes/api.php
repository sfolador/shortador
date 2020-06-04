<?php


use Illuminate\Support\Facades\Route;


Route::namespace('Api')->group(static function(){
  Route::namespace('Shortener')->prefix('url')->group(static function(){
      Route::post('/','ShortenerApiController@create');
      Route::delete('/','ShortenerApiController@delete');
      Route::get('/{hashedId}','ShortenerApiController@show');
  });

  Route::namespace('Stat')->prefix('url')->group(static function(){
        Route::get('/{hashedId}/stat','StatController@show');
  });
});

