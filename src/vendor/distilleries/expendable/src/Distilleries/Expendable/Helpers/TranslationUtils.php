<?php namespace Distilleries\Expendable\Helpers;

      use Illuminate\Support\Str;
      
      class TranslationUtils {

          const KEY_OVERRIDE_LOCAL = 'local_override';
          const KEY_LOCALE = 'locale';
          const KEY_REDIRECT_BACK = 'redirect_back_from_language';
          const KEY_ADMIN_SERVICE_PROVIDER = 'ExpendableServiceProviderEnabledCounter';
          const COUNT_ADMIN_SERVICE_PROVIDER = 10;

          public static function overrideLocal($iso = null)
          {
              if ( isset($iso) && Str::length($iso) == 2 ) 
              {
                  config([self::KEY_OVERRIDE_LOCAL => $iso]);
              }
          }

          public static  function resetOverrideLocal()
          {
              config([self::KEY_OVERRIDE_LOCAL => null]);
          }

          public static function forgetRedirectBack()
          {
              $session_id = session_id();

              if (!empty($session_id) || defined('TESTSUITE'))
              {
                  unset($_SESSION[self::KEY_REDIRECT_BACK]);
              }
          }

          public static function setRedirectBack(array $redirect = null)
          {
              $session_id = session_id();

              if (!empty($session_id) || defined('TESTSUITE'))
              {
                  $_SESSION[self::KEY_REDIRECT_BACK] = $redirect;
              }
          }

          public static function getRedirectBack()
          {
              $session_id = session_id();

              if (!empty($session_id) || defined('TESTSUITE'))
              {
                  if (isset($_SESSION[self::KEY_REDIRECT_BACK])) 
                  {
                      return $_SESSION[self::KEY_REDIRECT_BACK];
                  }
                  
              }
              return false;
          }

          public static function incAdminServiceProvaderCounter()
          {
              $session_id = session_id();

              if (!empty($session_id))
              {
                 if (isset($_SESSION[self::KEY_ADMIN_SERVICE_PROVIDER]))
                  {
                      $counter = (int)$_SESSION[self::KEY_ADMIN_SERVICE_PROVIDER];
                      if($counter < 10)
                      {
                          $_SESSION[self::KEY_ADMIN_SERVICE_PROVIDER] = ++$counter; 
                      }
                  }else{
                      $_SESSION[self::KEY_ADMIN_SERVICE_PROVIDER] = 1;
                  }
              }
          }

          public static function isAdminServiceProvader()
          {
              if (defined('INCLUDE_PATH'))
              {
                  return $GLOBALS['CIOINA_Config']->get('ExpendableServiceProviderEnabled');
              }

              $session_id = session_id();

              if (!empty($session_id))
              {
                  if($GLOBALS['CIOINA_Config']->get('ExpendableServiceProviderEnabled'))
                  {
                      return true; 
                  }

                  if(isset($_SESSION[self::KEY_ADMIN_SERVICE_PROVIDER]))
                  {
                      $counter = (int)$_SESSION[self::KEY_ADMIN_SERVICE_PROVIDER];
                      if($counter === self::COUNT_ADMIN_SERVICE_PROVIDER)
                      {
                          return true;
                      }
                  }
              }

              return false;

          }

      }