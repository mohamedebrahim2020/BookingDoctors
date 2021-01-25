<?php

namespace App\Http\Requests;

use App\Enums\WeekDays;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class StoringDoctorWorkingDayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day' => ['required', new EnumValue(WeekDays::class, false)],
            'from' => 'nullable|required_with:to|date_format:g:a',
            'to' => 'nullable|required_with:from|date_format:g:a|after:from',
        ];
    }
}
