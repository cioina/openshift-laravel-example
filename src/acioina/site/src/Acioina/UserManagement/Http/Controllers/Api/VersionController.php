<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Acioina\UserManagement\Transformers\VersionTransformer;

class VersionController extends ApiController
{
    private const ANGULAR_APP_HASH = 'b476a80d7b15a94ea561d19fa465c824';

    public function __construct(VersionTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    public function index()
    {
        return $this->respondWithTransformer(['hash' => self::ANGULAR_APP_HASH]);
    }
}
