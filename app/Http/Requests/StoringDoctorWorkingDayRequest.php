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
            'working_days' => 'required|array',
            'working_days.*.day' => ['required', new EnumValue(WeekDays::class, false)],
            'working_days.*.from' => 'nullable|required_with:working_days.*.to|date_format:h:i A|required_if:working_days.*.is_all_day,0',
            'working_days.*.to' => 'nullable|required_with:working_days.*.from|date_format:h:i A|after:working_days.*.from',
            'working_days.*.is_all_day' => 'required_if:working_days.*.from,|boolean'    
        ];
    }
}
