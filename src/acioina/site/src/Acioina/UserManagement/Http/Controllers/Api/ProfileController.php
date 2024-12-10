<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Distilleries\Expendable\Models\Client;
use Acioina\UserManagement\Transformers\ProfileTransformer;

class ProfileController extends ApiController
{
    /**
     * ProfileController constructor.
     *
     * @param ProfileTransformer $transformer
     */
    public function __construct(ProfileTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    /**
     * Get the profile of the user given by their username
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Client $user)
    {
        return $this->respondWithTransformer($user);
    }
}
