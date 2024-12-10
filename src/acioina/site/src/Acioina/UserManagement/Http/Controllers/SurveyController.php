<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Http\Controllers\Base\ModelBaseJsonController;

      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use Distilleries\Expendable\Models\Survey;
      use Distilleries\Expendable\Helpers\FormUtils;

      use Illuminate\Http\Request;

      use \CIOINA_Util;

      use \Facebook\Facebook;
      use \Facebook\Exceptions\FacebookSDKException;
      use \Facebook\Exceptions\FacebookResponseException;
      use \Facebook\Authentication\AccessToken;

      class SurveyController extends ModelBaseJsonController implements FormStateContract
      {
          const STEP_ONE_SETTINGS_ID = 46;
          const STEP_TWO_SETTINGS_ID = 47;
          const STEP_THREE_SETTINGS_ID = 48;
          const STEP_FOUR_SETTINGS_ID = 49;

          private $onlineClient = false;

          public function __construct(Survey $model)
          {
              parent::__construct($model);
          }

          public function getEdit($id = 0)
          {
              if (! $this->createJsonFromInput($id))
              {
                  return;
              }

              $this->onlineClient = FormUtils::getClientLogin();

              if($this->onlineClient === false)
              {
                  $this->sendErrorJsonWithParameter('no_session_keys', 2);
                  return;
              }

              if( $id == 1 )
              {
                  $this->sendStepOne();
              }elseif( $id == 2 )
              {
                  $this->sendStepTwo();
              }elseif( $id == 3 )
              {
                  $this->sendStepThree();
              }elseif ( $id == 4 )
              {
                  $this->sendStepFour();
              }elseif ( $id == 5 )
              {
                  $this->sendStepFive();
              }elseif ( $id == 10 )
              {
                  $this->sendPhpInfo();
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

              $this->onlineClient = FormUtils::getClientLogin();

              if($this->onlineClient === false)
              {
                  $this->sendErrorJsonWithParameter('no_session_keys', 2);
                  return;
              }

              if( $this->currentStep == 0
                  || $this->currentStep == 10)
              {
                  //Do nothing
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
              }else
              {
                  $this->sendJsonMessage('wrong_id_parameter');
                  return;
              }

              $this->sendJsonMessage('OK', true);
          }

          private function getCount()
          {
              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ')'
                . ' FROM ' . $this->getFullTableName()
                . $this->getWhere();

              return $GLOBALS['CIOINA_dbi']->fetchValue($query);
          }

          private function CheckRecordForUpdate()
          {
              if( $this->getCount() != 1 )
              {
                  $this->sendJsonMessage('fetch_result_count');
                  return false;
              }

              return true;
          }

          protected function getWhere()
          {
              return
                ' WHERE ' . CIOINA_Util::backquote('client_id') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes($this->onlineClient[0]['id']) . '\'';
          }

          private function sendStepOne()
          {
              $dataJson = $this->getCount() != 1 ? '"complexName":"N/A","isComplexName":"false"' : $this->getStepOne();
              $result = $this->decodeJson(self::STEP_ONE_SETTINGS_ID, $dataJson);
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

              if ( strpos($dataJson, '"isComplexName":"false"') !== false)
              {
                  $result->options->fields->complexName->disabled = true;
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

              $this->sendJson($result);
          }

          private function sendStepFour()
          {
              $result = $this->decodeJson(self::STEP_FOUR_SETTINGS_ID, $this->getAll(), 'web-display');
              if($result === false)
              {
                  return;
              }

              $this->sendJson($result);
          }

          private function sendStepFive()
          {
              FormUtils::forgetSessionMapster();

              $this->sendJsonMessage('OK', true);
          }

          private function sendPhpInfo()
          {
              if($this->onlineClient === false)
              {
                  $this->sendErrorJsonWithParameter('no_session_keys', 2);
                  return;
              }

              if($this->onlineClient[0]['id'] != 1)
              {
                  $this->sendErrorJsonWithParameter('no_session_keys', 3);
                  return;
              }

              ob_start();
              phpinfo();
              ob_end_flush();
              exit;
          }

          private function validateStepOne()
          {
              $jsonData = $this->getJsonSchema(self::STEP_ONE_SETTINGS_ID);
              if($jsonData === false)
              {
                  return false;
              }

              $jsonData['schema']->schema->properties->FullName->pattern = parent::PERSON_PATTERN;
              $jsonData['schema']->schema->properties->birthday->pattern = parent::DATE_PATTERN;

              $this->escapeAndNormalize('complexName');

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

              $this->escapeAndNormalize('recentVacation');

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

          private function saveStepOne()
          {
              sleep(1);

              $now = (new \DateTime())->format('Y-m-d H:i:s');
              if ($this->getCount() == 1)
              {
                  $query = 'UPDATE ' . $this->getFullTableName()
                   . ' SET ' 
                   . CIOINA_Util::backquote('updated_at'). '='
                   . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                   . CIOINA_Util::backquote('ip_address') . '='
                   . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\','
                   . CIOINA_Util::backquote('session_id') . '='
                   . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\','
                   . CIOINA_Util::backquote('person_name'). '='
                   . '\'' . CIOINA_Util::sqlAddSlashes($this->data->FullName) . '\','
                   . CIOINA_Util::backquote('is_complex_name'). '='
                   . ($this->data->isComplexName?'\''. CIOINA_Util::sqlAddSlashes(1) . '\',':'\''. CIOINA_Util::sqlAddSlashes(0) . '\',')
                   . CIOINA_Util::backquote('complex_name'). '='
                   . (!$this->data->isComplexName?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->complexName) . '\',')
                   . CIOINA_Util::backquote('email'). '='
                   . (!isset($this->data->email)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->email) . '\',')
                   . CIOINA_Util::backquote('birthday'). '='
                   . (!isset($this->data->birthday)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes(vsprintf('%3$04d-%1$02d-%2$02d',
                    sscanf($this->data->birthday,'%02d%02d%04d'))) . '\',')
                   . CIOINA_Util::backquote('gender'). '='
                   . (!isset($this->data->Gender)?'NULL':'\''. CIOINA_Util::sqlAddSlashes($this->data->Gender) . '\'')

                   . $this->getWhere();

                  $GLOBALS['CIOINA_dbi']->tryQuery($query);

              }else{
                  $query = 'INSERT INTO ' . $this->getFullTableName()
                  . ' (created_at, updated_at, client_id, person_name, is_complex_name, complex_name, email, birthday, gender, session_id, ip_address) '
                  . ' VALUES('
                  . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->onlineClient[0]['id']) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes($this->data->FullName) . '\','
                  . ($this->data->isComplexName?'\''. CIOINA_Util::sqlAddSlashes(1) . '\',':'\''. CIOINA_Util::sqlAddSlashes(0) . '\',')
                  . (!$this->data->isComplexName?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->complexName) . '\',')
                  . (!isset($this->data->email)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->email) . '\',')
                  . (!isset($this->data->birthday)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes(vsprintf('%3$04d-%1$02d-%2$02d',
                    sscanf($this->data->birthday,'%02d%02d%04d'))) . '\',')
                  . (!isset($this->data->Gender)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->Gender) . '\',')
                  . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\')';

                  $GLOBALS['CIOINA_dbi']->tryQuery($query);
              }
          }

          private function saveStepTwo()
          {
              $now = (new \DateTime())->format('Y-m-d H:i:s');
              $query = 'UPDATE ' . $this->getFullTableName()
                . ' SET '
                . CIOINA_Util::backquote('updated_at'). '='
                . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                . CIOINA_Util::backquote('ip_address') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\','
                . CIOINA_Util::backquote('session_id') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\','
                . CIOINA_Util::backquote('phone'). '='
                . (!isset($this->data->phone)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->phone) . '\',')
                . CIOINA_Util::backquote('zip'). '='
                . (!isset($this->data->zip)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->zip) . '\',')
                . CIOINA_Util::backquote('state_code'). '='
                . (!isset($this->data->state)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->state) . '\',')
                . CIOINA_Util::backquote('country_code'). '='
                . (!isset($this->data->country)?'NULL':'\''. CIOINA_Util::sqlAddSlashes($this->data->country) . '\'')
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
                . CIOINA_Util::backquote('ip_address') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\','
                . CIOINA_Util::backquote('session_id') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\','
                . CIOINA_Util::backquote('recent_vacation'). '='
                . (!isset($this->data->recentVacation)?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($this->data->recentVacation) . '\',')
                . CIOINA_Util::backquote('first_drive_age'). '='
                . (!isset($this->data->firstDriveAge)?'NULL':'\''. CIOINA_Util::sqlAddSlashes($this->data->firstDriveAge) . '\'')
                . $this->getWhere();

              $GLOBALS['CIOINA_dbi']->tryQuery($query);
          }

          private function getStepOne()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('person_name') .','
              . CIOINA_Util::backquote('is_complex_name') .','
              . CIOINA_Util::backquote('complex_name') .','
              . CIOINA_Util::backquote('email') .',' 
              . CIOINA_Util::backquote('birthday') .',' 
              . CIOINA_Util::backquote('gender') 
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d =  CIOINA_Util::deleteLastComma(
                       CIOINA_Util::formatJsonString($records[0]['person_name'], 'FullName')
                     . CIOINA_Util::formatJsonBoolean($records[0]['is_complex_name'], 'complexName', 'N/A', $this->escapeBackslash($records[0]['complex_name']))
                     . CIOINA_Util::formatJsonString($records[0]['email'], 'email')
                     . CIOINA_Util::formatJsonString($records[0]['birthday'], 'birthday', CIOINA_Util::getPlaneDate($records[0]['birthday']))
                     . CIOINA_Util::formatJsonString($records[0]['gender'], 'Gender')
                     . CIOINA_Util::formatJsonBoolean($records[0]['is_complex_name'], 'isComplexName', 'false', 'true', false)
               );

              return $d;
          }

          private function getStepTwo()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('country_code') .','
              . CIOINA_Util::backquote('phone') .','
              . CIOINA_Util::backquote('zip') .','
              . CIOINA_Util::backquote('state_code') 
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d =  CIOINA_Util::deleteLastComma(
                       CIOINA_Util::formatJsonString($records[0]['country_code'], 'country')
                     . CIOINA_Util::formatJsonString($records[0]['phone'], 'phone')
                     . CIOINA_Util::formatJsonString($records[0]['zip'], 'zip')
                     . CIOINA_Util::formatJsonString($records[0]['state_code'], 'state', null, false));

              return $d;
          }

          private function getStepThree()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('recent_vacation') .','
              . CIOINA_Util::backquote('first_drive_age')
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d =  CIOINA_Util::deleteLastComma(
                      CIOINA_Util::formatJsonString($records[0]['recent_vacation'], 'recentVacation', $this->escapeBackslash($records[0]['recent_vacation']))
                    . CIOINA_Util::formatJsonString($records[0]['first_drive_age'], 'firstDriveAge', null, false)
                    );

              return $d;
          }

          private function getAll()
          {
              $query = 'SELECT '
              . CIOINA_Util::backquote('person_name') .','
              . CIOINA_Util::backquote('is_complex_name') .','
              . CIOINA_Util::backquote('complex_name') .','
              . CIOINA_Util::backquote('email') .','
              . CIOINA_Util::backquote('birthday') .','
              . CIOINA_Util::backquote('gender') .','

              . CIOINA_Util::backquote('country_code') .','
              . CIOINA_Util::backquote('phone') .','
              . CIOINA_Util::backquote('zip') .','
              . CIOINA_Util::backquote('state_code') .','

              . CIOINA_Util::backquote('recent_vacation') .','
              . CIOINA_Util::backquote('first_drive_age') 
              . ' FROM ' . $this->getFullTableName()
              . $this->getWhere();

              $records = $this->fetchResult($query);

              $d = CIOINA_Util::deleteLastComma(
                      CIOINA_Util::formatJsonString($records[0]['person_name'], 'FullName')
                    . CIOINA_Util::formatJsonBoolean($records[0]['is_complex_name'], 'isComplexName', 'No', 'Yes')
                    . CIOINA_Util::formatJsonString($records[0]['is_complex_name'], 'complexName', $this->escapeBackslash($records[0]['complex_name']))
                    . CIOINA_Util::formatJsonString($records[0]['email'], 'email')
                    . CIOINA_Util::formatJsonString($records[0]['birthday'], 'birthday', CIOINA_Util::getFormatedDate($records[0]['birthday']))
                    . CIOINA_Util::formatJsonString($records[0]['gender'], 'Gender')
                    . CIOINA_Util::formatJsonString($records[0]['country_code'], 'country')
                    . CIOINA_Util::formatJsonString($records[0]['phone'], 'phone', CIOINA_Util::getFormatedPhone($records[0]['phone']))
                    . CIOINA_Util::formatJsonString($records[0]['zip'], 'zip', CIOINA_Util::getFormatedZip($records[0]['zip']))
                    . CIOINA_Util::formatJsonString($records[0]['state_code'], 'state')
                    . CIOINA_Util::formatJsonString($records[0]['recent_vacation'], 'recentVacation', $this->escapeBackslash($records[0]['recent_vacation']))
                    . CIOINA_Util::formatJsonString($records[0]['first_drive_age'], 'firstDriveAge', null, false)
               );

              return $d;
          }

      }