<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class PlatformType extends Enum
{
    const WEB = 1;
    const ANDROID = 2;
    const IOS = 3;
}
