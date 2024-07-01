<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required'
        ];
    }
    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    public function getCredentials()
    {
        return $this->only('email', 'password');
    }
}
