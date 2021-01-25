<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FolderName extends Enum
{
    const PHOTO =   '/photo';
    const DEGREE_COPY =   '/degree_copy';
    const PATIENT_PHOTO = '/patient_photo';
}
