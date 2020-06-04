<?php

namespace App\Http\Controllers\Api\Shortener;

use App\Http\Controllers\Controller;
use App\Http\Resources\UrlResource;

class ShortenerApiController extends Controller
{


    public function create(\Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'url' => 'required|url'
        ]);

        if ($validator->failed()){
            return response()->json($validator->failed(),400);
        }

        $plainUrl = $request->get('url');
        $url = \Shortener::shorten($plainUrl);



        return new UrlResource($url);
    }


    public function show(\Request $request,$hashedId)
    {
        $v = \Validator::make(['hashedId' => $hashedId],[
            'hashedId' => 'required|alphanum'
        ]);

        if ($v->fails()){
            return response()->json($v->failed(),400);
        }

        $url = \Shortener::load($hashedId);
        return new UrlResource($url);
    }


    public function delete(\Request $request,$hashedId)
    {
        $v = \Validator::make(['hashedId' => $hashedId],[
            'hashedId' => 'required|alphanum'
        ]);

        if ($v->fails()){
            return response()->json($v->failed(),400);
        }

        $url = \Shortener::load($hashedId);
       if ($url)
       {
           \Shortener::delete($url);
       }


        return new UrlResource($url);
    }

}
