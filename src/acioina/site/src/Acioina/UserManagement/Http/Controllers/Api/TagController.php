<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Distilleries\Expendable\Models\Topic;
use Acioina\UserManagement\Transformers\TagTransformer;

class TagController extends ApiController
{
    /**
     * TagController constructor.
     *
     * @param TagTransformer $transformer
     */
    public function __construct(TagTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    /**
     * Get all the tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tags = Topic::all();

        return $this->respondWithTransformer($tags);
    }
}
