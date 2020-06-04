<?php


namespace App\Models\Url\Manager;


use Illuminate\Support\Facades\Facade;

class UrlShortenerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shortener';
    }
}
