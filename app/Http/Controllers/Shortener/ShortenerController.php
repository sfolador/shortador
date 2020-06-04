<?php


namespace App\Http\Controllers\Shortener;


use App\Events\UrlOpenedEvent;
use App\Http\Controllers\Controller;
use App\Models\Url\Url;

class ShortenerController extends Controller
{
    public function show(\Request $request, $hashedId)
    {

        $v = \Validator::make(['hashedId' => $hashedId],[
            'hashedId' => 'required|alphanum'
        ]);

        if ($v->fails()){
            return response('',404);
        }



        $url = \Shortener::load($hashedId);


        if (!$url){
            return response('',404);
        }

        event(new UrlOpenedEvent($url));

        return redirect()->away($url->getUnfurledUrl());
    }

}
