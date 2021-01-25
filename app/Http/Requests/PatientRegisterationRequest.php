<?php

namespace App\Http\Requests;

use App\Enums\GenderType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class PatientRegisterationRequest extends FormRequest
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
            'name' => 'required|max:100',
            'email' => 'required|email|unique:patients,email',
            'password' => 'required|min:6',
            'phone' => 'required|unique:patients,phone',
            'photo' => 'required|file|mimes:png',
            'gender' => ['required', new EnumValue(GenderType::class)],
        ];
    }
}
