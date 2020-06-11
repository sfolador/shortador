<?php


use Illuminate\Support\Facades\Route;


Route::namespace('Api')->group(static function(){
  Route::namespace('Shortener')->prefix('url')->group(static function(){
      Route::post('/','ShortenerApiController@create')->name('create-url');
      Route::delete('/{hashedId}','ShortenerApiController@delete')->name('delete-url');
      Route::get('/{hashedId}','ShortenerApiController@show')->name('show-url');
  });

  Route::namespace('Stat')->prefix('url')->group(static function(){
        Route::get('/{hashedId}/stat','StatController@show')->name('show-url-stats');
  });
});

