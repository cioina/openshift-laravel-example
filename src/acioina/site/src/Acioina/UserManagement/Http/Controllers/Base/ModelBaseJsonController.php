<?php namespace Acioina\UserManagement\Http\Controllers\Base;

      use Acioina\UserManagement\Http\Controllers\Controller as AppController;

     
      use Distilleries\Expendable\Helpers\Jsv4;
      use Distilleries\Expendable\Models\Setting;
      use Distilleries\Expendable\Helpers\FormUtils;
      
      use Illuminate\Support\Facades\Request;
      use Illuminate\Database\Eloquent\Model;

      use \CIOINA_Util;
      use \CIOINA_Response;

      use \Facebook\Facebook;
      use \Facebook\Exceptions\FacebookSDKException;
      use \Facebook\Exceptions\FacebookResponseException;
      use \Facebook\Authentication\AccessToken;

      class ModelBaseJsonController extends AppController 
      {
          // These are "global" keys
          const KEY_SESSION_LOGIN_URL  = 'fb_loginUrl';
          const KEY_SESSION_LOGOUT_URL = 'fb_logoutUrl';
          const KEY_SESSION_FB_TOKEN   = 'fb_token';
          const KEY_SESSION_FB_PICTURE = 'fb_picture';
          const KEY_SESSION_FB_EMAIL   = 'fb_email';

          const FACEBOOK_LOGIN_SETTINGS_ID = 26;
          const FACEBOOK_LOGIN_URL_SETTINGS_ID = 27;
          const FACEBOOK_LOGOUT_URL_SETTINGS_ID = 30;
          const FACEBOOK_STATUS_SETTINGS_ID = 31;

          const COMMON_JSON_SETTINGS_ID = 3;

          const US_DATE_FORMAT = 'm/d/Y';

          const PASSWORD_PATTERN = "^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{2,})\S$";
          const PHONE_PATTERN = "^(\\D?(\\d{3})\\D?(\\d{3})\\D?\\D?(\\d{4})\\D?\\D?(\\d{5})?)?$";
          const ZIP_PATTERN = "^((\\d{5})\\D?(\\d{4})?)?$";
          const DATE_PATTERN = "^((\\d{2})\\D?(\\d{2})\\D?(\\d{4}))?$";
          const PERSON_PATTERN = "^((([A-Z]{1}[a-z]{1,}){1,2}|[A-Z]{1}[a-z]{0,}[\'][A-Z]{1}[a-z]{1,})[- ]){0,}(([A-Z]{1}[a-z]{1,}){1,2}|[A-Z]{1}[a-z]{0,}[\'][A-Z]{1}[a-z]{1,})$";

          protected $model;
          protected $currentStep;
          protected $data;
          protected $requestId;

          public function __construct(Model $model)
          {
              $this->model = $model;
          }

          public function getFacebookData($param = 'email', $isPicture = false)
          {
              $fb_settings = FormUtils::getFacebookSettings();
              if ($fb_settings === false)
              {
                  $this->sendJsonMessage('system_error');
                  return null;
              }

              if(! $fb_settings->data->IsFacebookEnabled)
              {
                  $this->sendJsonMessage('facebook_disabled'); 
                  return null;
              }
              
              if ( isset( $_SESSION[self::KEY_SESSION_FB_TOKEN] ) ) 
              {
                  $accessToken = $_SESSION[self::KEY_SESSION_FB_TOKEN];
                  if (! $accessToken instanceof AccessToken) {
                      $accessToken = null;
                  }
                  elseif ($accessToken->isExpired())
                  {
                      $accessToken = null;
                  }
              } else {
                  $accessToken = null;
              }

              if ( ! isset( $accessToken ) ) { 
                  $this->sendErrorJsonWithParameter('graph_error', trans('user-management::errors.facebook_token') );
                  return null;
              }

              try {
                  
                  $fb = new Facebook([
                 'app_id'     => $GLOBALS['CIOINA_Config']->get('FacebookAppId'),
                 'app_secret' => $GLOBALS['CIOINA_Config']->get('FacebookAppSecret'),
                 'default_graph_version' => $GLOBALS['CIOINA_Config']->get('FacebookGraphVersion')]);
              }
              catch(Exception $e) {
                  $this->sendErrorJsonWithParameter('graph_error', 'Facebook object returned an error: ' . $e->getMessage());
                  return null;
              }

              try {
                  $fb->setDefaultAccessToken($accessToken);
              }
              catch(FacebookSDKException $e) {
                  $this->sendErrorJsonWithParameter('graph_error', 'OAuth 2.0 client handler');
                  return null;
              }

              try {
                  $response = $fb->get('/me?fields=' . $param);
                  $node = $response->getGraphNode();

                  if($isPicture)
                  {
                      $res = $fb->get( '/me/picture?type=large&redirect=false' );
                      $picture = $res->getGraphObject();
                  }
              }
              catch(FacebookResponseException $e) {
                  $this->sendErrorJsonWithParameter('graph_error', 'Graph returned an error: ' . $e->getMessage());
                  return null;
              }
              catch(FacebookSDKException $e) {
                  $this->sendErrorJsonWithParameter('graph_error', 'Facebook SDK returned an error: ' . $e->getMessage());
                  return null;
              }

              return [$node, ($isPicture ? $picture['url'] : '#'),];
          }

          public function checkFacebook($facebookRedirectUrl = '#')
          {
              $fb_settings = FormUtils::getFacebookSettings();
              if ($fb_settings === false)
              {
                  $this->sendJsonMessage('system_error');
                  return false;
              }

              if(! $fb_settings->data->IsFacebookEnabled)
              {
                  $this->sendJsonMessage('facebook_disabled');
                  return false;
              }

              try {
                  
                  $fb = new Facebook([
                 'app_id'     => $GLOBALS['CIOINA_Config']->get('FacebookAppId'),
                 'app_secret' => $GLOBALS['CIOINA_Config']->get('FacebookAppSecret'),
                 'default_graph_version' => $GLOBALS['CIOINA_Config']->get('FacebookGraphVersion')]);
              }
              catch(Exception $e) {
                  $this->sendJsonMessage('system_error');
                  return false;
              }

              try {
                  $newHelper = $fb->getRedirectLoginHelper();

                  if ( isset( $_SESSION[self::KEY_SESSION_FB_TOKEN] ) ) {
                      $accessToken = $_SESSION[self::KEY_SESSION_FB_TOKEN];
                      if (! $accessToken instanceof AccessToken) {
                          $accessToken = null;
                      }
                      elseif ($accessToken->isExpired())
                      {
                          $accessToken = null;
                      }
                  } else {
                      $accessToken = $newHelper->getAccessToken();
                  }
              }
              catch(FacebookResponseException $e) {
                  $accessToken = null;
              }
              catch(FacebookSDKException $e) {
                  $accessToken = null;
              }

              if ( isset( $accessToken ) ) 
              {
                  $_SESSION[self::KEY_SESSION_FB_TOKEN] = $accessToken;
                  if ( isset( $_SESSION[self::KEY_SESSION_LOGIN_URL] ) ) 
                  {
                      unset($_SESSION[self::KEY_SESSION_LOGIN_URL]);
                      try {
                          $fb->setDefaultAccessToken($accessToken);
                      }
                      catch(FacebookSDKException $e) {
                          $this->sendErrorJsonWithParameter('graph_error', 'OAuth 2.0 client handler');
                      }
                      
                      try {
                          $response = $fb->get('/me?fields=email');
                          $node = $response->getGraphNode();
                          
                          $res = $fb->get( '/me/picture?type=large&redirect=false' );
                          $picture = $res->getGraphObject();

                          $_SESSION[self::KEY_SESSION_FB_PICTURE] = $picture['url'];
                          $_SESSION[self::KEY_SESSION_FB_EMAIL] = $node->getField('email');
                      }
                      catch(FacebookResponseException $e) {
                          $this->sendErrorJsonWithParameter('graph_error', 'Graph returned an error: ' . $e->getMessage());
                      }
                      catch(FacebookSDKException $e) {
                          $this->sendErrorJsonWithParameter('graph_error', 'Facebook SDK returned an error: ' . $e->getMessage());
                      }
                      
                      return true;
                  }elseif ( isset( $_SESSION[self::KEY_SESSION_FB_PICTURE] ) ) {
                      unset($_SESSION[self::KEY_SESSION_LOGOUT_URL]);

                      return true;
                  }else{   
                      $logoutURL = $newHelper->getLogoutUrl( $accessToken,  $facebookRedirectUrl);
                      $_SESSION[self::KEY_SESSION_LOGOUT_URL] = $logoutURL;
                  }
                  
              } else {
                  $loginUrl = $newHelper->getLoginUrl($facebookRedirectUrl , $fb_settings->data->permissions);
                  unset($_SESSION[self::KEY_SESSION_LOGOUT_URL]);
                  unset($_SESSION[self::KEY_SESSION_FB_TOKEN]);
                  unset($_SESSION[self::KEY_SESSION_FB_PICTURE]);

                  $_SESSION[self::KEY_SESSION_LOGIN_URL] = $loginUrl;
              }

              return false;
          }

          public function checkFacebookStatus($forceSendJson = false)
          {
              $result = $this->decodeJson(self::FACEBOOK_LOGIN_URL_SETTINGS_ID, '','web-display');
              if($result === false)
              {
                  return false;
              }

              $redirect = config('app.url') . '/contact/edit/10';
              unset($_SESSION[self::KEY_SESSION_LOGIN_URL]);
              unset($_SESSION[self::KEY_SESSION_LOGOUT_URL]);
              $isFacebook = false;

              if ($this->checkFacebook($redirect))
              {
                  if(isset($_SESSION[self::KEY_SESSION_FB_PICTURE]))
                  {
                      $isFacebook = true;
                      if($forceSendJson)
                      {
                          $setting = $this->getSetting(self::FACEBOOK_LOGIN_URL_SETTINGS_ID);

                          $temp = FormUtils::makeBlocks(
                          [
                                'redirectUrl'        => FormUtils::getDefaultRedirect(),      
                                'facebookPictureUrl' => FormUtils::getSimpleImage($_SESSION[self::KEY_SESSION_FB_PICTURE], 
                                    'fb',
                                    $result->data->imageTitle
                                ),
                          ], $setting->content);

                          $result->data->facebookPictureUrl = $temp->render();
                      }
                  }else{
                      unset($_SESSION[self::KEY_SESSION_LOGOUT_URL]);

                      $setting = $this->getSetting(self::FACEBOOK_STATUS_SETTINGS_ID);
                      $temp = FormUtils::makeBlocks(
                      [
                            'login' => 'logout',
                      ], $setting->content);

                      $result->data->facebookPictureUrl = $temp->render();
                  }
                  
                  if (! $isFacebook || $forceSendJson)
                  {
                      unset($result->data->facebookLoginUrl);
                      $this->sendJson($result);
                      return false;
                  }

              }elseif(isset($_SESSION[self::KEY_SESSION_LOGIN_URL]) || 
                      isset($_SESSION[self::KEY_SESSION_LOGOUT_URL]))
              {
                  unset($_SESSION[self::KEY_SESSION_LOGIN_URL]);
                  
                  if ($forceSendJson || ! isset($_SESSION[self::KEY_SESSION_FB_EMAIL]))
                  {
                      $setting = $this->getSetting(self::FACEBOOK_LOGOUT_URL_SETTINGS_ID);

                      $result->data->facebookPictureUrl = $setting->content;

                      unset($result->data->facebookLoginUrl);
                      $this->sendJson($result);
                      return false;
                  }
              }

              return true;
          }

          protected function validateCurrentDate()
          {
              $jsonData = $this->getJsonSchema(self::FACEBOOK_LOGIN_SETTINGS_ID);

              if ($jsonData === false)
              {
                  return false;
              }

              $jsonData['schema']->schema->properties->today->pattern = self::DATE_PATTERN;

              if( $this->validateJson($jsonData) === false)
              {
                  return false;
              }

              $today = (new \DateTime())->format('mdY');
              if ($today !== $this->data->today)
              {
                  $this->sendJsonMessage('incorrect_today_date');
                  return false;
              }

              return true;
          }

          protected function getFullTableName()
          {
              return CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                  . '.' . CIOINA_Util::backquote($this->model->getTable());
          }

          protected function sendResponce()
          {
              if (! defined('TESTSUITE')) {
                  exit;
              }else{
                  CIOINA_Response::response(); 
              }
          }

          protected function sendJsonMessage($message = '', $isSuccess = false)
          {
              $response = CIOINA_Response::getInstance();
              $response->isSuccess($isSuccess);
              $response->addJSON('message', $message);

              $this->sendResponce();
          }

          protected function getSetting( $id = 0)
          {
              $setting = Setting::withoutTranslation()->findOrFail($id); 

              $id_element = $setting->hasBeenTranslated($setting->getTable(), $id, app()->getLocale());
              if (! empty($id_element)) 
              {
                  $setting = Setting::withoutTranslation()->findOrFail($id_element);
              }
              return $setting;
          }

          protected function decodeJson($id = 0, $data = '', $view = 'web-edit')
          {
              try {
                  $setting = $this->getSetting($id);

                  $rec = json_decode('{"data": {'. $data . '},'
                     . $setting->code_block . '}');

                  if(! isset($rec))
                  {
                      $this->sendJsonMessage('json_decode_exception');
                      return false;
                  }

                  $rec->view = $view;
                  $rec->postRender = "NONE";

                  return $rec;
              }
              catch (Exception $e) {
                  $this->sendJsonMessage('system_error');
                  return false;
              }
          }

          protected function sendJson($jsonData = null, $next = 'next')
          {
              $response = CIOINA_Response::getInstance();
              $response->isSuccess(true);

              $response->addJSON($next, $jsonData);

              $this->sendResponce();
          }

          protected function getJsonSchema($id = 0)
          {
              try {
                  $setting = Setting::withoutTranslation()->findOrFail($id);

                  $schema = json_decode('{' . $setting->code_block . '}');

                  if (empty($schema))
                  {
                      $this->sendErrorJsonWithParameter('json_validation', '$setting->code_block');
                      return false;
                  }

                  return [
                        'schema'      => $schema,
                        'code_block'  => $setting->code_block,
                      ];
              }
              catch (Exception $e) {
                  $this->sendJsonMessage('system_error');
                  return false;
              }
          }

          protected function sendErrorJsonWithParameter( $errorId = 'system_error', $parameter = 'NONE' )
          {
              $response = CIOINA_Response::getInstance();
              $response->isSuccess(false);
              $response->addJSON('message', $errorId);
              $response->addJSON('param1', $parameter);

              $this->sendResponce();
          }

          protected function validateJson( array $jsonData = null )
          {
              try {
                  $baseSettings = json_decode('{' . $jsonData['code_block'] . '}', true);

                  foreach($baseSettings['schema']['properties'] as $key => $value)
                  {
                      $result = Jsv4::isValidMember($this->data, $jsonData['schema'], $key);
                      if( isset($result) && $result === false)
                      {
                          $this->sendErrorJsonWithParameter('json_validation', $key);
                          return false;
                      }
                  }
              }
              catch (Exception $e) {
                  $this->sendJsonMessage('system_error');
                  return false;
              }

              return true;
          }

          protected function validateDatabaseJson($data = null, array $jsonData = null )
          {
              try {
                  $baseSettings = json_decode('{' . $jsonData['code_block'] . '}', true);

                  foreach($baseSettings['schema']['properties'] as $key => $value)
                  {
                      $result = Jsv4::isValidDatabaseMember($data, $jsonData['schema'], $key, $this->data);
                      if( isset($result) && $result === false)
                      {
                          return false;
                      }
                  }
              }
              catch (Exception $e) {
                  $this->sendJsonMessage('system_error');
              }

              return true;
          }

          protected function getDatabaseJson($data='')
          {
              try {
                  $result = json_decode('{'.$data.'}');

                  if(! isset($result))
                  {
                      return false;
                  }
              }
              catch (Exception $e) {
                  $this->sendJsonMessage('system_error');
              }

              return $result;
          }

          protected function createJsonFromInput($id = null)
          {
              if (!is_numeric($id))
              {
                  $this->sendJsonMessage('not_numeric_parameters');
                  return false;
              }  
              
              $robot = Request::get('robot', null);
              $formid = Request::get('formid', null);

              if(! isset($robot) || ! isset($formid) || empty($robot) || empty ($formid))
              {
                  $this->sendErrorJsonWithParameter('empty_parameters', $id);
                  return false;
              }

              $data   = 
                '"Status": "' . $robot . '",
                 "ID": "' . $formid . '",
                 "StepId": ' . $id;

              if ($this->getDataFromJson($data))
              {
                  return $this->ValidateCommonJsonSettings(); 
              }else{
                  return false;
              }
          }

          protected function fetchResult($query = null)
          {
              $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

              if (count($records) != 1)
              {
                  $this->sendJsonMessage('fetch_result_count');
              }

              return $records; 
          }

          protected function getDataFromJson($data='')
          {
              try {
                  $this->data = json_decode('{'.$data.'}');

                  if(! isset($this->data))
                  {
                      $this->sendJsonMessage('json_decode_exception');
                      return false;
                  }
              }
              catch (Exception $e) {
                  $this->sendJsonMessage('system_error');
                  return false;
              }
              return true;
          }

          protected function getDataParameter()
          {
              $this->data = json_decode($this->getParams('Data', null));
          }

          protected function ValidateCommonJsonSettings()
          {
              $imageMapKeys = FormUtils::getSessionImageMapKeys();
              $imageMapId = FormUtils::getSessionImageMapId();

              if ( empty($imageMapKeys) || 
                   empty($imageMapId)) 
              {
                  $this->sendErrorJsonWithParameter('no_session_keys', 1);
                  return false;
              }

              if (empty($this->data)) 
              {
                  $this->sendJsonMessage('no_us_states_selected');
                  return false;
              }

              $jsonData = $this->getJsonSchema(self::COMMON_JSON_SETTINGS_ID);

              if($jsonData === false)
              {
                  return false;
              }

              if ($this->validateJson($jsonData) === false)
              {
                  return false;
              }
              
              $arr_str1 = explode(',', FormUtils::getSessionImageMapKeys());
              $arr_str2 = explode(',', $this->data->Status);

              $test_size = sizeof($arr_str1);
              if($test_size != sizeof($arr_str2))
              {
                  $this->sendJsonMessage('wrong_us_states_count');
                  return false;
              }

              $arr_str3 = CIOINA_arrayMergeRecursive($arr_str1, $arr_str2);
              if($test_size != sizeof($arr_str3))
              {
                  $this->sendJsonMessage('wrong_us_states');
                  return false;
              }
              foreach ($arr_str3 as $value)
              {
                  if(! in_array($value, $arr_str1))
                  {
                      $this->sendJsonMessage('wrong_us_states');
                      return false;
                  }
              }
              
              if(FormUtils::getSessionImageMapId() != $this->data->ID) 
              {
                  $this->sendJsonMessage('wrong_session');
                  return false;
              }

              $this->currentStep = $this->data->StepId;
              $this->requestId = $this->data->ID;

              return true;
          }

          protected function getWhere()
          {
              $sessionTime = -2 * $GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes');

              $query = ' WHERE ' . CIOINA_Util::backquote('created_at'). '<= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes((new \DateTime())->format('Y-m-d H:i:s')) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
              . ' AND ' . CIOINA_Util::backquote('created_at'). '>= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes(FormUtils::getDateIntervalTime(0, $sessionTime)->format('Y-m-d H:i:s')) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
              . ' AND ' . CIOINA_Util::backquote('request_ip_address') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
              . ' AND ' . CIOINA_Util::backquote('request_id') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->requestId) . '\'';

              return $query;
          }

          //protected function getHash($ac = '', $password = '')
          //{
          //    if( strlen($ac) !== 32 || strlen($password) < 10)
          //    {
          //        $this->sendErrorJsonWithParameter('wrong_login', 1);
          //        return null;
          //    }

          //    $gui = $back = vsprintf('%1$08s-%2$04s-%3$04s-%4$04s-%5$12s', sscanf($ac,'%08s%04s%04s%04s%12s'));
          //    $arr1 = explode('-', $gui);
          //    $back = vsprintf('%4$02s-%3$02s-%2$02s-%1$02s', sscanf($password,'%02s%02s%02s%02s'));
          //    $arr2 = explode('-', $back);
          //    $last =  /*overload*/mb_substr($password, 8);
          //    $pass = md5($arr1[4] . $arr2[3] . $last . $arr1[3] . $arr2[0] . $arr1[2] . $arr2[1] . $arr1[1] . $arr2[2]  . $arr1[0]); 
          //    return $pass;
          //}

          protected function getParams($key, $default_value)
          {
              $element = Request::get($key);

              if (empty($element)) 
              {
                  $element = $default_value;
              }

              return $element;
          }

          protected function escapeAndNormalize($member)
          {
              if(isset($this->data->{$member}))
              {
                  $this->data->{$member} = FormUtils::escapeAndNormalize($this->data->{$member});
              }
          }

          protected function escapeBackslash($value)
          {
              return FormUtils::escapeBackslash($value);
          }

      }