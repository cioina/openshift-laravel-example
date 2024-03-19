<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Http\Controllers\Base\BaseComponent;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Formatter\Message;

      use \CIOINA_Util;

      class ClientLogoutController
      {
          public function getIndex()
          {
              $client = FormUtils::getClientLogin();
              FormUtils::forgetSessionOnlineId();

              $redirectUrl = FormUtils::getDefaultRedirect();

              if($client === false)
              {
                  return redirect()->to($redirectUrl, 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'))->with(Message::WARNING, [trans('user-management::form.client_not_logout')]);
                  
              }else{
                  $now = (new \DateTime())->format('Y-m-d H:i:s');

                  $query = ' UPDATE ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                  . '.' . CIOINA_Util::backquote('online_clients')
                  . ' SET ' . CIOINA_Util::backquote('updated_at') . '= '
                  . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
                  . CIOINA_Util::backquote('is_logged_out') . '= 1'
                  . ' WHERE '. CIOINA_Util::backquote('ip_address') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
                  . ' AND ' . CIOINA_Util::backquote('session_id') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes(session_id()) . '\''
                  . ' AND ' . CIOINA_Util::backquote('id') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes($client[0]['onlineRowId']) . '\''
                  . ' AND ' . CIOINA_Util::backquote('client_id') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes($client[0]['id']) . '\''
                  . ' AND ' . CIOINA_Util::backquote('online_id') . '='
                  . '\'' . CIOINA_Util::sqlAddSlashes($client[0]['onlineId']) . '\'';

                  $GLOBALS['CIOINA_dbi']->tryQuery($query);

                  return redirect()->to($redirectUrl, 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'))->with(Message::MESSAGE, [trans('user-management::form.client_logout')]);
              }
          }

          public function getCookies()
          {
              FormUtils::forgetCookies();

              $redirectUrl = FormUtils::getDefaultRedirect();

              return redirect()->to($redirectUrl, 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'))->with(Message::MESSAGE, [trans('user-management::errors.cookies_accept')]);
          }

      }