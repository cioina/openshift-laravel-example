<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Acioina\UserManagement\Transformers\VersionTransformer;

class VersionController extends ApiController
{
    private const ANGULAR_APP_HASH = '8f788b5bd28bb94be2ba892eef0de1e4';

    public function __construct(VersionTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    public function index()
    {
        return $this->respondWithTransformer(['hash' => self::ANGULAR_APP_HASH]);
    }
}
