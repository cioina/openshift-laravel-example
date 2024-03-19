<?php

namespace Acioina\UserManagement\Transformers;

class ArticleTransformer extends Transformer
{
    protected $resourceName = 'article';

    public function transform($data)
    {
        return [
            'title' => $data['label'],
            'slug' => $data['slug'],
            'id' => $data['id'],
            'description' => $data['description'],
            'createdAt' => $data['created_at']->format('jS M Y'),
        ];

     }
}
