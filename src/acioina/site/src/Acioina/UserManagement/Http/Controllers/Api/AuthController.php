<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Auth;
use Distilleries\Expendable\Models\Client;
use Distilleries\Expendable\Helpers\FormUtils;
use Distilleries\Expendable\Helpers\TranslationUtils;
use Acioina\UserManagement\Http\Requests\Api\LoginUser;
use Acioina\UserManagement\Http\Requests\Api\RegisterUser;
use Acioina\UserManagement\Transformers\UserTransformer;
use \CIOINA_Util;

class AuthController extends ApiController
{
    const CURRENT_URL_ACTION = '\\Acioina\\UserManagement\\Http\Controllers\\Api\\AuthController@login';
    /**
     * AuthController constructor.
     *
     * @param UserTransformer $transformer
     */
    public function __construct(UserTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    /**
     * Login user and return the user if successful.
     *
     * @param LoginUser $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUser $request)
    {
        $credentials = $request->only('user.email', 'user.password');
        $credentials = $credentials['user'];

        if(FormUtils::getClientLogin() !== false)
        {
            if (! Auth::attempt($credentials) ) {
                return $this->respondFailedLogin('email or password', 'is invalid');
            }

            return $this->respondWithTransformer(auth()->user());
        }

        $maxLoginAttempts = $GLOBALS['CIOINA_Config']->get('MaxClientLoginAttempts');
        $lockoutTime = -1 * $GLOBALS['CIOINA_Config']->get('LockoutPeriodInMinutes');

        $nowStr = (new \DateTime())->format('Y-m-d H:i:s');
        $now = FormUtils::getDateIntervalTime(0, $lockoutTime)->format('Y-m-d H:i:s');

        $start = config('app.url');
        $url = '%'. substr(action(self::CURRENT_URL_ACTION), strlen($start));

        $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') FROM '
        . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        . '.' . CIOINA_Util::backquote('web_statistics')
        . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
        . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
        . ' AND ' . CIOINA_Util::backquote('absolute_uri') . ' LIKE '
        . '\'' . CIOINA_Util::sqlAddSlashes($url) . '\''
        . ' AND ' . CIOINA_Util::backquote('request_count') . ' IS NULL'
        . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes($nowStr) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
        . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';

        $count = $GLOBALS['CIOINA_dbi']->fetchValue($query);

        if ($count > $maxLoginAttempts)
        {
            $query = ' UPDATE '
            . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
            . '.' . CIOINA_Util::backquote('web_statistics')
            . ' SET ' . CIOINA_Util::backquote('request_count') . '= '. $maxLoginAttempts
            . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
            . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
            . ' AND ' . CIOINA_Util::backquote('absolute_uri') . ' LIKE '
            . '\'' . CIOINA_Util::sqlAddSlashes($url) . '\''
            . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
            . '\'' . CIOINA_Util::sqlAddSlashes($nowStr) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
            . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
            . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';

            $GLOBALS['CIOINA_dbi']->tryQuery($query);
        }

        $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') FROM '
        . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        . '.' . CIOINA_Util::backquote('web_statistics')
        . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
        . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
        . ' AND ' . CIOINA_Util::backquote('absolute_uri') . ' LIKE '
        . '\'' . CIOINA_Util::sqlAddSlashes($url) . '\''
        . ' AND ' . CIOINA_Util::backquote('request_count') . ' ='
        . '\'' . CIOINA_Util::sqlAddSlashes($maxLoginAttempts) . '\''
        . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes($nowStr) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
        . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';

        $count = $GLOBALS['CIOINA_dbi']->fetchValue($query);

        if ($count > 0)
        {
            return $this->respondFailedLogin('account', 'was locked out for ' . $GLOBALS['CIOINA_Config']->get('LockoutPeriodInMinutes') . ' minutes');
        }

        $query = 'SELECT '
        . CIOINA_Util::backquote('id') .','
        . CIOINA_Util::backquote('ac_code') .','
        . CIOINA_Util::backquote('password') .','
        . CIOINA_Util::backquote('request_ip_address') .','
        . CIOINA_Util::backquote('is_deleted') .','
        . CIOINA_Util::backquote('is_suspended')
        . ' FROM ' . $this->getFullTableName()
        . ' WHERE ' . CIOINA_Util::backquote('email') . '='
        . '\'' . CIOINA_Util::sqlAddSlashes($credentials['email']) . '\'';

        $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

        if (count($records) !== 1)
        {
            return $this->respondFailedLogin('email or password', 'is invalid');
        }

        if($records[0]['is_deleted'] == 1)
        {
            return $this->respondFailedLogin('account', 'was marked for deletion');
        }

        if($records[0]['is_suspended'] == 1)
        {
            return $this->respondFailedLogin('account', 'was suspended');
        }

        $fb_settings = FormUtils::getFacebookSettings();
        if ($fb_settings === false)
        {
            return $this->respondFailedLogin('settings', 'login form is disabled at this time');
        }

        if($fb_settings->data->IsFacebookEnabled)
        {
            if($records[0]['request_ip_address'] !== CIOINA_Util::getIP())
            {
                return $this->respondFailedLogin('account', 'unknown IP address');
            }
        }

        $query = ' UPDATE ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        . '.' . CIOINA_Util::backquote('web_statistics')
        . ' SET ' . CIOINA_Util::backquote('request_count') . '= 1'
        . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
        . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
        . ' AND ' . CIOINA_Util::backquote('absolute_uri') . ' LIKE '
        . '\'' . CIOINA_Util::sqlAddSlashes($url) . '\''
        . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes($nowStr) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
        . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';

        $GLOBALS['CIOINA_dbi']->tryQuery($query);

        $onlineId = CIOINA_Util::getGUID();
        $isError = FormUtils::setOnlineClient($onlineId, $records[0]['id']);

        if ($isError)
        {
            return $this->respondFailedLogin('account', 'was locked out for ' . $GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes') . ' minutes');
        }

        if (! Auth::attempt($credentials) ) {
            return $this->respondFailedLogin('email or password', 'is invalid');
        }

        FormUtils::setSessionOnlineId($onlineId);

        if(!$fb_settings->data->IsFacebookEnabled && $records[0]['id'] == 1)
        {
          $client = Client::findOrFail(1);
          $client->request_ip_address = CIOINA_Util::getIP();
          $client->save();

          $_SESSION[TranslationUtils::KEY_ADMIN_SERVICE_PROVIDER] = 10;
        }

        return $this->respondWithTransformer(auth()->user());
    }

    /**
     * Register a new user and return the user if successful.
     *
     * @param RegisterUser $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUser $request)
    {
        if (php_sapi_name() !== 'cli') {
            return $this->respond([
                'errors' => [
                    'register' => 'not implemented yet',
                ]
            ], 422);
        }

        $user = Client::create([
            'first_name' => $request->input('user.username'),
            'last_name' => $request->input('user.username'),
            'email' => $request->input('user.email'),
            'password' => $request->input('user.password'),
            'username' => $request->input('user.username'),

            'fb_email' => $request->input('user.email'),
            'fb_first_name' => $request->input('user.username'),
            'fb_last_name' => $request->input('user.username'),
            'fb_id' => $request->input('user.email'),
            'fb_token' => $request->input('user.email'),
            'fb_picture' => $request->input('user.email'),
        ]);

        return $this->respondWithTransformer($user);
    }

    /**
     * Respond with failed login.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondFailedLogin($key ='email or password', $message = 'unknown')
    {
        return $this->respond([
            'errors' => [
                $key => $message,
            ]
        ], 422);
    }

    private function getFullTableName()
    {
        return CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
            . '.' . CIOINA_Util::backquote(Client::getTableNameStatic());
    }

    private function validateLogin($email = '', $password = '')
    {

        return true;
    }

}
