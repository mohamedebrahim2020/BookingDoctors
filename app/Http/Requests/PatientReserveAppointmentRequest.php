<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientReserveAppointmentRequest extends FormRequest
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
            'time' => 'required|date|date_format:Y-m-d H:i:s|after:today',
            'duration' => 'required|integer|min:30|max:120'
        ];
    }
}
