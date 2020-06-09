<?php


namespace App\Models\Event;


use App\Enums\EventTypeEnum;
use App\Models\Url\Url;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Enum\Laravel\HasEnums;

/**
 * Class Event
 * @package App\Models\Event
 */
class Event extends Model
{
    use HasEnums;

    /**
     * @var string[]
     */
    protected $enums = [
        'event_type' => EventTypeEnum::class
    ];

    /**
     * @return BelongsTo
     */
    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }
}
