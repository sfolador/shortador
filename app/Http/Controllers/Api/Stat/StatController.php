<?php


namespace App\Http\Controllers\Api\Stat;


use App\Http\Controllers\Controller;
use App\Http\Resources\StatResource;
use App\Http\Resources\UrlResource;
use App\Models\Stat\Stat;

class StatController extends Controller
{
    /**
     * @param $hashedId
     * @return StatResource|\Illuminate\Http\JsonResponse
     */
    public function show($hashedId)
    {
        $v = \Validator::make(['hashedId' => $hashedId],[
            'hashedId' => 'required|alpha_num'
        ]);

        if ($v->fails()){
            return response()->json(['errors' => $v->errors()],400);
        }

//        $url = \Shortener::load($hashedId);
        $stat =  \Shortener::getStatForShortenedUrl($hashedId);
        if (!$stat){
            return response()->json([],404);
        }


        return new StatResource($stat);
    }
}
