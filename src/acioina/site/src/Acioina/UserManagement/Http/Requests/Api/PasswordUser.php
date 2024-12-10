<?php

namespace Acioina\UserManagement\Http\Requests\Api;

class PasswordUser extends ApiRequest
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
            'password' => 'required|min:10|max:20',
        ];
    }

    public function messages()
    {
        return [
          'password.required' => 'is required',
          'password.min' => 'must be at least 10 characters',
          'password.max' => 'must be shorter than 20 characters',
        ];
    }
}
