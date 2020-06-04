<?php


namespace App\Models\Stat;


use App\Models\Url\Url;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stat extends Model
{
    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }

    public function scopeUrlIs($query,$urlId)
    {
        return $query->where('url_id', $urlId);
    }

    public function scopeShortenedUrlIs($query,$shortened)
    {
        return $query->whereHas('url',static function($q) use($shortened){
            $q->where('shortened',$shortened);
        });
    }



}
