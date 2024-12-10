<?php

namespace Acioina\UserManagement\Transformers;

class TagTransformer extends Transformer
{
    protected $resourceName = 'tags';

    public function transform($data)
    {
        return [
            'title'  => $data['label'],
            'id'     => $data['id'],
        ];
    }
}
