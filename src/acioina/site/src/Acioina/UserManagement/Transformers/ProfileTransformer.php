<?php

namespace Acioina\UserManagement\Transformers;

class ProfileTransformer extends Transformer
{
    protected $resourceName = 'profile';

    public function transform($data)
    {
        return [
            'username'  => $data['username'],
            'bio'       => $data['first_name'],
            'image'     => $data['fb_picture'],
        ];
    }
}
