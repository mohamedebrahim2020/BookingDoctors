<?php

namespace App\Http\Requests;

use Carbon\Carbon;
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
        $duration = array(30, 60, 120);
        $nowInMs = Carbon::now()->addDay()->timestamp;
        return [
            'time' => 'required|numeric|gt:'. $nowInMs,
            'duration' => 'required|integer|in:' . implode(',', $duration),
        ];
    }

}
