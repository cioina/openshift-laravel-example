<?php

namespace Acioina\UserManagement\Http\Requests\Api;

class LoginUser extends ApiRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->get('user') ?: [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|max:80',
            'password' => 'required|min:10|max:20',
        ];
    }

    public function messages()
    {
        return [
          'email.required' => 'is required',
          'email.email' => 'must be a valid email address',
          'email.max' => 'must be shorter than 80 characters',

          'password.required' => 'is required',
          'password.min' => 'must have at least 10 characters',
          'password.max' => 'must be shorter than 20 characters',
        ];
    }
}
