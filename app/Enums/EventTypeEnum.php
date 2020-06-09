<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * Class EventTypeEnum
 *
 * This class is an Enum and it's used to define types of events occurring to a single URL. For example, when a URL is Opened (i.e. a User clicks on a
 * shortened URL and he opens it in the browser), the event type is "opened".
 *
 * @package App\Enums
 *
 * @method static self created()
 * @method static self opened()
 * @method static self deleted()
 */
final class EventTypeEnum extends Enum
{

}
