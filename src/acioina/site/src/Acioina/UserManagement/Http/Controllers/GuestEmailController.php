<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Http\Controllers\Base\ModelBaseJsonController;

      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use Distilleries\Expendable\Models\GuestEmail;
      use Distilleries\Expendable\Helpers\FormUtils;
      
      use Illuminate\Support\Facades\Request;
      use Illuminate\Http\Request as HttpRequest;

      use \CIOINA_Util;
      use \Mailgun\Mailgun;

      class GuestEmailController extends ModelBaseJsonController implements FormStateContract
      {
          const STEP_ONE_SETTINGS_ID = 6;
          const STEP_TWO_SETTINGS_ID = 7;
          const STEP_THREE_SETTINGS_ID = 8;
          const STEP_FOUR_SETTINGS_ID = 9;
          const FACEBOOK_EMAIL_SETTINGS_ID = 33;

          private $isBackFromFacebook = false;
          
          public function __construct(GuestEmail $model)
          {
              parent::__construct($model);
          }

          public function getEdit($id = 0)
          {
              $robot = Request::get('robot', null);
              $formid = Request::get('formid', null);

              if( ! isset($robot) && ! isset($formid) )
              {
                  if (
                         ($id == 10 && isset($_SESSION[parent::KEY_SESSION_LOGIN_URL])) || 
                         ($id == 11 && isset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]))
                     )
                  {
                  }elseif ($id == 10 && isset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]))
                  {
                      $id = 11;
                      $this->isBackFromFacebook = true;
                  }elseif ($id == 10) 
                  {
                      if (isset($_SESSION[parent::KEY_SESSION_FB_PICTURE]) && 
                         isset($_SESSION[parent::KEY_SESSION_FB_EMAIL]))
                      {
                          $result = $this->decodeJson(parent::FACEBOOK_LOGIN_URL_SETTINGS_ID, '','web-display');
                          if($result === false)
                          {
                              $this->redirectToWebPageList();
                              
                          }else{
                              $this->redirectToWebPageList([$result->data->messageBag]);
                          }
                      }
                      
                      if (! $this->createJsonFromInput($id))
                      {
                          return;
                      }
                  }elseif (! $this->createJsonFromInput($id))
                  {
                      return;
                  }

              }elseif (! $this->createJsonFromInput($id))
              {
                  return;
              }              

              if( $id == 0 )
              {
                  $this->sendStepOne();
              }elseif( $id == 1 )
              {
                  $this->sendStepTwo();
              }elseif( $id == 2 )
              {
                  $this->sendStepThree();
              }elseif ( $id == 3 )
              {
                  $this->sendStepFour();
              }elseif( $id == 4 ) 
              {
                  $this->sendStepFive();
              }elseif( $id == 10 ) 
              {
                  $this->sendFacebookLogin();
              }elseif( $id == 11 ) 
              {
                  $this->sendFacebookLogout();
              }elseif( $id == 12 ) 
              {
                  $this->checkFacebookStatus(true);
              }elseif( $id == 13 ) 
              {
                  $this->sendEmail();
              }elseif( $id == 14 ) 
              {
                  $this->sendSuccessEmailHtml();
              }else{
                  $this->sendJsonMessage('wrong_id_parameter');
              }
          }

          public function postEdit(HttpRequest $request)
          {
              $this->getDataParameter();

              if (! $this->ValidateCommonJsonSettings())
              {
                  return;
              }

              if( $this->currentStep == 0 )
              {
                  if ($this->validateStepOne() === false)
                  {
                      return;
                  }
              }elseif( $this->currentStep == 1 )
              {
                  if ($this->validateStepTwo() === false)
                  {
                      return;
                  }
              }elseif( $this->currentStep == 2 )
              {
                  if($this->validateStepThree() === false)
                  {
                      return;
                  }
              }elseif( 
                  $this->currentStep == 10 || 
                  $this->currentStep == 11 || 
                  $this->currentStep == 12 || 
                  $this->currentStep == 13)
              {
                  if ($this->validateCurrentDate() === false)
                  {
                      return;
                  }
              }elseif( $this->currentStep == 14 ) 
              {
                  if($this->sendFacebookEmail() === false)
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

          private function CheckRecordForUpdate()
          {
              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ')'
                . ' FROM ' . $this->getFullTableName()
                . $this->getWhere();

              $today_ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

              if ($today_ids != 1)
              {
                  $this->sendJsonMessage('wrong_session');
                  return false;
              }

              return true;
          }

          private function sendStepOne()
          {
              $result = $this->decodeJson(self::STEP_ONE_SETTINGS_ID, $this->getStepOne());
              if($result === false)
              {
                  return;
              }

              $this->sendJson($result);
          }

          private function sendStepTwo()
          {
              $result = $this->decodeJson(self::STEP_TWO_SETTINGS_ID, $this->getStepTwo());
              if($result === false)
              {
                  return;
              }

              $result->schema->properties->phone->pattern = parent::PHONE_PATTERN;
              $result->schema->properties->zip->pattern = parent::ZIP_PATTERN;

              $this->sendJson($result);
          }

          private function sendStepThree()
          {
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

          private function sendStepFour()
          {
              $result = $this->decodeJson(self::STEP_FOUR_SETTINGS_ID, $this->getAll(), 'web-display');
              if($result === false)
              {
                  return;
              }

              $this->saveStepFour();
              $this->sendJson($result);
          }

          private function sendStepFive()
          {
              FormUtils::forgetSessionMapster();

              $this->sendJsonMessage('OK', true);
          }

          private function LoginAndUpdateClient()
          {
              sleep(1);

              if(FormUtils::getClientLogin() === false)
              {
                  list($node, $pictureUrl) = $this->getFacebookData('email,first_name,last_name,id');

                  if (empty($node))
                  {
                      return false;
                  }

                  $query = 'SELECT '
                  . CIOINA_Util::backquote('id') .','
                  . CIOINA_Util::backquote('is_deleted') .','
                  . CIOINA_Util::backquote('is_suspended') 
                  . ' FROM '. CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                  . '.' . CIOINA_Util::backquote('clients')
                  . ' WHERE ' . CIOINA_Util::backquote('fb_email') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('email')) . '\'';

                  $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

                  if (count($records) === 1)
                  {
                      if($records[0]['is_deleted'] == 1)
                      {
                          $this->sendErrorJsonWithParameter('wrong_login', 6);
                          return false;
                      }

                      $onlineId = CIOINA_Util::getGUID();

                      $isError = FormUtils::setOnlineClient($onlineId, $records[0]['id']);

                      if ($isError)
                      {
                          $this->sendErrorJsonWithParameter('throttle_login', $GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes'));
                          return false;
                      }

                      FormUtils::setSessionOnlineId($onlineId);

                      $now = (new \DateTime())->format('Y-m-d H:i:s');

                      $query = 'UPDATE ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
                      . '.' . CIOINA_Util::backquote('clients')
                      . ' SET ' 
                      . CIOINA_Util::backquote('updated_at') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                      . CIOINA_Util::backquote('fb_first_name') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('first_name')) . '\','
                      . CIOINA_Util::backquote('fb_last_name') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('last_name')) . '\','
                      . CIOINA_Util::backquote('fb_id') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes($node->getField('id')) . '\','
                      . CIOINA_Util::backquote('fb_token') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes($_SESSION[parent::KEY_SESSION_FB_TOKEN]) . '\','
                      . CIOINA_Util::backquote('fb_picture') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes(isset($_SESSION[parent::KEY_SESSION_FB_PICTURE]) ? $_SESSION[parent::KEY_SESSION_FB_PICTURE]: '#') . '\','
                      . CIOINA_Util::backquote('fb_verified') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes(1) . '\','
                      . CIOINA_Util::backquote('request_ip_address') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
                      . ' WHERE ' . CIOINA_Util::backquote('id') . '='
                      . '\'' . CIOINA_Util::sqlAddSlashes($records[0]['id']) . '\'';

                      $GLOBALS['CIOINA_dbi']->tryQuery($query);
                  }
              }

              return true;
          }

          private function redirectToWebPageList(array $messages = null)
          {
              $redirectUrl =  FormUtils::getDefaultRedirect();

              FormUtils::setMessages($messages) ;

              if (! defined('TESTSUITE')) {
                  header('Location: ' . $redirectUrl);
                  exit;
              }
          }

          private function sendFacebookLogin()
          {
              $result = $this->decodeJson(parent::FACEBOOK_LOGIN_URL_SETTINGS_ID, '','web-display');
              if($result === false)
              {
                  return;
              }

              $back = isset($_SESSION[parent::KEY_SESSION_LOGIN_URL]);
              $redirect = config('app.url') . '/contact/edit/10';

              if ($this->checkFacebook($redirect))
              {
                  unset($result->data->facebookLoginUrl);

                  if($back)
                  {
                      if($this->LoginAndUpdateClient() === false)
                      {
                          return;
                      }

                      $this->redirectToWebPageList([$result->data->messageBag]);
                  }
                  elseif(isset($_SESSION[parent::KEY_SESSION_FB_PICTURE]))
                  {
                      if($this->LoginAndUpdateClient() === false)
                      {
                          return;
                      }

                      $setting = $this->getSetting(parent::FACEBOOK_LOGIN_URL_SETTINGS_ID);

                      $temp = FormUtils::makeBlocks(
                      [
                            'redirectUrl'        => FormUtils::getDefaultRedirect(),
                            'facebookPictureUrl' => FormUtils::getSimpleImage($_SESSION[parent::KEY_SESSION_FB_PICTURE], 
                                'fb',
                                $result->data->imageTitle
                             ),
                     ], $setting->content);

                      $result->data->facebookPictureUrl = $temp->render();

                  }else{
                      
                      $result->data->facebookPictureUrl = $result->data->messageBag;
                  }

                  $this->sendJson($result);

              }elseif(isset($_SESSION[parent::KEY_SESSION_LOGIN_URL]) || 
                      isset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]) )
              {
                  unset($result->data->facebookPictureUrl);

                  if(isset($_SESSION[parent::KEY_SESSION_LOGIN_URL]))
                  {
                      $result->data->facebookLoginUrl = $_SESSION[parent::KEY_SESSION_LOGIN_URL];
                  }elseif(isset($_SESSION[parent::KEY_SESSION_LOGOUT_URL])){
                      $result->data->facebookLoginUrl = $_SESSION[parent::KEY_SESSION_LOGOUT_URL];
                  }
                  
                  $this->sendJson($result);
              }
          }

          private function sendFacebookLogout()
          {
              $result = $this->decodeJson(parent::FACEBOOK_LOGOUT_URL_SETTINGS_ID, '','web-display');
              if($result === false)
              {
                  return;
              }

              $back = isset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]);
              $redirect = config('app.url') . '/contact/edit/11';

              if ($this->checkFacebook($redirect))
              {
                  unset($result->data->facebookPictureUrl);

                  $save = $_SESSION[parent::KEY_SESSION_FB_PICTURE];
                  unset( $_SESSION[parent::KEY_SESSION_FB_PICTURE]);

                  $this->checkFacebook($redirect);

                  $_SESSION[parent::KEY_SESSION_FB_PICTURE] = $save;

                  if(isset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]))
                  {
                      if($back)
                      {
                          unset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]);
                          unset($_SESSION[parent::KEY_SESSION_FB_TOKEN]);
                          unset($_SESSION[parent::KEY_SESSION_FB_PICTURE]);
                          unset($_SESSION[parent::KEY_SESSION_FB_EMAIL]);

                          $this->redirectToWebPageList([$result->data->messageBag]);
                      }
                      else{
                          $result->data->facebookLoginUrl = $_SESSION[parent::KEY_SESSION_LOGOUT_URL];
                      }
                  }

                  $this->sendJson($result);

              }elseif(isset($_SESSION[parent::KEY_SESSION_LOGIN_URL]) || 
                      isset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]) )
              {
                  unset($result->data->facebookLoginUrl);
                  
                  if($back)
                  {
                      unset($_SESSION[parent::KEY_SESSION_LOGOUT_URL]);
                      unset($_SESSION[parent::KEY_SESSION_FB_TOKEN]);
                      unset($_SESSION[parent::KEY_SESSION_FB_PICTURE]);
                      unset($_SESSION[parent::KEY_SESSION_FB_EMAIL]);

                      if(isset($_SESSION[parent::KEY_SESSION_LOGIN_URL]))
                      {
                          unset($_SESSION[parent::KEY_SESSION_LOGIN_URL]);

                          $this->redirectToWebPageList([$result->data->messageBag]);
                      }
                  }
                  else{
                      $setting = $this->getSetting(parent::FACEBOOK_LOGOUT_URL_SETTINGS_ID);

                      $result->data->facebookPictureUrl = $setting->content;
                  }

                  if ($this->isBackFromFacebook) 
                  {
                      $this->redirectToWebPageList([trans('user-management::errors.facebook_redirect')]);
                  }

                  $this->sendJson($result);
              }
              
          }

          private function sendFacebookEmail()
          {
              if($this->checkFacebookStatus() !== false)
              {
                  
                  list($node, $pictureUrl) = $this->getFacebookData('email,first_name,last_name');

                  if (empty($node))
                  {
                      return false;
                  }

                  $jsonData = $this->getJsonSchema(self::FACEBOOK_EMAIL_SETTINGS_ID);
                  if ($jsonData === false)
                  {
                      return false;
                  }

                  $this->escapeAndNormalize('Subject');
                  $this->escapeAndNormalize('Body');
                  
                  if($this->validateJson($jsonData) === false)
                  {
                      return false;
                  }
                  
                  if($this->saveFacebookEmail(
                      [ 
                           'Subject' => $this->data->Subject,
                           'Body' => $this->data->Body,
                           'email' => $node->getField('email'),
                           'FirstName' => $node->getField('first_name'),
                           'LastName' => $node->getField('last_name'),
                      ]) === false)
                  {
                      $this->sendJsonMessage('total_guest_emails_limit');
                      return false;
                  }

                  if (! \App::environment(['testing'])) 
                  {
                      $mgClient = new Mailgun($GLOBALS['CIOINA_Config']->get('MailgunKey'));
                      $domain = $GLOBALS['CIOINA_Config']->get('MailgunDomain');
                      $result = $mgClient->sendMessage($domain, 
                      [
                      'from'    => 'Cioina website <postmaster@' . $domain . '>',
                      'to'      =>  'Alexei Cioina <' . $GLOBALS['CIOINA_Config']->get('MailgunRecipient') . '>',
                      'subject' => $this->data->Subject,
                      'text'    => ' fb_email = ' . $node->getField('email')
                          . ' fb_first_name = ' . $node->getField('first_name')
                          . ' fb_last_name  = ' . $node->getField('last_name')
                          . ' MESSAGE: '. $this->data->Body,
                      ]);
                  }

                  return true;
              }
          }

          private function sendEmail()
          {
              if($this->checkFacebookStatus(!isset($_SESSION[parent::KEY_SESSION_FB_PICTURE])))
              {
                  list($node, $pictureUrl) = $this->getFacebookData('email');

                  if (empty($node))
                  {
                      return;
                  }

                  $result = $this->decodeJson(self::FACEBOOK_EMAIL_SETTINGS_ID , '');
                  if($result === false)
                  {
                      return;
                  }

                  $this->sendJson($result);
              }
          }

          private function sendSuccessEmailHtml()
          {
              if($this->checkFacebookStatus() !== false)
              {
                  $result = $this->decodeJson(parent::FACEBOOK_LOGIN_URL_SETTINGS_ID, '','web-display');
                  if($result === false)
                  {
                      return;
                  }

                  $setting = $this->getSetting(self::FACEBOOK_EMAIL_SETTINGS_ID);
                  
                  list($node, $pictureUrl) = $this->getFacebookData('email,first_name,last_name');

                  if (empty($node))
                  {
                      return;
                  }

                  $temp = FormUtils::makeBlocks(
                  [
                        'redirectUrl' => FormUtils::getDefaultRedirect(),
                        'first_name' => $node->getField('first_name'),
                        'last_name' => $node->getField('last_name'),
                        'email' => $node->getField('email'),
                        'facebookPictureUrl' => FormUtils::getSimpleImage(
                            (isset($_SESSION[parent::KEY_SESSION_FB_PICTURE]) ? $_SESSION[parent::KEY_SESSION_FB_PICTURE]: '#'), 
                            'fb',
                            $result->data->imageTitle
                         ),
                 ], $setting->content);
                  $result->data->facebookPictureUrl = $temp->render();

                  unset($result->data->facebookLoginUrl);
                  $this->sendJson($result);
              }
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

              $this->escapeAndNormalize('Subject');
              $this->escapeAndNormalize('Body');

              if($this->validateJson($jsonData) === false)
              {
                  return false;
              }

              if($this->saveStepOne() === false)
              {
                  return false;
              }

              $result = $this->decodeJson(self::STEP_ONE_SETTINGS_ID, $this->getStepOne());
              if($result === false)
              {
                  return false;
              }

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

              $this->saveStepTwo();

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

              $this->saveStepThree();

              return true;
          }
           
          private function saveFacebookEmail(array $data = null)
          {
              sleep(1);

              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id'). ')'
              . ' FROM ' . $this->getFullTableName()
              . ' WHERE ' . CIOINA_Util::backquote('created_at'). '<= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d H:i:s')) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
              . ' AND ' . CIOINA_Util::backquote('created_at'). '>= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d')) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d') .  '\')'
              . ' AND ' . CIOINA_Util::backquote('is_facebook') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(1) . '\'';
              

              $today_ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

              if ($today_ids >= $GLOBALS['CIOINA_Config']->get('TotalFacebookEmailsPerDay'))
              {
                  $this->sendJsonMessage('total_guest_emails_limit');
                  return false;
              }

              $now = gmdate('Y-m-d H:i:s');
              $query = 'INSERT INTO ' . $this->getFullTableName()
              . ' (created_at, updated_at, email_subject, email_body, email, first_name, last_name, has_facebook, is_facebook, request_ip_address) '
              . ' VALUES('
              . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes($data['Subject']) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes($data['Body']) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes($data['email']) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes($data['FirstName']) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes($data['LastName']) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes(1) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes(1) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\')';

              $GLOBALS['CIOINA_dbi']->tryQuery($query);

              return true;
          }

          private function saveStepOne()
          {
              sleep(1);

              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ')'
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $today_ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

              if ($today_ids == 1){
                  $query = 'UPDATE ' . $this->getFullTableName()
              . ' SET ' 
              . CIOINA_Util::backquote('has_facebook'). '='
              . ($this->data->member?'\''. CIOINA_Util::sqlAddSlashes(1) . '\',':'\''. CIOINA_Util::sqlAddSlashes(0) . '\',')
              . CIOINA_Util::backquote('email_subject'). '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->data->Subject) . '\','
              . CIOINA_Util::backquote('email_body'). '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->data->Body) . '\','
              . CIOINA_Util::backquote('email'). '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->data->email) . '\','
              . CIOINA_Util::backquote('first_name'). '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->data->FirstName) . '\','
              . CIOINA_Util::backquote('last_name'). '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->data->LastName) . '\''
              . $this->getWhere();

                  $GLOBALS['CIOINA_dbi']->tryQuery($query);

              }else{

                  $query = 'SELECT COUNT('. CIOINA_Util::backquote('id'). ')'
                  . ' FROM ' . $this->getFullTableName()
                  . ' WHERE ' . CIOINA_Util::backquote('created_at'). '<= STR_TO_DATE('
                  . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d H:i:s')) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
                  . ' AND ' . CIOINA_Util::backquote('created_at'). '>= STR_TO_DATE('
                  . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d')) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d') .  '\')';

                  $today_ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

                  if ($today_ids >= $GLOBALS['CIOINA_Config']->get('TotalGuestEmailsPerDay')){
                      $this->sendJsonMessage('total_guest_emails_limit');
                      return false;
                  }

                  $now = gmdate('Y-m-d H:i:s');
                  $query = 'INSERT INTO ' . $this->getFullTableName()
                  . ' (created_at, updated_at, request_id,  email_subject, email_body, email, first_name, last_name, has_facebook, request_ip_address) '
                  . ' VALUES('
                  . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->requestId) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->data->Subject) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->data->Body) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->data->email) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->data->FirstName) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->data->LastName) . '\','
                  . ($this->data->member?'\''. CIOINA_Util::sqlAddSlashes(1) . '\',':'\''. CIOINA_Util::sqlAddSlashes(0) . '\',')
                  . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\')';

                  $GLOBALS['CIOINA_dbi']->tryQuery($query);
              }
              
              return true;
          }

          private function saveStepTwo()
          {
              $query = 'UPDATE ' . $this->getFullTableName()
                . ' SET ' . CIOINA_Util::backquote('phone'). '='
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
              $query = 'UPDATE ' . $this->getFullTableName()
                . ' SET ' 
                . CIOINA_Util::backquote('age'). '='
                . ($this->data->age==null?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->age) . '\',')
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

          private function saveStepFour()
          {
              $query = 'UPDATE ' . $this->getFullTableName()
                . ' SET ' 
                . CIOINA_Util::backquote('request_id'). '= NULL'
                . $this->getWhere();

              $GLOBALS['CIOINA_dbi']->tryQuery($query);
          }
          
          private function getStepOne()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('first_name') .','
              . CIOINA_Util::backquote('last_name') .','
              . CIOINA_Util::backquote('email') .','
              . CIOINA_Util::backquote('email_subject') .','
              . CIOINA_Util::backquote('has_facebook') .','
              . CIOINA_Util::backquote('email_body')
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d =  CIOINA_Util::deleteLastComma(
                       CIOINA_Util::formatJsonString($records[0]['first_name'], 'FirstName')
                     . CIOINA_Util::formatJsonString($records[0]['last_name'], 'LastName')
                     . CIOINA_Util::formatJsonString($records[0]['email'], 'email')
                     . CIOINA_Util::formatJsonString($records[0]['email_subject'], 'Subject', $this->escapeBackslash($records[0]['email_subject']))
                     . CIOINA_Util::formatJsonString($records[0]['email_body'], 'Body', $this->escapeBackslash($records[0]['email_body']))
                     . CIOINA_Util::formatJsonBoolean($records[0]['has_facebook'], 'member', 'false', 'true', false)
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
              . CIOINA_Util::backquote('age') .','
              . CIOINA_Util::backquote('birthday') .','
              . CIOINA_Util::backquote('gender') .','
              . CIOINA_Util::backquote('country_code') 
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d = CIOINA_Util::deleteLastComma(
                      CIOINA_Util::formatJsonString($records[0]['country_code'], 'country')
                    . CIOINA_Util::formatJsonString($records[0]['age'], 'age')
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
              . CIOINA_Util::backquote('email') .','
              . CIOINA_Util::backquote('email_subject') .','
              . CIOINA_Util::backquote('email_body') .','

              . CIOINA_Util::backquote('phone') .','
              . CIOINA_Util::backquote('zip') .','
              . CIOINA_Util::backquote('state_code') .','

              . CIOINA_Util::backquote('age') .','
              . CIOINA_Util::backquote('birthday') .','
              . CIOINA_Util::backquote('gender') .','
              . CIOINA_Util::backquote('has_facebook') .','
              . CIOINA_Util::backquote('country_code') 
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d = CIOINA_Util::deleteLastComma(
                      CIOINA_Util::formatJsonString($records[0]['first_name'], 'FirstName')
                    . CIOINA_Util::formatJsonString($records[0]['last_name'], 'LastName')
                    . CIOINA_Util::formatJsonString($records[0]['email'], 'email')
                    . CIOINA_Util::formatJsonString($records[0]['email_subject'], 'Subject', $this->escapeBackslash($records[0]['email_subject']))
                    . CIOINA_Util::formatJsonString($records[0]['email_body'], 'Body', $this->escapeBackslash($records[0]['email_body']))
                    . CIOINA_Util::formatJsonString($records[0]['phone'], 'phone', CIOINA_Util::getFormatedPhone($records[0]['phone']))
                    . CIOINA_Util::formatJsonString($records[0]['zip'], 'zip', CIOINA_Util::getFormatedZip($records[0]['zip']))
                    . CIOINA_Util::formatJsonString($records[0]['state_code'], 'state')
                    . CIOINA_Util::formatJsonString($records[0]['country_code'], 'country')
                    . CIOINA_Util::formatJsonString($records[0]['age'], 'age')
                    . CIOINA_Util::formatJsonString($records[0]['birthday'], 'birthday', CIOINA_Util::getFormatedDate($records[0]['birthday']))
                    . CIOINA_Util::formatJsonString($records[0]['gender'], 'Gender')
                    . CIOINA_Util::formatJsonBoolean($records[0]['has_facebook'], 'member', 'No', 'Yes', false)
                    );

              return $d;
          }

      }