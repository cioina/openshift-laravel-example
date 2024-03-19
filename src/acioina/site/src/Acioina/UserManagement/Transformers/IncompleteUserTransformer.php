<?php

namespace Acioina\UserManagement\Transformers;

class IncompleteUserTransformer extends Transformer
{
    protected $resourceName = 'user';

    public function transform($data)
    {
        return [
            'username'  => $data['username'],
            'bio'       => $data['first_name'],
            'image'     => $data['fb_picture'],
            'email'     => $data['email'],
        ];
    }
}
