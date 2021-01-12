<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;

class AddAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $superAdmin = Admin::findorfail(auth()->user()->id);
        return ($superAdmin->is_super == 1);
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
            'email' => 'required|email|unique:admins',
            'phone' => 'nullable',
            'permissions' => 'required|array',
            'permissions.*' =>'exists:Spatie\Permission\Models\Permission,id'
        ];
    }
}
