<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Acioina\UserManagement\Transformers\VersionTransformer;

class VersionController extends ApiController
{
    private const ANGULAR_APP_HASH = '2ba87242e58cb38e3e6fe34bf0ac062d';

    public function __construct(VersionTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    public function index()
    {
        return $this->respondWithTransformer(['hash' => self::ANGULAR_APP_HASH]);
    }
}
