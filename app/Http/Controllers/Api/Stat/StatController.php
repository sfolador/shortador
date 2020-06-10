<?php

namespace App\Http\Controllers\Api\Stat;


use App\Http\Controllers\Controller;
use App\Http\Resources\StatResource;
use Illuminate\Http\JsonResponse;

/**
 * Class StatController
 * @package App\Http\Controllers\Api\Stat
 */
class StatController extends Controller
{
    /**
     * @param $hashedId
     * @return StatResource|JsonResponse
     */
    public function show($hashedId)
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


        $stat = \Shortener::getStatForShortenedUrl($hashedId);
        if (!$stat) {
            return response()->json([], 404);
        }


        return new StatResource($stat);
    }
}
