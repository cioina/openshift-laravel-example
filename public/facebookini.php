<?php
require_once 'libraries/laravel.inc.acioina.php';

use \Facebook\Facebook;
use \Facebook\Exceptions\FacebookSDKException;
use \Facebook\Exceptions\FacebookResponseException;
use \Facebook\Authentication\AccessToken;

$query = 'SELECT '
 . CIOINA_Util::backquote('code_block') 
 . ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
 . '.' . CIOINA_Util::backquote('settings')
 . ' WHERE ' . CIOINA_Util::backquote('id') . '= 55';
$records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

$query = 'SELECT '
. CIOINA_Util::backquote('fb_email') .','
. CIOINA_Util::backquote('fb_token') .','
. CIOINA_Util::backquote('fb_id') 
. ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
. '.' . CIOINA_Util::backquote('clients')
. ' WHERE ' . CIOINA_Util::backquote('id') . '= 1';
$fb_user = $GLOBALS['CIOINA_dbi']->fetchResult($query);

if (isset($records) && count($records) === 1){
    $json = json_decode('{' . $records[0] . '}');
}

if (isset($fb_user) && count($fb_user) === 1 
    && isset($json) && isset($json->data)
    && isset($json->data->IsFacebookEnabled) && $json->data->IsFacebookEnabled === true
    )
{
    if(isset( $_SESSION['fb_token'] ) && isset( $_SESSION['logoutUrl'] )){
        unset($_SESSION['fb_token']);
        unset($_SESSION['fb_picture']);
    }
    
    $httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']: null;
    
    if( isset($httpReferer) && 
        $httpReferer === vsprintf($GLOBALS['CIOINA_Config']->get('FacebookLoginUri'), [$GLOBALS['CIOINA_Config']->get('LaravelAdminUri')]) )
    {
        
    }elseif( defined('ACIOINA_FACEBOOK') )
    {
        
    }else{
        unset($query, $records, $fb_user, $json, $httpReferer);
        exit;
    }

    unset($query, $records, $fb_user, $json, $httpReferer);

    $permissions = ['email'];
    
    try {
        $fb = new Facebook([
         'app_id'     => $GLOBALS['CIOINA_Config']->get('FacebookAppId'),
         'app_secret' => $GLOBALS['CIOINA_Config']->get('FacebookAppSecret'),
         'default_graph_version' => $GLOBALS['CIOINA_Config']->get('FacebookGraphVersion')]);

        $newHelper = $fb->getRedirectLoginHelper();

        if ( isset( $_SESSION['fb_token'] ) ) {
            $accessToken = $_SESSION['fb_token'];
            if (!$accessToken instanceof AccessToken) {
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

    if ( isset( $accessToken ) ) {
        $_SESSION['fb_token'] = $accessToken;

        if ( isset( $_SESSION['loginUrl'] ) ) 
        {
            $_SESSION['logoutUrl'] = $newHelper->getLogoutUrl( $accessToken,  $GLOBALS['CIOINA_Config']->get('FacebookRedirectUri'));

            unset($_SESSION['loginUrl'], $permissions, $accessToken, $newHelper, $fb);
            
            header( 'Location: ' . vsprintf($GLOBALS['CIOINA_Config']->get('FacebookLoginUri'), [$GLOBALS['CIOINA_Config']->get('LaravelAdminUri')]) );
        }
        else{
            if(isset( $_SESSION['fb_picture'] ) ){
                $_SESSION['logoutUrl'] = $newHelper->getLogoutUrl( $accessToken,  $GLOBALS['CIOINA_Config']->get('FacebookRedirectUri'));
            }else{
                $_SESSION['loginUrl'] = $newHelper->getLoginUrl($GLOBALS['CIOINA_Config']->get('FacebookRedirectUri'), $permissions );

                unset($_SESSION['logoutUrl'], $_SESSION['fb_token']);
            }

            unset($permissions, $accessToken, $newHelper, $fb);

            header( 'Location: ' . vsprintf($GLOBALS['CIOINA_Config']->get('FacebookLoginUri'), [$GLOBALS['CIOINA_Config']->get('LaravelAdminUri')]) );
        }
        
    } else {
        $_SESSION['loginUrl'] = $newHelper->getLoginUrl($GLOBALS['CIOINA_Config']->get('FacebookRedirectUri'), $permissions );

        unset($_SESSION['logoutUrl'], $_SESSION['fb_token'], $_SESSION['fb_picture'], $permissions, $newHelper, $fb);
        
        header( 'Location: ' . vsprintf($GLOBALS['CIOINA_Config']->get('FacebookLoginUri'), [$GLOBALS['CIOINA_Config']->get('LaravelAdminUri')]) );
    }
    
}