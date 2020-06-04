<?php


namespace App\Models\Url;


use App\Models\Stat\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Url extends Model
{
    public function stats(): HasOne
    {
        return $this->hasOne(Stat::class);
    }

    /**
     * @return string
     */
    public function getUnfurledUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getShortenedString(): string
    {
        return $this->shortened;
    }

    /**
     * @param $query
     * @param $value
     * @return mixed
     */
    public function scopeShortenedIs($query,$value)
    {
        return $query->where('shortened',$value);
    }
}
