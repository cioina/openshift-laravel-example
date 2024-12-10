<?php namespace Distilleries\Expendable\Helpers;

      use \Auth;
      use \Session;

      class UserUtils {

          const KEY_SESSION_LOGGED_IN = 'isLoggedIn';
          const KEY_SESSION_PERMISSIONS = 'permissions';
          const KEY_SESSION_DISPLAY_ALL_STATUS = 'display_all_status';

          public static function get()
          {
              return Auth::user();
          }

          public static function getEmail()
          {
              return Auth::user()->getEmailForPasswordReset();
          }

          public static function getDisplayName()
          {
              return Auth::user()->role->initials;
          }

          public static function isSuperAdmin()
          {
              return (Auth::user()->role->initials === '@sa') 
                  && (Auth::user()->role->overide_permission === 1);
          }

          public static function setArea($area)
          {
              Session::put(self::KEY_SESSION_PERMISSIONS, $area);
          }

          public static function getArea()
          {
              return Session::get(self::KEY_SESSION_PERMISSIONS);
          }

          public static function forgotArea()
          {
              Session::forget(self::KEY_SESSION_PERMISSIONS);
          }

          public static function hasAccess($key)
          {
              $key  = ltrim($key, "\\");
              $area = self::getArea();
              $area = (is_array($area) && !empty($area)) ? $area : [];

              return in_array($key, $area);
          }

          public static function hasDisplayAllStatus()
          {
              return Session::get(self::KEY_SESSION_DISPLAY_ALL_STATUS, false);
          }

          public static function forgotDisplayAllStatus()
          {
              Session::forget(self::KEY_SESSION_DISPLAY_ALL_STATUS);
          }

          public static function setDisplayAllStatus()
          {
              Session::put(self::KEY_SESSION_DISPLAY_ALL_STATUS, true);
          }

          public static function setIsLoggedIn()
          {
              $session_id = session_id();

              if (!empty($session_id))
              {
                  $_SESSION[self::KEY_SESSION_LOGGED_IN] = true;
              }
          }

          public static function getIsLoggedIn()
          {
              $session_id = session_id();

              if (!empty($session_id))
              {
                  return isset($_SESSION[self::KEY_SESSION_LOGGED_IN]) 
                      && $_SESSION[self::KEY_SESSION_LOGGED_IN] === true;
              }
              return false;
          }

          public static function forgotIsLoggedIn()
          {
              $session_id = session_id();

              if (!empty($session_id))
              {
                  unset($_SESSION[self::KEY_SESSION_LOGGED_IN]);
              }
          }
      }