<?php

namespace Acioina\UserManagement\Http\Controllers\Api\Admin;

use Acioina\UserManagement\Http\Controllers\Api\ApiController;
use Acioina\UserManagement\Transformers\Transformer;

class BaseAdminController extends ApiController
{
    /**
     * BaseAdminController constructor.
     *
     * @param Transformer $transformer
     */
    public function __construct(Transformer $transformer)
    {
        parent::__construct($transformer);
        $this->middleware('auth.api');
        $this->middleware('admin.api');
    }
}

