<?php

namespace App\Http\Requests;

use App\Enums\GenderType;
use Illuminate\Foundation\Http\FormRequest;
use BenSampo\Enum\Rules\EnumValue;

class DoctorRegistrationRequest extends FormRequest
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
            'email' => 'required|email|unique:doctors,email',
            'phone' => 'required|unique:doctors,phone',
            'specialization_id' => 'required|exists:specializations,id',
            'password' => 'required|min:4',
            'photo' => 'required|file|mimes:png',
            'degree_copy' =>'required|file|mimes:png',
            'gender' => ['required', new EnumValue(GenderType::class, false)],
        ];
    }
}
