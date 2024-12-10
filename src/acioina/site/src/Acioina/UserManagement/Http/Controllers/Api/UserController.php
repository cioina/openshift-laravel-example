<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Distilleries\Expendable\Models\Client;
use Acioina\UserManagement\Http\Requests\Api\UpdateUser;
use Acioina\UserManagement\Http\Requests\Api\PasswordUser;
use Acioina\UserManagement\Transformers\UserTransformer;
use Acioina\UserManagement\Transformers\IncompleteUserTransformer;
use Distilleries\Expendable\Helpers\FormUtils;
use Illuminate\Support\Facades\Hash;

use \CIOINA_Util;

class UserController extends ApiController
{
    const CURRENT_URL_ACTION = '\\Acioina\\UserManagement\\Http\Controllers\\Api\\UserController@login';
    protected $incompleteTransformer = null;
    /**
     * UserController constructor.
     *
     * @param UserTransformer $transformer
     * @param IncompleteUserTransformer $incomplete
     */
    public function __construct(UserTransformer $transformer, IncompleteUserTransformer $incompleteTransformer)
    {
        parent::__construct($transformer);
        $this->middleware('auth.api')->except('login');
        $this->middleware('auth.api:optional')->only('login');
        $this->incompleteTransformer = $incompleteTransformer;
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $this->transformer = $this->incompleteTransformer;
        return $this->respondWithTransformer(auth()->user());
    }

    /**
     * Update the authenticated user and return the user if successful.
     *
     * @param UpdateUser $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUser $request)
    {
        if (! $request->has('user'))
        {
            return $this->respondFailedLogin('wrong data', 'there is nothing to update');
        }

        $data = $request->only(
            'user.username',
            'user.password'
            );
        if(isset($data) && is_array($data) && count($data) === 0)
        {
            return $this->respondFailedLogin('empty data', 'there is nothing to update');
        }
        $data = $data['user'];
        foreach($data as $key => $value)
        {
            if(empty($value))
            {
                return $this->respondFailedLogin($key, 'cannot be empty');
            }
        }

        $user = auth()->user();
        $user->update($data);

        $onlineId = CIOINA_Util::getGUID();
        FormUtils::setOnlineClient($onlineId, $user->id, false);
        FormUtils::setSessionOnlineId($onlineId);

        return $this->respondWithTransformer($user);
    }

    public function login(PasswordUser $request)
    {
        $user = auth()->user();

        $credentials = $request->only('user.password');
        $credentials = $credentials['user'];

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
        . '\'' . CIOINA_Util::sqlAddSlashes($user->email) . '\'';

        $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

        if (count($records) !== 1)
        {
            return $this->respondFailedLogin('email', 'is invalid');
        }

        if($records[0]['is_deleted'] == 1)
        {
            return $this->respondFailedLogin('account', 'was marked for deletion');
        }

        if($records[0]['is_suspended'] == 1)
        {
            return $this->respondFailedLogin('account', 'was suspended');
        }

        if(! Hash::check($credentials['password'], $user->password))
        {
            return $this->respondFailedLogin('password', 'is invalid');
        }

        $fb_settings = FormUtils::getFacebookSettings();
        if ($fb_settings === false)
        {
            return $this->respondFailedLogin('login', 'is not allowed at this time');
        }

        if($fb_settings->data->IsFacebookEnabled)
        {
            if($records[0]['request_ip_address'] !== CIOINA_Util::getIP())
            {
                return $this->respondFailedLogin('IP address', 'is suspended');
            }
        }

        $onlineId = CIOINA_Util::getGUID();
        FormUtils::setOnlineClient($onlineId, $records[0]['id'], false);

        $sessionTime = -1 *($GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes') + 2);
        $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') FROM '
        . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        . '.' . CIOINA_Util::backquote('online_clients')
        . ' WHERE ' . CIOINA_Util::backquote('client_id') . '='
        . '\'' . CIOINA_Util::sqlAddSlashes($records[0]['id']) . '\''
        . ' AND ' . CIOINA_Util::backquote('is_logged_out') . '= 0'
        . ' AND ' . CIOINA_Util::backquote('updated_at') . '<= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes(FormUtils::getDateIntervalTime(0, -1)->format('Y-m-d H:i:s')) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
        . ' AND ' . CIOINA_Util::backquote('updated_at') . '>= STR_TO_DATE('
        . '\'' . CIOINA_Util::sqlAddSlashes(FormUtils::getDateIntervalTime(0, $sessionTime)->format('Y-m-d H:i:s')) . '\','
        . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';

        $count = $GLOBALS['CIOINA_dbi']->fetchValue($query);

        if ($count >= $GLOBALS['CIOINA_Config']->get('TotalOnlineClientLogins') )
        {
            unset($_SESSION[FormUtils::KEY_SESSION_ONLINE_ID]);
            return $this->respondFailedLogin('account', 'was locked out for ' . $GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes') . ' minutes');
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

        FormUtils::setSessionOnlineId($onlineId);
        return $this->respondWithTransformer($user);
    }

    /**
     * Respond with failed login.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondFailedLogin($key ='session', $message = 'unknown')
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

}
