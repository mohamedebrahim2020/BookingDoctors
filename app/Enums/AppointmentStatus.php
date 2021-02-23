<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class AppointmentStatus extends Enum
{
    const PENDING = 1;
    const APPROVED = 2;
    const REJECTED = 3;
    const CANCELLED = 4;
    const CHECKED = 5;
}
