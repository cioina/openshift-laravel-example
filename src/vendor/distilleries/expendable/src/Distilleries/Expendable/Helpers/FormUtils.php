<?php namespace Distilleries\Expendable\Helpers;

      use Wpb\StringBladeCompiler\StringView;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\Expendable\Models\PostTopic;
      use Distilleries\Expendable\Models\Post;
      use Distilleries\Expendable\Models\Setting;
      use Distilleries\Expendable\Models\Topic;
      use Illuminate\Support\Collection;
      use Illuminate\Support\Str;
      use \CIOINA_Util;
      
      class FormUtils 
      {
          const BLADE_START_MARK = '{!!$setting_';
          const BLADE_END_MARK = '!!}';

          const ID_SETTING_ROBOT = 1;
          const ID_SETTING_MAPSTER = 2;
          const ID_SETTING_LOGIN_FORM = 41;
          const ID_SETTING_YOUTUBE_PLAYER = 52;
          const ID_SETTING_FACEBOOK_API = 55;

          const KEY_SESSION_IMAGE_MAP_KEYS = 'imagemap_keys';
          const KEY_SESSION_IMAGE_MAP_ID = 'imagemap_id';
          const KEY_SESSION_ONLINE_ID  = 'online_id';
          const KEY_SESSION_MESSAGE_BAG  = 'message_bag';
          const KEY_SESSION_USE_COOKIES = 'use_cookies';
          const KEY_SESSION_WEBPAGE_HAS_FORMS  = 'webpage_has_forms';
          const KEY_SESSION_REDIRECT  = 'redirect_url';

          // These are "global" keys
          const KEY_SESSION_FB_PICTURE = 'fb_picture';

          const DEFAULT_REDIRECT_ACTION = '\\Acioina\\UserManagement\\Http\Controllers\\WebPageController@getIndex';

          const LOGIN_SLUG = 'login';

          /**
           * @codeCoverageIgnore
           */ 
          public static function setOnlineClient($onlineId = null, $clientId = null, $checkTotalOnlineClientLogins = true)
          {
              if($checkTotalOnlineClientLogins)
              {
                  $sessionTime = -1 *($GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes') + 2);

                  $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') FROM ' 
                  . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                  . '.' . CIOINA_Util::backquote('online_clients')
                  . ' WHERE ' . CIOINA_Util::backquote('client_id') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes($clientId) . '\''
                  . ' AND ' . CIOINA_Util::backquote('is_logged_out') . '= 0'
                  . ' AND ' . CIOINA_Util::backquote('updated_at') . '<= STR_TO_DATE('
                  . '\'' . CIOINA_Util::sqlAddSlashes(self::getDateIntervalTime(0, -1)->format('Y-m-d H:i:s')) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
                  . ' AND ' . CIOINA_Util::backquote('updated_at') . '>= STR_TO_DATE('
                  . '\'' . CIOINA_Util::sqlAddSlashes(self::getDateIntervalTime(0, $sessionTime)->format('Y-m-d H:i:s')) . '\','
                  . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';

                  $count = $GLOBALS['CIOINA_dbi']->fetchValue($query);

                  if ($count >= $GLOBALS['CIOINA_Config']->get('TotalOnlineClientLogins') )
                  {
                      return true;
                  }
              }

              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') AS count, MAX('
              . CIOINA_Util::backquote('id') . ') AS max_id FROM ' 
              . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
              . '.' . CIOINA_Util::backquote('online_clients')
              . ' WHERE ' . CIOINA_Util::backquote('client_id') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes($clientId) . '\''
              . ($checkTotalOnlineClientLogins ? ' AND ' . CIOINA_Util::backquote('is_logged_out') . '= 1' : '')
              . ' AND ' . CIOINA_Util::backquote('ip_address') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
              . ($checkTotalOnlineClientLogins ?' AND ' . CIOINA_Util::backquote('session_id') . '=' . '\'' . CIOINA_Util::sqlAddSlashes(session_id()). '\'' :'') ;

              $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

              $now = (new \DateTime())->format('Y-m-d H:i:s');
              
              if (isset($records) && count($records) === 1 && $records[0]['count'] > 0)
              {
                  $query = 'UPDATE ' 
                . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
                . '.' . CIOINA_Util::backquote('online_clients')
                . ' SET ' 
                . CIOINA_Util::backquote('online_id') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes($onlineId) . '\','
                . CIOINA_Util::backquote('session_id') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\','
                 . CIOINA_Util::backquote('updated_at') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                . CIOINA_Util::backquote('is_logged_out') . '= 0'
                . ' WHERE ' . CIOINA_Util::backquote('id') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes($records[0]['max_id']) . '\'';
              }else{
                  $query = 'INSERT INTO ' 
                . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
                . '.' . CIOINA_Util::backquote('online_clients')
                . ' ( online_id, client_id, updated_at, created_at, session_id, ip_address) '
                . ' VALUES('
                . '\'' . CIOINA_Util::sqlAddSlashes($onlineId) . '\','
                . '\'' . CIOINA_Util::sqlAddSlashes($clientId) . '\','
                . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\','
                . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\')';
              }

              $GLOBALS['CIOINA_dbi']->tryQuery($query);

              return false;
          }

          /**
           * @codeCoverageIgnore
           */ 
          public static function getClientLogin()
          {
              if ( ! isset(  $_SESSION[self::KEY_SESSION_ONLINE_ID]) ) 
              {
                  return false;
              }

              $sessionTime = -1 * $GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes');

              $now = self::getDateIntervalTime(0, $sessionTime)->format('Y-m-d H:i:s');

              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') AS count, MAX('
              . CIOINA_Util::backquote('request_date') . ') AS max_date FROM ' 
              . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
              . '.' . CIOINA_Util::backquote('web_statistics')
              . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
              . ' AND ' . CIOINA_Util::backquote('request_session') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\''
              . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes(self::getDateIntervalTime(0, -1)->format('Y-m-d H:i:s')) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
              . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';

              $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

              if (isset($records) && count($records) === 1 && $records[0]['count'] > 0)
              {
                  $query = ' UPDATE ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                  . '.' . CIOINA_Util::backquote('online_clients')
                  . ' SET ' . CIOINA_Util::backquote('updated_at') . '= '
                  . '\'' . CIOINA_Util::sqlAddSlashes($records[0]['max_date']) . '\''
                  . ' WHERE '. CIOINA_Util::backquote('ip_address') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
                  . ' AND ' . CIOINA_Util::backquote('session_id') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\''
                  . ' AND ' . CIOINA_Util::backquote('online_id') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes($_SESSION[self::KEY_SESSION_ONLINE_ID]) . '\'';

                  $GLOBALS['CIOINA_dbi']->tryQuery($query);
              }

              $query = 'SELECT '
              . CIOINA_Util::backquote('T2') . '.' . CIOINA_Util::backquote('onlineId') .','
              . CIOINA_Util::backquote('T2') . '.' . CIOINA_Util::backquote('onlineRowId') .','
              . CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('id') .','
              . CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('request_ip_address') .','
              . CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('fb_verified') .','
              . CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('fb_email') .','
              . CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('fb_first_name') .','
              . CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('fb_last_name') .','
              . CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('fb_picture')
              . ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
              . '.' . CIOINA_Util::backquote('clients')
              . ' AS T3 ' 
              . 'INNER JOIN ( ' 
              .  ' SELECT' 
              . CIOINA_Util::backquote('T1') . '.' .  CIOINA_Util::backquote('id') . ' AS onlineRowId, '
              . CIOINA_Util::backquote('T1') . '.' .  CIOINA_Util::backquote('client_id') . ' AS clientId, '
              . CIOINA_Util::backquote('T1') . '.' .  CIOINA_Util::backquote('online_id') . ' AS onlineId '
              . ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
              . '.' . CIOINA_Util::backquote('online_clients')
              . ' AS T1' 
              . ' WHERE ' . CIOINA_Util::backquote('T1') .'.'. CIOINA_Util::backquote('updated_at') . '<= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes((new \DateTime())->format('Y-m-d H:i:s')) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
              . ' AND ' . CIOINA_Util::backquote('T1') .'.'. CIOINA_Util::backquote('updated_at') . '>= STR_TO_DATE('
              . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
              . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
              //. ' AND ' . CIOINA_Util::backquote('T1') .'.'. CIOINA_Util::backquote('is_logged_out') . '='
              //. '\'' . CIOINA_Util::sqlAddSlashes(0)  . '\'' 
              . ' AND ' . CIOINA_Util::backquote('T1') .'.'. CIOINA_Util::backquote('ip_address') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
              . ' AND ' . CIOINA_Util::backquote('T1') .'.'. CIOINA_Util::backquote('session_id') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\''
              . ' AND ' . CIOINA_Util::backquote('T1') .'.'. CIOINA_Util::backquote('online_id') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes($_SESSION[self::KEY_SESSION_ONLINE_ID]) . '\''
               . ' ) AS T2'
              . ' ON '.  CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('id') . '=' 
              .  CIOINA_Util::backquote('T2') .'.' . CIOINA_Util::backquote('clientId')
              . ' AND ' . CIOINA_Util::backquote('T3') .'.'. CIOINA_Util::backquote('is_deleted') . '='
              . '\'' . CIOINA_Util::sqlAddSlashes(0)  . '\''; 

              $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

              if (isset($records) && count($records) !== 1){
                  unset($_SESSION[self::KEY_SESSION_ONLINE_ID]);

                  return false;
              }

              return $records;
          }

          public static function getFacebookSettings()
          {
              //check if the database is empty
              $setting = Setting::withoutTranslation()->find(3);

              if(! isset($setting))
              {
                  return false;
              }

              try {
                  $setting = Setting::withoutTranslation()->findOrFail(self::ID_SETTING_FACEBOOK_API);
                  $fields = json_decode('{' . $setting->code_block . '}', true);

                  if (isset($fields) && isset($fields['data']) && count($fields['data']) > 0)
                  {
                      $data = json_decode('{' . $setting->code_block . '}');
                      foreach($fields['schema']['properties'] as $key => $value)
                      {
                          $result = Jsv4::isValidMember($data->data, $data, $key);                               
                          if( isset($result) && $result === false)
                          {
                              return false;
                          }
                      }
                      return $data;
                      
                  }else{
                      return false;
                  }
              }
              catch (Exception $e) {
                  return false;
              }
              
          }

          public static function getClientDisplayName()
          {
              $client = self::getClientLogin();
              if($client !==false)
              {
                  return $client[0]['fb_first_name'];
              }
              return null;
          }


          public static function hasCookies()
          {
              return isset($_SESSION[self::KEY_SESSION_USE_COOKIES]) ? true : false;
          }

          public static function forgetCookies()
          {
              unset($_SESSION[self::KEY_SESSION_USE_COOKIES]);
          }

          public static function setRedirect($url = '#')
          {
              $_SESSION[self::KEY_SESSION_REDIRECT] = $url;
          }

          public static function getRedirect($url = null)
          {
              return isset($_SESSION[self::KEY_SESSION_REDIRECT]) ? $_SESSION[self::KEY_SESSION_REDIRECT] : $url;
          }

          public static function forgetRedirect()
          {
              unset($_SESSION[self::KEY_SESSION_REDIRECT]);
          }

          public static function tryHttps($url)
          {
              if ($GLOBALS['CIOINA_Config']->get('ForceSSL') && !Str::startsWith($url, 'https://') && Str::startsWith($url, 'http://')) 
              {
                  return Str::replaceFirst('http://', 'https://', $url);
              }
              return $url;
          }

          public static function hasForms()
          {
              return  isset($_SESSION)
                  && isset($_SESSION[self::KEY_SESSION_WEBPAGE_HAS_FORMS])  
                  && !empty($_SESSION[self::KEY_SESSION_WEBPAGE_HAS_FORMS])
                  && $_SESSION[self::KEY_SESSION_WEBPAGE_HAS_FORMS] === true ? true : false;
          }

          public static function forgetForms()
          {
              if (isset($_SESSION))
              {
                  unset($_SESSION[self::KEY_SESSION_WEBPAGE_HAS_FORMS]);
              }
          }

          public static function hasMessages()
          {
              $messages = self::getMessages();
              return isset($messages) && is_array($messages) && count($messages) > 0 ? true : false;
          }

          public static function getMessages()
          {
              return (isset($_SESSION) && isset($_SESSION[self::KEY_SESSION_MESSAGE_BAG])) ? $_SESSION[self::KEY_SESSION_MESSAGE_BAG] : null;
          }

          public static function setMessages( array $messages = null )
          {
              if (isset($messages) && is_array($messages) && count($messages) > 0)
              {
                  $_SESSION[self::KEY_SESSION_MESSAGE_BAG] = $messages;
              }
          }

          public static function forgetMessages()
          {
              if(isset($_SESSION))
              {
                  unset($_SESSION[self::KEY_SESSION_MESSAGE_BAG]);
              }
          }

          public static function getDefaultRedirect()
          {
              return self::tryHttps(action(self::DEFAULT_REDIRECT_ACTION));
          }

          public static function getSessionOnlineId()
          {
              return isset($_SESSION[self::KEY_SESSION_ONLINE_ID]) ? $_SESSION[self::KEY_SESSION_ONLINE_ID] : null;
          }

          public static function setSessionOnlineId($value)
          {
              $session_id = session_id();

              if (!empty($session_id) || defined('TESTSUITE'))
              {
                  $_SESSION[self::KEY_SESSION_ONLINE_ID] = $value;
              }
          }

          public static function forgetSessionOnlineId()
          {
              unset($_SESSION[self::KEY_SESSION_ONLINE_ID]);
              unset($_SESSION[self::KEY_SESSION_FB_PICTURE]);
          }

          public static function getSessionImageMapKeys()
          {
              return isset($_SESSION[self::KEY_SESSION_IMAGE_MAP_KEYS]) ? $_SESSION[self::KEY_SESSION_IMAGE_MAP_KEYS] : null;
          }

          public static function getSessionImageMapId()
          {
              return isset($_SESSION[self::KEY_SESSION_IMAGE_MAP_ID]) ? $_SESSION[self::KEY_SESSION_IMAGE_MAP_ID] : null;
          }

          public static function forgetSessionMapster()
          {
              unset($_SESSION[self::KEY_SESSION_IMAGE_MAP_KEYS]);
              unset($_SESSION[self::KEY_SESSION_IMAGE_MAP_ID]);
          }

          public static function setSessionMapster($useMapster = true)
          {
              $keyAreas = 
                    [
                    'AL',
                    'AK',
                    'AZ',
                    'AR',
                    'CA',
                    'CO',
                    'FL',
                    'GA',
                    'ID',
                    'IL',
                    'IN',
                    'IA',
                    'KS',
                    'KY',
                    'LA',
                    'ME',
                    'MI',
                    'MN',
                    'MS',
                    'MO',
                    'MT',
                    'NE',
                    'NV',
                    'NM',
                    'NY',
                    'NC',
                    'ND',
                    'OH',
                    'OK',
                    'OR',
                    'PA',
                    'SC',
                    'SD',
                    'TN',
                    'TX',
                    'UT',
                    'VA',
                    'WA',
                    'WV',
                    'WI',
                    'WY'
                    ]; 
              $len  = sizeof($keyAreas) - 1;  
              $shift = mt_rand(-$len, $len);
              $count = mt_rand(3, 5);
              $arr = array(0);
              $arr[0]= mt_rand(0, $len);
              $i=1;
              while($i < $count){
                  $rand = mt_rand(0, $len);
                  $ex = false;
                  foreach ( $arr as $key => $value )
                  {
                      if($arr[ $key ] == $rand){
                          $ex = true;
                          break;
                      }
                  }
                  if(!$ex){
                      $arr[]= $rand;
                      $i++;
                  }
              }
              $str_arr = array();
              foreach ( $arr as $key => $value )
              {
                  $str_arr[] = $keyAreas[ $arr[ $key ]]; 
                  $arr[ $key ] += $shift;
              }

              $result = "var g = [{}], i = [], j = 0, h = $shift;";
              foreach ( $arr as $key => $value )
              {
                  $result.= 'i[' . $key . '] = h + ' .  $arr[ $key ] . ';';
              }

              $_SESSION[self::KEY_SESSION_IMAGE_MAP_KEYS] = implode(',', $str_arr);
              $_SESSION[self::KEY_SESSION_IMAGE_MAP_ID] = CIOINA_Util::getGUID();

              $_SESSION[self::KEY_SESSION_WEBPAGE_HAS_FORMS] = true;

              return $useMapster ? $result : $_SESSION[self::KEY_SESSION_IMAGE_MAP_KEYS];
          }

          public static function diffForHumans($ago)
          {
              //return Carbon::createFromTimeStamp(strtotime($ago))->diffForHumans();

              return $ago->diffForHumans();
              
              //$now = new \DateTime();
              //$diff = $now->diff($ago);

              //$diff->w = floor($diff->d / 7);
              //$diff->d -= $diff->w * 7;

              //$string = array(
              //     'y' => 'year',
              //     'm' => 'month',
              //     'w' => 'week',
              //     'd' => 'day',
              //     'h' => 'hour',
              //     'i' => 'minute',
              //     's' => 'second',
              // );
              //foreach ($string as $k => &$v) 
              //{
              //    if ($diff->$k) 
              //    {
              //        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
              //    } else {
              //        unset($string[$k]);
              //    }
              //}

              //$string = array_slice($string, 0, 1);
              //return $string ? implode(', ', $string) . ' ago' : 'just now';
          }

          public static function getTitle($title = null, $created = '', $updated = '',  $datesMessage = '')
          {
              if (isset($title))
              {
                  $format = 'F j, Y';
                  $datesMessage = !empty($datesMessage) ? '<h5 style="text-align: center;">' 
                  . trans($datesMessage, 
                  [
                    'created'   =>  date_format($created, $format), 
                    'updated'   =>  date_format($updated, $format)
                  ]) . '</h5>':'';
                  return '<h3 style="text-align: center;">'
                  . $title
                  . '</h3>'
                  . $datesMessage;
              }
          }

          public static function makeBlocks(array $blocks, string $template, string $cacheKey = null)
          {
              $stringView = new StringView;
              $content    = $stringView->make(
                  [
                      'template'   => $template,
                      'cache_key'  => isset($cacheKey) ? ($cacheKey . app()->getLocale()) : uniqid(),
                      // Delete this
                      'updated_at' => 0
                  ],
                  $blocks
              );

              return $content;
          }

          //P1Y2M5DT1H5M10S  This will set an interval of 1 Year , 2 Months, 5 Days , 1 Hour, 5 Minutes, 10 Seconds
          public static function getDateIntervalDate($years = 0, $months = 0, $days = 0)
          {
              $now = \DateTime::createFromFormat('Y-m-d', (new \DateTime())->format('Y-m-d'));
              $dateInterval = new \DateInterval('P' . abs($years) . 'Y'. abs($months) . 'M'. abs($days) . 'D');
              if($years < 0 || $months < 0 || $days < 0)
              {
                  $dateInterval->invert = 1; //Make it negative.
              }
              $now->add( $dateInterval );

              return $now;
          }

          public static function getDateIntervalTime($hours = 0, $minutes = 0, $seconds = 0)
          {
              $now = \DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime())->format('Y-m-d H:i:s'));
              $dateInterval = new \DateInterval('PT' . abs($hours) . 'H'. abs($minutes) . 'M'. abs($seconds) . 'S');
              if( $hours < 0 || $minutes < 0 || $seconds < 0)
              {
                  $dateInterval->invert = 1; //Make it negative.
              }
              $now->add( $dateInterval );

              return $now;
          }

          public static function escapeBackslash($value)
          {
              return str_replace('\\','\\\\', $value);
          }

          public static function escapeAndNormalize($message)
          {
              return preg_replace('#\R+#', ' ', e($message));
          }

          public static function getImage($imageUrl = null, 
              $id = '', 
              $title = '', 
              $colorBox = '', 
              $bigImageUrl = '', 
              $clickMessage = '')
          {
              if (isset($imageUrl))
              {
                  $message = !empty($clickMessage) ? '<h5 style="text-align: center;">' . trans($clickMessage) . '</h5>':'';
                  $title = self::escapeAndNormalize($title);

                  return
                      '<div style="text-align: center; margin-top: 10px;"><a style="display: block;" title="' . $title
                      .'" id="a_' . $id . uniqid()
                      .'" href="' . $bigImageUrl 
                      . '" class=" ' . $colorBox
                      .'"><img src="' . $imageUrl 
                      .'" id="img_' . $id . uniqid()
                      .'" alt="' . $title
                      .'" width="auto" height="auto" style="max-width:100%;" />'
                      .$message
                      .'</a></div>';
              }
          }

          public static function getSimpleImage($imageUrl = null, 
            $id = '', 
            $title = '')
          {
              if (isset($imageUrl))
              {
                  $title = self::escapeAndNormalize($title);

                  return
                      '<div style="text-align: center; margin-top: 10px;"><img src="' . $imageUrl 
                      .'" id="img_' . $id . uniqid()
                      .'" alt="' . $title
                      .'" width="auto" height="auto" style="max-width:100%;" />'
                      .'</div>';
              }
          }

          public static function getVideo($imageUrl = null, 
              $id = '', 
              $title = '', 
              $colorBox = '', 
              $bigImageUrl = '', 
              $clickMessage = '', 
              $videoId = '', 
              $playVideoMessage = '',
              $youtubeId = '', 
              $playDirectVideoMessage = '')
          {
              if (isset($imageUrl))
              {
                  $message = !empty($clickMessage) ? '<h5 style="text-align: center;">' . trans($clickMessage) . '</h5>':'';
                  $title = self::escapeAndNormalize($title);

                  return
                      '<div style="text-align: center; margin-top: 10px;"><a style="display: block;" title="'.$title
                      .'" id="a_' . $id . uniqid()
                      .'" href="' . $bigImageUrl 
                      . '" class=" '.$colorBox
                      .'"><img src="'. $imageUrl 
                      .'" id="img_' . $id . uniqid()
                      .'" alt="'. $title
                      .'" width="auto" height="auto" style="max-width:100%;" />'
                      .$message
                      .'</a></div><div class="alpaca-form-buttons-container video_link hidden"><button data-video-id="'. $videoId
                      .'" class="alpaca-form-button play-yt-video">' . trans($playVideoMessage) . '</button> </div>'
                      .'<a style="display: none;" title="'.$title
                      .'" id="yt_' . $id . uniqid()
                      .'" href="https://www.youtube.com-nocookie.com/embed/' . $youtubeId 
                      . '" target="_blank" class="direct_video_link"><div class="alpaca-form-buttons-container">'
                      .'<button class="alpaca-form-button ">' . trans($playDirectVideoMessage) . '</button> </div>'
                      .'</a>';
              }
          }


          public static function getCodeBlock($codeBlock = null, $lang = '')
          {
              if (isset($codeBlock))
              {
                  return '<pre class="line-numbers"><code class="language-'
                  . StaticLabel::codeLanguage($lang) .'">'
                  .  htmlspecialchars($codeBlock) 
                  . '</code></pre>';
              }
          }

          public static function getTopic($topicId = '', 
              $postId = '', 
              $actionName='', 
              $topicContent = null, 
              $message = '')
          {
              if (! empty($topicId))
              {
                  $topics = PostTopic::where('topic_id', '=', $topicId)->get();
                  $posts = new Collection;                 
                  foreach ($topics as $topic)
                  {
                      if(! isset($topic->post))
                      {
                          $post = Post::withoutTranslation()->where('id','=', $topic->post_id)->first();
                      }else{
                          $post = $topic->post;
                      }
                      $posts->push($post);
                  }

                  $posts = $posts->sortBy('created_at');
                  
                  $result = '';
                  $count = 0;
                  foreach ($posts as $post)
                  {
                      if ($post->status === StaticLabel::STATUS_OFFLINE)
                      {
                          $result .= '<li style="margin-bottom: .8em;">'. $post->label .'</li>';
                      }elseif ($postId === $post->id)
                      {
                          $result .= '<li style="margin-bottom: .8em;"><b>'. $post->label 
                          .'</b>'. (! empty($message)?'&nbsp;&lt;&nbsp;'.trans($message):'') .'</li>';
                      }else{
                          $result .= '<li style="margin-bottom: .8em;"><span style="text-decoration: underline;"><a href="'
                          . config('app.url') . $actionName . $post->id . ( $count === 0 ? '?#postTopic' . $topicId : '')
                          . '" target="_self" rel="noopener noreferrer">' . $post->label . '</a></span></li>';
                      }
                      $count++;
                  }
                  
                  if($count>1)
                  {
                      $result = '<ol>'. $result . '</ol>';
                  }else{
                      $result = '';
                  }
                  
                  if (isset($topicContent))
                  {
                      $result .= $topicContent;
                  }

                  $posts = null;
                  return $result;
              }
          }

          public static function getRememberUsername()
          {
              $query = 'SELECT '
                . CIOINA_Util::backquote('id') .','
                . CIOINA_Util::backquote('is_remember_username') 
                . ' FROM ' 
                . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                . '.' . CIOINA_Util::backquote('clients')
                . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\'';

              $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

              if (isset($records) && count($records) === 1)
              {
                  return $records[0]['is_remember_username'] === '1' ? true: false;
              }

              return false;
          }

          public static function makeLoginForm($setting = null)
          {
              $schema = json_decode('{' . $setting . '}');

              if (empty($schema))
              {
                  abort(500); 
              }
              $schema->data->saveLogin = self::getRememberUsername();

              $s= json_encode($schema);
              $s = substr($s, 1, strlen($s) - 2);

              return $s;
          }

          public static function renderWebPage($model = null, $nameSpace = '')
          {
              if (! $model->is_public && $model->has_form)
              {
                  $keyAreas = self::setSessionMapster(false);
              }
              
              $allImages = [];

              //This does not work with translated pages
              $pageVideos = $model->web_page_videos;
              if(isset($pageVideos) && !empty($pageVideos))
              {
                  $youtubeList = new Collection; 
                  foreach ($pageVideos as $pageVideo)
                  {
                      $youtubeList->push($pageVideo->video);
                  }

                  $youtubeList = $youtubeList->sortByDesc('updated_at');

                  $vs = '';
                  $i=0;
                  foreach( $youtubeList as $youtube)
                  {
                      $allImages['video_' . $youtube->id] = 
                      self::getVideo($youtube->original_image_url, 
                              $youtube->id,
                              $youtube->label,
                              'img_group1', 
                              $youtube->original_image_url,
                              $nameSpace . '::form.click_image', 
                              $i,
                              $nameSpace . '::form.play_video',
                              $youtube->video_id,
                              $nameSpace . '::form.play_video_direct'
                      );

                      if(!empty($vs))
                      {
                          $vs .= ',';
                      }
                      $vs .= '{ videoURL: "'. $youtube->video_id . '" ';
                      if(!empty($youtube->start))
                      {
                          $vs .= ', startAt: '.  $youtube->start;
                      }
                      if(!empty($youtube->end))
                      {
                          $vs .= ', stopAt: '.$youtube->end;
                      }
                      //if(array_key_exists('autoPlay', $video))
                      //{
                      //    $vs .= ', autoPlay: '. $video['autoPlay'];
                      //}
                      $vs .= '}';
                      $i++;
                  }

                  $videos = 'var $videos = ['.$vs.']';
                  $youtubeList = null;
              }

              //This does not work with translated pages
              $web_page_images = $model->web_page_settings;
              if(isset($web_page_images) && !empty($web_page_images))
              {
                  
                  $startLen = strlen(self::BLADE_START_MARK);
                  $endLen = strlen(self::BLADE_END_MARK);

                  foreach ($web_page_images as $pageImage)
                  {
                      $setting = $pageImage->setting;

                      if (isset($setting) && !empty($setting))
                      {
                          //Begin: we need this for AJAX Forms with translation 
                          if(Str::startsWith($setting->code_block, self::BLADE_START_MARK))
                          {
                              if ( ( $end = strpos($setting->code_block, self::BLADE_END_MARK) ) !== false)
                              {
                                  $numberLen = $end - $startLen;
                                  if ($numberLen > 0)
                                  {
                                      $id = trim(substr($setting->code_block, $startLen , $numberLen));
                                      if (! is_numeric($id))
                                      {
                                          abort(500);
                                      }
                                      $enSetting = Setting::withoutTranslation()->findOrFail(trim($id));

                                      $js = trim(substr($setting->code_block, $end + $endLen));
                                      if(empty($js))
                                      {
                                          if($enSetting->id === self::ID_SETTING_LOGIN_FORM)
                                          {
                                              $settingData = self::makeBlocks(
                                                  [
                                                    'name_space' =>$nameSpace,
                                                   ], 
                                                   self::makeLoginForm($enSetting->code_block),
                                                   $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));

                                          }elseif($enSetting->id === self::ID_SETTING_MAPSTER){
                                              $keyAreas = self::setSessionMapster(true);

                                              $settingData = self::makeBlocks(
                                                  [
                                                    'name_space' =>$nameSpace,
                                                    'key_areas' => $keyAreas,
                                                    'step_id'  => self::getSessionImageMapId(),
                                                   ],
                                                   $enSetting->code_block,
                                                   $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));

                                          }elseif($enSetting->id === self::ID_SETTING_YOUTUBE_PLAYER){
                                              if (isset($videos)){
                                                  $settingData = self::makeBlocks(
                                                       [
                                                         'videos'  => $videos,
                                                        ],
                                                        $enSetting->code_block,
                                                        $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                              }
                                          }elseif($enSetting->id === self::ID_SETTING_ROBOT){
                                              $settingData = $setting->content 
                                                  . self::makeBlocks(['name_space' => $nameSpace,], 
                                                  $enSetting->code_block,
                                                  $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                          }else{
                                              if (isset($keyAreas) && $model->has_form)
                                              {
                                                  $settingData = self::makeBlocks(
                                                      [
                                                        'name_space' => $nameSpace,
                                                        'key_areas' => $keyAreas,
                                                        'step_id'  => self::getSessionImageMapId(),
                                                       ],
                                                       $enSetting->code_block,
                                                       $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                                  
                                              }else{

                                                  $settingData = self::makeBlocks(['name_space' => $nameSpace,], 
                                                      $enSetting->code_block, 
                                                      $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                              }
                                          }
                                      }else{
                                          abort(500);
                                      }
                                  }else{
                                      abort(500); 
                                  }
                              }else{
                                  $settingData = self::makeBlocks(['name_space' => $nameSpace,], 
                                      $setting->code_block, 
                                      $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                              }
                              //End: we need this for AJAX Forms with translation
                          }else{
                              if($setting->id === self::ID_SETTING_LOGIN_FORM)
                              {
                                  $settingData = self::makeBlocks(
                                      [
                                        'name_space' =>$nameSpace,
                                       ], 
                                       self::makeLoginForm($setting->code_block), 
                                       $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));

                              }elseif($setting->id === self::ID_SETTING_MAPSTER){
                                  $keyAreas = self::setSessionMapster(true);

                                  $settingData = self::makeBlocks(
                                      [
                                        'name_space' =>$nameSpace,
                                        'key_areas' => $keyAreas,
                                        'step_id'  => self::getSessionImageMapId(),
                                       ],
                                       $setting->code_block,
                                       $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));

                              }elseif($setting->id === self::ID_SETTING_YOUTUBE_PLAYER){
                                  if (isset($videos)){
                                      $settingData = self::makeBlocks(
                                           [
                                             'videos'  => $videos,
                                            ],
                                            $setting->code_block,
                                            $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                                  }
                              }elseif($setting->id === self::ID_SETTING_ROBOT){
                                  $settingData = $setting->content 
                                      . self::makeBlocks(['name_space' =>$nameSpace,],
                                      $setting->code_block,
                                      $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                              }else{
                                  if (isset($keyAreas) && $model->has_form)
                                  {
                                      $settingData = self::makeBlocks(
                                        [
                                          'name_space' =>$nameSpace,
                                          'key_areas' => $keyAreas,
                                          'step_id'  => self::getSessionImageMapId(),
                                         ],
                                         $setting->code_block,
                                         $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                                      
                                  }else{
                                      $settingData = self::makeBlocks(['name_space' =>$nameSpace,], 
                                          $setting->code_block,
                                          $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                                  }
                              }
                          }

                          $allImages['setting_' . $setting->id] = $settingData;
                      }
                  }
              }

              //This does not work with translated pages
              $web_page_images = $model->web_page_images;
              if(isset($web_page_images) && !empty($web_page_images))
              {
                  foreach ($web_page_images as $pageImage)
                  {
                      $image = $pageImage->image;
                      $allImages['image_' . $image->id] = 
                          self::getImage($image->original_image_url, 
                          $image->id,
                          $image->label,
                          'img_group1', 
                          $image->original_image_url,
                          $nameSpace . '::form.click_image');
                  }
              }

              
              return self::makeBlocks($allImages, 
                  $model->content, 
                  $model->cache_key . $model->updated_at->format('Y-m-d H:i:s'));
          }

          public static function renderPost($model = null, $nameSpace = '', $postUrl = '')
          {
              $allImages = [];

              $pageVideos = $model->post_videos;
              if(isset($pageVideos) && !empty($pageVideos))
              {
                  $youtubeList = new Collection; 
                  foreach ($pageVideos as $pageVideo)
                  {
                      $youtubeList->push($pageVideo->video);
                  }

                  $youtubeList = $youtubeList->sortByDesc('updated_at');

                  $vs = '';
                  $i=0;
                  foreach( $youtubeList as $youtube)
                  {
                      $allImages['video_' . $youtube->id] = 
                      self::getVideo($youtube->original_image_url, 
                              $youtube->id,
                              $youtube->label,
                              'img_group1', 
                              $youtube->original_image_url,
                              $nameSpace . '::form.click_image', 
                              $i,
                              $nameSpace . '::form.play_video',
                              $youtube->video_id,
                              $nameSpace . '::form.play_video_direct'
                       );

                      if(!empty($vs))
                      {
                          $vs .= ',';
                      }
                      $vs .= '{ videoURL: "'. $youtube->video_id . '" ';
                      if(!empty($youtube->start))
                      {
                          $vs .= ', startAt: '.  $youtube->start;
                      }
                      if(!empty($youtube->end))
                      {
                          $vs .= ', stopAt: '.$youtube->end;
                      }
                      //if(array_key_exists('autoPlay', $video))
                      //{
                      //    $vs .= ', autoPlay: '. $video['autoPlay'];
                      //}
                      $vs .= '}';
                      $i++;
                  }

                  $videos = 'var $videos = ['.$vs.']';
                  $youtubeList = null;
              }

              $web_page_images = $model->post_settings;
              if(isset($web_page_images) && !empty($web_page_images))
              {
                  
                  $startLen = strlen(self::BLADE_START_MARK);
                  $endLen = strlen(self::BLADE_END_MARK);

                  foreach ($web_page_images as $pageImage)
                  {
                      $setting = $pageImage->setting;

                      if (isset($setting) && !empty($setting))
                      {
                          //Begin: we need this for AJAX Forms with translation 
                          if(Str::startsWith($setting->code_block, self::BLADE_START_MARK))
                          {
                              if ( ( $end = strpos($setting->code_block, self::BLADE_END_MARK) ) !== false)
                              {
                                  $numberLen = $end - $startLen;
                                  if ($numberLen > 0)
                                  {
                                      $id = trim(substr($setting->code_block, $startLen , $numberLen));
                                      if (! is_numeric($id))
                                      {
                                          abort(500);
                                      }
                                      $enSetting = Setting::withoutTranslation()->findOrFail(trim($id));

                                      $js = trim(substr($setting->code_block, $end + $endLen));
                                      if(empty($js))
                                      {
                                          if($enSetting->id === self::ID_SETTING_MAPSTER)
                                          {
                                              $keyAreas = self::setSessionMapster(true);

                                              $settingData = self::makeBlocks(
                                                  [
                                                    'name_space' =>$nameSpace,
                                                    'key_areas' => $keyAreas,
                                                    'step_id'  => self::getSessionImageMapId(),
                                                   ],
                                                   $enSetting->code_block,
                                                   $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));

                                          }elseif($enSetting->id === self::ID_SETTING_YOUTUBE_PLAYER){
                                              if (isset($videos)){
                                                  $settingData = self::makeBlocks(
                                                       [
                                                         'videos'  => $videos,
                                                        ],
                                                        $enSetting->code_block,
                                                        $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                              }
                                          }elseif($enSetting->id === self::ID_SETTING_ROBOT){
                                              $settingData = $setting->content 
                                                  . self::makeBlocks(['name_space' =>$nameSpace,],
                                                  $enSetting->code_block,
                                                  $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                          }else{
                                              if (isset($keyAreas))
                                              {
                                                  $settingData = self::makeBlocks(
                                                      [
                                                        'name_space' =>$nameSpace,
                                                        'key_areas' => $keyAreas,
                                                        'step_id'  => self::getSessionImageMapId(),
                                                       ],
                                                       $enSetting->code_block,
                                                       $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                                  
                                              }else{

                                                  $settingData = self::makeBlocks(['name_space' =>$nameSpace,], 
                                                      $enSetting->code_block,
                                                      $enSetting->cache_key . $enSetting->updated_at->format('Y-m-d H:i:s'));
                                              }
                                          }
                                      }else{
                                          abort(500);
                                      }
                                  }else{
                                      abort(500); 
                                  }
                              }else{
                                  $settingData = self::makeBlocks(['name_space' =>$nameSpace,],
                                      $setting->code_block,
                                      $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                              }
                              //End: we need this for AJAX Forms with translation
                          }else{
                              if($setting->id === self::ID_SETTING_MAPSTER)
                              {
                                  $keyAreas = self::setSessionMapster(true);

                                  $settingData = self::makeBlocks(
                                      [
                                        'name_space' =>$nameSpace,
                                        'key_areas' => $keyAreas,
                                        'step_id'  => self::getSessionImageMapId(),
                                       ],
                                       $setting->code_block,
                                       $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));

                              }elseif($setting->id === self::ID_SETTING_YOUTUBE_PLAYER){
                                  if (isset($videos)){
                                      $settingData = self::makeBlocks(
                                           [
                                             'videos'  => $videos,
                                            ],
                                            $setting->code_block,
                                            $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                                  }
                              }elseif($setting->id === self::ID_SETTING_ROBOT){
                                  $settingData = $setting->content 
                                      . self::makeBlocks(['name_space' => $nameSpace,], 
                                      $setting->code_block,
                                      $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                              }else{
                                  if (isset($keyAreas))
                                  {
                                      $settingData = self::makeBlocks(
                                        [
                                          'name_space' =>$nameSpace,
                                          'key_areas' => $keyAreas,
                                          'step_id'  => self::getSessionImageMapId(),
                                         ],
                                         $setting->code_block,
                                         $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                                      
                                  }else{
                                      $settingData = self::makeBlocks(['name_space' => $nameSpace,], 
                                          $setting->code_block,
                                          $setting->cache_key . $setting->updated_at->format('Y-m-d H:i:s'));
                                  }
                              }
                          }

                          $allImages['setting_' . $setting->id] = $settingData;
                      }
                  }
              }

              $web_page_images = $model->post_images;
              if(isset($web_page_images) && !empty($web_page_images))
              {
                  foreach ($web_page_images as $pageImage)
                  {
                      $image = $pageImage->image;
                      $allImages['image_' . $image->id] = 
                          self::getImage($image->original_image_url, 
                          $image->id,
                          $image->label,
                          'img_group1', 
                          $image->original_image_url,
                          $nameSpace . '::form.click_image');
                  }
              }

              $post_parts = $model->post_topics;
              if(isset($post_parts) && !empty($post_parts))
              {
                  $firstTopic = true;
                  foreach ($post_parts as $value)
                  {
                      if(! isset($value->topic))
                      {
                          $postJoin = Topic::withoutTranslation()->where('id','=', $value->topic_id)->first();
                      }else{
                          $postJoin = $value->topic;
                      }
                      $allImages['topic_' . $postJoin->id] = 
                          self::getTopic($postJoin->id, 
                          $model->id, 
                          $postUrl,
                          $firstTopic ? $postJoin->content : null,
                          $nameSpace . '::form.you_are_here'
                          );
                      $firstTopic = false;
                  }
              }

              $post_parts = $model->post_parts;
              if(isset($post_parts) && !empty($post_parts))
              {
                  foreach ($post_parts as $value)
                  {
                      $postJoin = $value->code_block;
                      $allImages['code_block_' . $postJoin->id] = 
                          self::getCodeBlock($postJoin->code_block, 
                               $postJoin->code_type);
                  }
              }

              return self::makeBlocks($allImages, 
                  $model->content, 
                  $model->cache_key . $model->updated_at->format('Y-m-d H:i:s'));
          }

      }