<?php

namespace Acioina\UserManagement\Http\Requests\Api;

class RegisterUser extends ApiRequest
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
            'username' => 'required|min:2|max:50|alpha_num|unique:clients,username',
            'email' => 'required|email|max:80|unique:clients,email',
            'password' => 'required|min:10|max:20',
        ];
    }

    public function messages()
    {
        return [
          'username.required' => 'is required',
          'username.min' => 'must be at least 2 characters',
          'username.max' => 'must be shorter than 50 characters',
          'username.alpha_num' => 'may only contain letters and numbers',
          'username.unique' => 'has been taken',

          'email.required' => 'is required',
          'email.email' => 'must be a valid email address',
          'email.max' => 'must be shorter than 80 characters',
          'email.unique' => 'has been taken',

          'password.required' => 'is required',
          'password.min' => 'must be at least 10 characters',
          'password.max' => 'must be shorter than 20 characters',
        ];
    }

}
