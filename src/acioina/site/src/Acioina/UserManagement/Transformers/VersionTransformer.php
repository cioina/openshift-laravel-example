<?php

namespace Acioina\UserManagement\Transformers;

class VersionTransformer extends Transformer
{
    protected $resourceName = 'version';

    public function transform($data)
    {
        return [
            'hash'  => $data['hash'],
        ];
    }
}
