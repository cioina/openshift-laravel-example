<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Http\Controllers\Base\ModelBaseJsonController;

      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use Distilleries\Expendable\Models\Client;
      use Distilleries\Expendable\Helpers\FormUtils;

      use Illuminate\Support\Str;
      use Illuminate\Http\Request;
      use Illuminate\Support\Facades\Hash;

      use \CIOINA_Util;

      class ClientLoginController extends ModelBaseJsonController implements FormStateContract
      {
          const CLIENT_QUESTION_SETTINGS_ID = 35;
          const STEP_ONE_SETTINGS_ID = 36;
          const STEP_TWO_SETTINGS_ID = 37;
          const STEP_THREE_SETTINGS_ID = 38;
          const STEP_FIVE_SETTINGS_ID = 40;
          const LOGIN_SETTINGS_ID = 41;
          const LOGIN_ACTIVATION_SETTINGS_ID = 43;

          const REFERER_ACTION     = '\\Acioina\\UserManagement\\Http\Controllers\\WebPageController@getView';
          const CURRENT_URL_ACTION = '\\Acioina\\UserManagement\\Http\Controllers\\ClientLoginController@postEdit';

          private $onlineClient = false;

          public function __construct(Client $model)
          {
              parent::__construct($model);
          }

          public function getEdit($id = 0)
          {
              if (! $this->createJsonFromInput($id))
              {
                  return;
              }

              if($id < 11)
              {
                  if ($this->canSetOnlineClient($id) === false)
                  {
                      return;
                  }
              }

              if( $id == 1 )
              {
                  $this->sendStepOne();
              }elseif( $id == 2 )
              {
                  $this->sendStepTwo();
              }elseif( $id == 3 )
              {
                  $this->sendStepTree();
              }elseif ( $id == 4 )
              {
                  $this->sendStepFive();
              }elseif( $id == 5 )
              {
                  $this->sendStepSix();
              }elseif( $id == 11 )
              {
                  $this->sendLoginInfo();
              }else{
                  $this->sendJsonMessage('wrong_id_parameter');
              }
          }

          public function postEdit(Request $request)
          {
              $this->getDataParameter();

              if (! $this->ValidateCommonJsonSettings())
              {
                  return;
              }

              if( $this->currentStep < 11)
              {
                  if ($this->canSetOnlineClient($this->currentStep) === false)
                  {
                      return;
                  }
              }

              if( $this->currentStep == 0 )
              {
                  if($this->onlineClient === false)
                  {
                      if ($this->validateCurrentDate() === false)
                      {
                          return;
                      }
                  }

              }elseif( $this->currentStep == 1 )
              {
                  if($this->validateStepOne() === false)
                  {
                      return;
                  }
              }elseif( $this->currentStep == 2 )
              {
                  if($this->validateStepTwo() === false)
                  {
                      return;
                  }
              }elseif( $this->currentStep == 3 )
              {
                  if($this->validateStepThree() === false)
                  {
                      return;
                  }
              }elseif( $this->currentStep == 11 )
              {
                  if($this->validateLogin() === false)
                  {
                      return;
                  }
              }else
              {
                  $this->sendJsonMessage('wrong_id_parameter');
                  return;
              }

              $this->sendJsonMessage('OK', true);
          }

          private function canSetOnlineClient($id = 0)
          {
              $this->onlineClient = FormUtils::getClientLogin();

              if($this->onlineClient === false)
              {
                  if ($id > 0)
                  {
                      if($this->checkFacebookStatus() === false)
                      {
                          return false;
                      }

                      if (! isset($_SESSION[parent::KEY_SESSION_FB_EMAIL]))
                      {
                          $this->sendJsonMessage('fetch_result_count');
                          return false;
                      }
                  }
              }

              return true;
          }

          private function getCount($forceJson = false)
          {
              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ')'
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $today_ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

              if($today_ids == 0 && isset($_SESSION[parent::KEY_SESSION_FB_EMAIL]))
              {
                  $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ')'
                    . ' FROM ' . $this->getFullTableName()
                    . ' WHERE ' . CIOINA_Util::backquote('fb_email') . '='
                    . '\'' . CIOINA_Util::sqlAddSlashes($_SESSION[parent::KEY_SESSION_FB_EMAIL]) . '\'';

                  $ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

                  if($ids > 0)
                  {
                      if($forceJson)
                      {
                          $result = $this->decodeJson(self::CLIENT_QUESTION_SETTINGS_ID,  '"facebookPictureUrl":"NONE"','web-display');
                          if($result === false)
                          {
                              return false;
                          }

                          $setting = $this->getSetting(self::CLIENT_QUESTION_SETTINGS_ID);

                          $temp = FormUtils::makeBlocks(
                          [
                                'login' => $_SESSION[parent::KEY_SESSION_FB_EMAIL],
                          ], $setting->content);

                          $result->data->facebookPictureUrl = $temp->render();
                          $this->sendJson($result);
                      }

                      return false;
                  }
              }

              return $today_ids;
          }

          private function clientExists($forceJson = false)
          {
              if($this->onlineClient !== false)
              {
                  return true;
              }else{
                  $count = $this->getCount($forceJson);
                  if($count === false)
                  {
                      return false;
                  }

                  return  $count == 1;
              }
          }

          protected function getWhere()
          {
              if ($this->onlineClient !== false)
              {
                  return
                    ' WHERE ' . CIOINA_Util::backquote('fb_email') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->onlineClient[0]['fb_email']) . '\'';

              }else{
                  return parent::getWhere();
              }
          }

          private function CheckRecordForUpdate()
          {
              if(! $this->clientExists())
              {
                  $this->sendJsonMessage('fetch_result_count');
                  return false;
              }

              return true;
          }

          private function sendStepOne()
          {
              $inDatabase = $this->clientExists(true);

              $result = $this->decodeJson(self::STEP_ONE_SETTINGS_ID, !$inDatabase ? '"email":"'
                  . $_SESSION[parent::KEY_SESSION_FB_EMAIL] . '"' : $this->getStepOne());
              if($result === false)
              {
                  return;
              }

              if (!$inDatabase)
              {
                  unset($result->schema->properties->changePassword);
              }else{
                  $result->options->fields->Password->disabled = true;
                  $result->options->fields->ConfirmPassword->disabled = true;
              }

              $result->schema->properties->Password->pattern = parent::PASSWORD_PATTERN;
              $result->schema->properties->ConfirmPassword->pattern = parent::PASSWORD_PATTERN;

              $this->sendJson($result);
          }

          private function sendStepTwo()
          {
              if($this->CheckRecordForUpdate() === false)
              {
                  return false;
              }

              $result = $this->decodeJson(self::STEP_TWO_SETTINGS_ID, $this->getStepTwo());
              if($result === false)
              {
                  return;
              }


              $result->schema->properties->phone->pattern = parent::PHONE_PATTERN;
              $result->schema->properties->zip->pattern = parent::ZIP_PATTERN;

              $this->sendJson($result);
          }

          private function sendStepTree()
          {
              if($this->CheckRecordForUpdate() === false)
              {
                  return false;
              }

              $result = $this->decodeJson(self::STEP_THREE_SETTINGS_ID, $this->getStepThree());
              if($result === false)
              {
                  return;
              }

              $result->schema->properties->birthday->pattern = parent::DATE_PATTERN;

              if (isset($result->options->fields->birthday->isBetween))
              {
                  $result->options->fields->birthday->isBetween[0] = FormUtils::getDateIntervalDate(-120)->format(parent::US_DATE_FORMAT);
                  $result->options->fields->birthday->isBetween[1] = FormUtils::getDateIntervalDate(-16)->format(parent::US_DATE_FORMAT);
              }

              $this->sendJson($result);
          }

          private function sendStepFive()
          {
              if($this->CheckRecordForUpdate() === false)
              {
                  return false;
              }

              $result = $this->decodeJson(self::STEP_FIVE_SETTINGS_ID, $this->getAll(), 'web-display');
              if($result === false)
              {
                  return;
              }

              $this->saveStepFive();

              $this->sendJson($result);
          }

          private function sendStepSix()
          {
              FormUtils::forgetSessionMapster();

              $this->sendJsonMessage('OK', true);
          }

          private function sendLoginInfo()
          {
              $client = FormUtils::getClientLogin();

              if($client === false)
              {
                  $this->sendErrorJsonWithParameter('wrong_login', 3);
                  return;
              }

              $result = $this->decodeJson(self::LOGIN_ACTIVATION_SETTINGS_ID, '','web-display');
              if($result === false)
              {
                  return;
              }

              $setting = $this->getSetting(self::LOGIN_ACTIVATION_SETTINGS_ID);

              $temp = FormUtils::makeBlocks(
              [
                    'redirectUrl'        => FormUtils::getRedirect(FormUtils::getDefaultRedirect()),
                    'first_name'         => $client[0]['fb_first_name'],
                    'last_name'          => $client[0]['fb_last_name'],
                    'facebookPictureUrl' => FormUtils::getSimpleImage($client[0]['fb_picture'],
                        'fb',
                         $result->data->imageTitle
                    ),
              ], $setting->content);

              FormUtils::forgetRedirect();

              $result->data->facebookPictureUrl = $temp->render();
              $this->sendJson($result);
          }

          private function validateStepOne()
          {
              $jsonData = $this->getJsonSchema(self::STEP_ONE_SETTINGS_ID);
              if($jsonData === false)
              {
                  return false;
              }

              $jsonData['schema']->schema->properties->FirstName->pattern = parent::PERSON_PATTERN;
              $jsonData['schema']->schema->properties->LastName->pattern = parent::PERSON_PATTERN;
              $jsonData['schema']->schema->properties->Password->pattern = parent::PASSWORD_PATTERN;
              $jsonData['schema']->schema->properties->ConfirmPassword->pattern = parent::PASSWORD_PATTERN;

              if($this->validateJson($jsonData) === false)
              {
                  return false;
              }

              $this->saveStepOne();

              return true;
          }

          private function validateStepTwo()
          {
              $jsonData = $this->getJsonSchema(self::STEP_TWO_SETTINGS_ID);
              if($jsonData === false)
              {
                  return false;
              }

              $jsonData['schema']->schema->properties->phone->pattern = parent::PHONE_PATTERN;
              $jsonData['schema']->schema->properties->zip->pattern = parent::ZIP_PATTERN;

              if($this->validateJson($jsonData) === false)
              {
                  return false;
              }

              if($this->CheckRecordForUpdate() === false)
              {
                  return false;
              }

              $data = $this->getDatabaseJson($this->getStepTwo());
              if ($data !== false)
              {
                  if( !$this->validateDatabaseJson($data, $jsonData))
                  {
                      $this->saveStepTwo();
                  }
              }else{
                  $this->saveStepTwo();
              }

              return true;
          }

          private function validateStepThree()
          {
              $jsonData = $this->getJsonSchema(self::STEP_THREE_SETTINGS_ID);
              if($jsonData === false)
              {
                  return false;
              }

              $jsonData['schema']->schema->properties->birthday->pattern = parent::DATE_PATTERN;

              if($this->validateJson($jsonData) === false)
              {
                  return false;
              }

              if($this->CheckRecordForUpdate() === false)
              {
                  return false;
              }

              $data = $this->getDatabaseJson($this->getStepThree());
              if ($data !== false)
              {
                  if( !$this->validateDatabaseJson($data, $jsonData))
                  {
                      $this->saveStepThree();
                  }
              }else{
                  $this->saveStepThree();
              }

              return true;
          }

          private function validateLogin()
          {
              sleep(1);

              if(FormUtils::getClientLogin() !== false)
              {
                  return true;
              }

              $jsonData = $this->getJsonSchema( self::LOGIN_SETTINGS_ID );
              if($jsonData === false)
              {
                  return false;
              }

              $jsonData['schema']->schema->properties->Password->pattern = parent::PASSWORD_PATTERN;

              if($this->validateJson($jsonData) === false)
              {
                  return false;
              }
              $referrer = FormUtils::tryHttps(action(self::REFERER_ACTION)) . '/';

              $httpReferer = $_SERVER['HTTP_REFERER'];

              if ( ! isset($httpReferer) || ! Str::startsWith($httpReferer, $referrer))
              {
                  $this->sendErrorJsonWithParameter('wrong_login', 4);
                  return false;
              }
              $referrer .= '%';

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
              . ' AND ' . CIOINA_Util::backquote('referrer') . ' LIKE '
              . '\'' . CIOINA_Util::sqlAddSlashes($referrer) . '\''
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
                  . ' AND ' . CIOINA_Util::backquote('referrer') . ' LIKE '
                  . '\'' . CIOINA_Util::sqlAddSlashes($referrer) . '\''
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
              . ' AND ' . CIOINA_Util::backquote('referrer') . ' LIKE '
              . '\'' . CIOINA_Util::sqlAddSlashes($referrer) . '\''
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
                  $this->sendErrorJsonWithParameter('throttle_login', $GLOBALS['CIOINA_Config']->get('LockoutPeriodInMinutes'));
                  return false;
              }

              $query = 'SELECT '
              . CIOINA_Util::backquote('id') .','
              . CIOINA_Util::backquote('ac_code') .','
              . CIOINA_Util::backquote('password') .','
              . CIOINA_Util::backquote('request_ip_address') .','
              . CIOINA_Util::backquote('is_deleted') .','
              . CIOINA_Util::backquote('is_suspended')
              . ' FROM ' . $this->getFullTableName()
              . ' WHERE ' . CIOINA_Util::backquote('fb_email') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->data->Login) . '\'';

              $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

              if (count($records) !== 1)
              {
                  $this->sendErrorJsonWithParameter('wrong_login', 7);
                  return false;
              }

              if($records[0]['is_suspended'] == 1)
              {
                  $this->sendErrorJsonWithParameter('wrong_login', 5);
                  return false;
              }

              if($records[0]['is_deleted'] == 1)
              {
                  $this->sendErrorJsonWithParameter('wrong_login', 6);
                  return false;
              }

              //$pass =  $this->getHash($records[0]['ac_code'], $this->data->Password);
              //if(empty($pass) || $records[0]['password'] !== $pass)

              if(! Hash::check($this->data->Password, $records[0]['password']))
              {
                  $this->sendErrorJsonWithParameter('wrong_login', 7);
                  return false;
              }

              $fb_settings = FormUtils::getFacebookSettings();
              if ($fb_settings === false)
              {
                  $this->sendErrorJsonWithParameter('wrong_login', 8);
                  return false;
              }

              if($fb_settings->data->IsFacebookEnabled)
              {
                  if($records[0]['request_ip_address'] !== CIOINA_Util::getIP())
                  {
                      $this->sendErrorJsonWithParameter('wrong_login', 9);
                      return false;
                  }
              }

              if(isset($this->data->saveLogin))
              {
                  $query = 'UPDATE ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
                    . '.' . CIOINA_Util::backquote('clients')
                    . ' SET '
                    . CIOINA_Util::backquote('updated_at') . '='
                    . '\'' . CIOINA_Util::sqlAddSlashes((new \DateTime())->format('Y-m-d H:i:s')) . '\','
                    . CIOINA_Util::backquote('is_remember_username') . '='
                    . '\'' . CIOINA_Util::sqlAddSlashes(($this->data->saveLogin === true) ? 1 : 0) . '\''
                    . ' WHERE ' . CIOINA_Util::backquote('id') . '='
                    . '\'' . CIOINA_Util::sqlAddSlashes($records[0]['id']) . '\'';
                  $GLOBALS['CIOINA_dbi']->tryQuery($query);
              }

              $query = ' UPDATE ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
              . '.' . CIOINA_Util::backquote('web_statistics')
              . ' SET ' . CIOINA_Util::backquote('request_count') . '= 1'
              . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
              . ' AND ' . CIOINA_Util::backquote('referrer') . ' LIKE '
              . '\'' . CIOINA_Util::sqlAddSlashes($referrer) . '\''
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
                  $this->sendErrorJsonWithParameter('throttle_login', $GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes'));
                  return false;
              }

              FormUtils::setSessionOnlineId($onlineId);

              return true;
          }

          private function saveStepOne()
          {
              sleep(1);

              $ac = CIOINA_Util::getGUID();
              $pass = Hash::make($this->data->Password);//$this->getHash($ac, $this->data->Password);

              $now = (new \DateTime())->format('Y-m-d H:i:s');

              if ($this->clientExists(true))
              {
                  if( isset($this->data->changePassword)
                      && $this->data->changePassword === true)
                  {
                      $query = 'UPDATE ' . $this->getFullTableName()
                       . ' SET '
                       . CIOINA_Util::backquote('updated_at'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                       . CIOINA_Util::backquote('ac_code'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($ac) . '\','
                       . CIOINA_Util::backquote('password'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($pass) . '\','
                       . CIOINA_Util::backquote('is_suspended'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes(0) . '\','
                       . CIOINA_Util::backquote('first_name'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($this->data->FirstName) . '\','
                       . CIOINA_Util::backquote('last_name'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($this->data->LastName) . '\''
                       . $this->getWhere();
                  }else{
                      $query = 'UPDATE ' . $this->getFullTableName()
                       . ' SET '
                       . CIOINA_Util::backquote('updated_at'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                       . CIOINA_Util::backquote('first_name'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($this->data->FirstName) . '\','
                       . CIOINA_Util::backquote('last_name'). '='
                       . '\'' . CIOINA_Util::sqlAddSlashes($this->data->LastName) . '\''
                       . $this->getWhere();
                  }

                  $GLOBALS['CIOINA_dbi']->tryQuery($query);

              }elseif(isset($_SESSION[parent::KEY_SESSION_FB_TOKEN]) &&
                  isset($_SESSION[parent::KEY_SESSION_FB_PICTURE]))
              {
                  list($node, $pictureUrl) = $this->getFacebookData('email,first_name,last_name,id');

                  // Do not allow user registration on live site. Allow new users only for unit testiong.
                  if (!empty($node) && (php_sapi_name() === 'cli'))
                  {
                      $query = 'INSERT INTO ' . $this->getFullTableName()
                      . ' (created_at, updated_at, username, email, fb_email, fb_first_name, fb_last_name,'
                      .' fb_id, fb_token, fb_picture, fb_verified, first_name, last_name,'
                      .' password, request_id, ac_code, request_ip_address) '
                      . ' VALUES('
                      . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getGUID()) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('email')) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('email')) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('first_name')) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('last_name')) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('id')) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($_SESSION[parent::KEY_SESSION_FB_TOKEN]) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($_SESSION[parent::KEY_SESSION_FB_PICTURE]) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes(1) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($this->data->FirstName) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($this->data->LastName) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($pass) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($this->requestId) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes($ac) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\')';

                      $GLOBALS['CIOINA_dbi']->tryQuery($query);

                  }
              }
          }

          private function saveStepTwo()
          {
              $now = (new \DateTime())->format('Y-m-d H:i:s');
              $query = 'UPDATE ' . $this->getFullTableName()
                . ' SET '
                . CIOINA_Util::backquote('updated_at'). '='
                . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                . CIOINA_Util::backquote('phone'). '='
                . (!isset($this->data->phone)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->phone) . '\',')
                . CIOINA_Util::backquote('zip'). '='
                . (!isset($this->data->zip)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->zip) . '\',')
                . CIOINA_Util::backquote('state_code'). '='
                . (!isset($this->data->state)?'NULL':'\''. CIOINA_Util::sqlAddSlashes($this->data->state) . '\'')
                . $this->getWhere();

              $GLOBALS['CIOINA_dbi']->tryQuery($query);
          }

          private function saveStepThree()
          {
              $now = (new \DateTime())->format('Y-m-d H:i:s');
              $query = 'UPDATE ' . $this->getFullTableName()
                . ' SET '
                . CIOINA_Util::backquote('updated_at'). '='
                . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                . CIOINA_Util::backquote('country_code'). '='
                . (!isset($this->data->country)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->country) . '\',')
                . CIOINA_Util::backquote('birthday'). '='
                . (!isset($this->data->birthday)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes(vsprintf('%3$04d-%1$02d-%2$02d',
                    sscanf($this->data->birthday,'%02d%02d%04d'))) . '\',')
                . CIOINA_Util::backquote('gender'). '='
                . (!isset($this->data->Gender)?'NULL':'\''. CIOINA_Util::sqlAddSlashes($this->data->Gender) . '\'')
                . $this->getWhere();

              $GLOBALS['CIOINA_dbi']->tryQuery($query);
          }

          private function saveStepFive()
          {
              $now = (new \DateTime())->format('Y-m-d H:i:s');
              $query = 'UPDATE ' . $this->getFullTableName()
                . ' SET '
                . CIOINA_Util::backquote('updated_at'). '='
                . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                . CIOINA_Util::backquote('request_id'). '= NULL'
                . $this->getWhere();

              $GLOBALS['CIOINA_dbi']->tryQuery($query);
          }

          private function getStepOne()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('first_name') .','
              . CIOINA_Util::backquote('last_name') .','
              . CIOINA_Util::backquote('password') .','
              . CIOINA_Util::backquote('fb_email')
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d =  CIOINA_Util::deleteLastComma(
                       CIOINA_Util::formatJsonString($records[0]['first_name'], 'FirstName')
                     . CIOINA_Util::formatJsonString($records[0]['last_name'], 'LastName')
                     . CIOINA_Util::formatJsonString($records[0]['password'], 'Password', 'thisIsNotapassword0')
                     . CIOINA_Util::formatJsonString($records[0]['password'], 'ConfirmPassword', 'thisIsNotapassword0')
                     . CIOINA_Util::formatJsonString($records[0]['fb_email'], 'email', null, false)
               );

              return $d;
          }

          private function getStepTwo()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('phone') .','
              . CIOINA_Util::backquote('zip') .','
              . CIOINA_Util::backquote('state_code')
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d =  CIOINA_Util::deleteLastComma(
                       CIOINA_Util::formatJsonString($records[0]['phone'], 'phone')
                     . CIOINA_Util::formatJsonString($records[0]['zip'], 'zip')
                     . CIOINA_Util::formatJsonString($records[0]['state_code'], 'state', null, false)
                     );

              return $d;
          }

          private function getStepThree()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('birthday') .','
              . CIOINA_Util::backquote('gender') .','
              . CIOINA_Util::backquote('country_code')
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d = CIOINA_Util::deleteLastComma(
                      CIOINA_Util::formatJsonString($records[0]['country_code'], 'country')
                    . CIOINA_Util::formatJsonString($records[0]['birthday'], 'birthday', CIOINA_Util::getPlaneDate($records[0]['birthday']))
                    . CIOINA_Util::formatJsonString($records[0]['gender'], 'Gender', null, false)
                    );

              return $d;
          }

          private function getAll()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('first_name') .','
              . CIOINA_Util::backquote('last_name') .','
              . CIOINA_Util::backquote('fb_email') .','

              . CIOINA_Util::backquote('phone') .','
              . CIOINA_Util::backquote('zip') .','
              . CIOINA_Util::backquote('state_code') .','

              . CIOINA_Util::backquote('birthday') .','
              . CIOINA_Util::backquote('gender') .','
              . CIOINA_Util::backquote('country_code')
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d = CIOINA_Util::deleteLastComma(
                      CIOINA_Util::formatJsonString($records[0]['first_name'], 'FirstName')
                    . CIOINA_Util::formatJsonString($records[0]['last_name'], 'LastName')
                    . CIOINA_Util::formatJsonString($records[0]['phone'], 'phone', CIOINA_Util::getFormatedPhone($records[0]['phone']))
                    . CIOINA_Util::formatJsonString($records[0]['zip'], 'zip', CIOINA_Util::getFormatedZip($records[0]['zip']))
                    . CIOINA_Util::formatJsonString($records[0]['state_code'], 'state')
                    . CIOINA_Util::formatJsonString($records[0]['country_code'], 'country')
                    . CIOINA_Util::formatJsonString($records[0]['birthday'], 'birthday', CIOINA_Util::getFormatedDate($records[0]['birthday']))
                    . CIOINA_Util::formatJsonString($records[0]['gender'], 'Gender', null, false)
               );

              return $d;
          }

      }