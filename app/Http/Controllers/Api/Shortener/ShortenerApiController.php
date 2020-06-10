<?php

namespace App\Http\Controllers\Api\Shortener;

use App\Http\Controllers\Controller;
use App\Http\Resources\UrlResource;
use Illuminate\Http\Request;

/**
 * Class ShortenerApiController
 * @package App\Http\Controllers\Api\Shortener
 */
class ShortenerApiController extends Controller
{


    /**
     * Stores a new shortened URL
     *
     * @param Request $request
     * @return UrlResource|\Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'url' => 'required|url'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $plainUrl = $request->get('url');
        $url = \Shortener::shorten($plainUrl);


        return new UrlResource($url);
    }


    /**
     *
     * Shows the original URL related to the shortened URL.
     *
     * @param Request $request
     * @param $hashedId
     * @return UrlResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $hashedId)
    {
        $v = \Validator::make(
            ['hashedId' => $hashedId],
            [
                'hashedId' => 'required|alpha_num|exists:urls,shortened'
            ]
        );

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 400);
        }

        $url = \Shortener::load($hashedId);

        return new UrlResource($url);
    }


    /**
     *
     * Deletes a shortened URL
     *
     * @param Request $request
     * @param $hashedId
     * @return UrlResource|\Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $hashedId)
    {
        $v = \Validator::make(
            ['hashedId' => $hashedId],
            [
                'hashedId' => 'required|alpha_num|exists:urls,shortened'
            ]
        );

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 400);
        }

        $url = \Shortener::load($hashedId);
        \Shortener::delete($url);

        return new UrlResource($url);
    }

}
