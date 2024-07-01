<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class CreateUserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required|array',
            'name' => 'required',
        ];

    }
}
