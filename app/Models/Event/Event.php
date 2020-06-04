<?php


namespace App\Models\Event;


use App\Enums\EventTypeEnum;
use App\Models\Url\Url;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Enum\Laravel\HasEnums;

class Event extends Model
{
    use HasEnums;

    protected $enums = [
        'event_type' => EventTypeEnum::class
    ];

    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }
}
