<?php


namespace App\Events;


use App\Models\Url\Url;

interface UrlEventInterface
{
    /**
     * @return Url
     */
    public function getUrl(): Url;
}
