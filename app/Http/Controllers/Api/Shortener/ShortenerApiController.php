<?php

namespace App\Http\Controllers\Api\Shortener;

use App\Http\Controllers\Controller;
use App\Http\Resources\UrlResource;
use Illuminate\Http\Request;

class ShortenerApiController extends Controller
{


    public function create(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'url' => 'required|url'
            ]
        );

        if ($validator->failed()) {
            return response()->json($validator->failed(), 400);
        }

        $plainUrl = $request->get('url');
        $url = \Shortener::shorten($plainUrl);


        return new UrlResource($url);
    }


    public function show(Request $request, $hashedId)
    {
        $v = \Validator::make(
            ['hashedId' => $hashedId],
            [
                'hashedId' => 'required|alpha_num'
            ]
        );

        if ($v->fails()) {
            return response()->json($v->failed(), 400);
        }

        $url = \Shortener::load($hashedId);
        return new UrlResource($url);
    }


    public function delete(Request $request, $hashedId)
    {
        $v = \Validator::make(
            ['hashedId' => $hashedId],
            [
                'hashedId' => 'required|alpha_num'
            ]
        );

        if ($v->fails()) {
            return response()->json($v->getMessageBag()->getMessages(), 400);
        }

        $url = \Shortener::load($hashedId);
        if ($url) {
            \Shortener::delete($url);
        } else {
            return response()->json([], 404);
        }


        return new UrlResource($url);
    }

}
