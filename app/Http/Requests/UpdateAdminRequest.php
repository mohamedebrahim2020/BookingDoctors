<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
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
        $admin = Admin::findorfail($this->admin);
        return [
            'name' => 'required|max:100',
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($admin)],
            'phone' => 'nullable',
            'permissions' => 'required|array',
            'permissions.*' =>'exists:Spatie\Permission\Models\Permission,id',
        ];
    }
}
