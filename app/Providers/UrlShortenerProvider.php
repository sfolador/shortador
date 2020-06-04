<?php

namespace App\Providers;

use App\Models\Url\Manager\UrlManager;
use Illuminate\Support\ServiceProvider;

class UrlShortenerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->app->singleton('shortener',static function(){
            return new UrlManager();
        });
    }
}
