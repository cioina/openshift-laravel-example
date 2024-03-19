<?php

namespace Acioina\UserManagement\Http\Controllers\Api\Admin;

use Distilleries\Expendable\Models\Topic;
use Acioina\UserManagement\Transformers\TagTransformer;

class AdminController extends BaseAdminController
{
    /**
     * AdminController constructor.
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

