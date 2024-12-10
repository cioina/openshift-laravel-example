<?php

namespace Acioina\UserManagement\Transformers;

class UserTransformer extends Transformer
{
    protected $resourceName = 'user';

    public function transform($data)
    {
        return [
            'username'  => $data['username'],
            'bio'       => $data['first_name'],
            'image'     => $data['fb_picture'],
            'email'     => $data['email'],
            'token'     => $data['token'],// see getTokenAttribute()
        ];
    }
}
