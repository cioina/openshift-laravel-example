<?php

namespace Acioina\UserManagement\Http\Requests\Api;

class UpdateUser extends ApiRequest
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
            'username' => 'min:2|max:50|alpha_num|unique:clients,username',
            'password' => 'min:10|max:20',

        ];
    }

    public function messages()
    {
        return [
          'username.min' => 'must have at least 2 characters',
          'username.max' => 'must be shorter than 50 characters',
          'username.alpha_num' => 'may only contain letters and numbers',
          'username.unique' => 'has been taken',

          'password.min' => 'must have at least 10 characters',
          'password.max' => 'must be shorter than 20 characters',
        ];
    }

}
