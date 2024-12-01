<?php

namespace Acioina\UserManagement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class AuthenticateAdminWithJWT
{
    /**
     * The JWT Authenticator.
     *
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $auth;

    /**
     * Create a new AuthenticateWithJWT instance.
     *
     * @param  \Tymon\JWTAuth\JWTAuth  $auth
     *
     * @return void
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param bool $optional
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $optional = null)
    {
        $user = $this->auth->user();

        if($user->id !== 1)
        {
            return $this->respondError(': how did you get here?');
        }

        return $next($request);
    }

    /**
     * Respond with json error message.
     *
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondError($message, $errorCode = 401)
    {
        return response()->json([
            'errors' => [
                'JWT error' => $message,
            ]
        ], $errorCode);
    }

}
