<?php

namespace Acioina\UserManagement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Distilleries\Expendable\Models\OnlineClient;
use Distilleries\Expendable\Helpers\FormUtils;
use Illuminate\Support\Str;
use \CIOINA_Util;

class AuthenticateWithJWT
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
        $this->auth->setRequest($request);

        try {
            // https://stackoverflow.com/questions/49626796/authorization-header-not-reaching-the-server-in-laravel-project
            $header = $request->header('Authorization', '');
            if (Str::startsWith($header, 'Bearer ')) {
                $token = Str::substr($header, 7);
                $this->auth->setToken($token);
            }else{
                return $this->respondError(': token is absent');
            }

            if (! $this->auth->authenticate()) {
                return $this->respondError(': user not found');
            }
            $query = 'SELECT '
             . CIOINA_Util::backquote('id') 
             . ' FROM ' . $this->getFullTableName()
             . ' WHERE ' . CIOINA_Util::backquote('online_id') . '='
             . '\'' . CIOINA_Util::sqlAddSlashes($this->auth->getClaim('oli')) . '\''
             . ' AND ' . CIOINA_Util::backquote('client_id') . '=' 
             . '\'' . CIOINA_Util::sqlAddSlashes($this->auth->getClaim('sub')) . '\'';
            
            $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

            if (count($records) !== 1)
            {
                return $this->respondError(': token has been deleted');
            }

            if(!isset($optional) && FormUtils::getClientLogin() === false)
            {
                return $this->respondError(': session has expired', 422);
            }
        }
        catch (TokenExpiredException $e) {
            return $this->respondError(': token has expired');
        }
        catch (TokenInvalidException $e) {
            return $this->respondError(': token is invalid');
        }
        catch (JWTException $e) {
            return $this->respondError(': token cannot be decoded');
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

    private function getFullTableName()
    {
        return CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
            . '.' . CIOINA_Util::backquote(OnlineClient::getTableNameStatic());
    }
}
