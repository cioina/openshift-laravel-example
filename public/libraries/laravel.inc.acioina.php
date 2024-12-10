<?php
/**
 * block attempts to directly run this script
 */
if (getcwd() == dirname(__FILE__))
{
    die('Attack stopped');
}

/**
 * Minimum PHP version; can't call CIOINA_fatalError() which uses a
 * PHP 5 function, so cannot easily localize this message.
 */
if (version_compare(PHP_VERSION, '7.3.0', 'lt'))
{
    die('PHP 7.3+ is required');
}

/**
 * for verification in all procedural scripts under libraries
 */
if (! defined('TESTSUITE')) {
    define('ACIOINA', true);
}

/**
 * String handling (security)
 */
require_once './libraries/String.class.acioina.php';
//$CIOINA_String = new CIOINA_String();
/**
 * the error handler
 */
require_once './libraries/Error_Handler.class.acioina.php';

if (! defined('TESTSUITE')) {

    /**
     * initialize the error handler
     */
    $GLOBALS['CIOINA_error_handler'] = new CIOINA_Error_Handler();
}
/**
 * core functions
 */
require_once './libraries/core.lib.acioina.php';

/**
 * Input sanitizing
 */
require_once './libraries/sanitizing.lib.acioina.php';

/**
 * Warning about mbstring.
 */
if (! function_exists('mb_detect_encoding'))
{
    CIOINA_warnMissingExtension('mbstring', true);
}

/**
 * the CIOINA_Config class
 */
require_once './libraries/Config.class.acioina.php';

/**
 * common functions
 */
include_once './libraries/Util.class.acioina.php';

/**
 * JavaScript escaping.
 */
include_once './libraries/js_escape.lib.acioina.php';

/**
 * Include URL/hidden inputs generating.
 */
include_once './libraries/url_generating.lib.acioina.php';

/**
 * Used to generate the page
 */
include_once './libraries/Response.class.acioina.php';

/**
 * PATH_INFO could be compromised if set, so remove it from PHP_SELF
 * and provide a clean PHP_SELF here
 */
$CIOINA_PHP_SELF = CIOINA_getenv('PHP_SELF');
$_PATH_INFO = CIOINA_getenv('PATH_INFO');
if (! empty($_PATH_INFO) && ! empty($CIOINA_PHP_SELF)) {
    $path_info_pos = /*overload*/mb_strrpos($CIOINA_PHP_SELF, $_PATH_INFO);
    $pathLength = $path_info_pos + /*overload*/mb_strlen($_PATH_INFO);
    if ($pathLength === /*overload*/mb_strlen($CIOINA_PHP_SELF))
    {
        $CIOINA_PHP_SELF = /*overload*/mb_substr($CIOINA_PHP_SELF, 0, $path_info_pos);
    }
}
$CIOINA_PHP_SELF = htmlspecialchars($CIOINA_PHP_SELF);

/**
 * just to be sure there was no import (registering) before here
 * we empty the global space (but avoid unsetting $variables_list
 * and $key in the foreach (), we still need them!)
 */
$variables_whitelist = array (
    'GLOBALS',
    '_SERVER',
    '_GET',
    '_POST',
    '_REQUEST',
    '_FILES',
    '_ENV',
    '_COOKIE',
    '_SESSION',
    'error_handler',
    'CIOINA_PHP_SELF',
    'variables_whitelist',
    'key',
    'CIOINA_String'
);

foreach (get_defined_vars() as $key => $value)
{
    if (! in_array($key, $variables_whitelist))
    {
        unset($key);
    }
}
unset($key, $value, $variables_whitelist);

$_REQUEST = array_merge($_GET, $_POST);

/**
 * check timezone setting
 * this could produce an E_STRICT - but only once,
 * if not done here it will produce E_STRICT on every date/time function
 * (starting with PHP 5.3, this code can produce E_WARNING rather than
 *  E_STRICT)
 *
 */
date_default_timezone_set(@date_default_timezone_get());

/**
 * We really need this one!
 */
if (! function_exists('preg_replace'))
{
    CIOINA_warnMissingExtension('pcre', true);
}

/**
 * JSON is required in several places.
 */
if (! function_exists('json_encode'))
{
    CIOINA_warnMissingExtension('json', true);
}

/**
 * @global CIOINA_Config $GLOBALS['CIOINA_Config']
 * force reading of config file, because we removed sensitive values
 * in the previous iteration
 */

if (! defined('TESTSUITE')) {

    $GLOBALS['CIOINA_Config'] = new CIOINA_Config(null);

    $GLOBALS['CIOINA_Config']->checkPmaAbsoluteUri();


    /**
     * BC - enable backward compatibility
     * exports all configuration settings into $GLOBALS ($GLOBALS['cfg'])
     */
    $GLOBALS['CIOINA_Config']->enableBc();
}

/**
 * check HTTPS connection
 */
if (php_sapi_name() !== 'cli' &&
    $GLOBALS['CIOINA_Config']->get('ForceSSL') &&
    ! $GLOBALS['CIOINA_Config']->detectHttps())
{
    // grab SSL URL
    $url = $GLOBALS['CIOINA_Config']->getSSLUri();
    $baseName = strtolower(basename($CIOINA_PHP_SELF));
    if ($baseName === 'index.php'){
        $baseName = $GLOBALS['CIOINA_Config']->get('HomePage');
    }

    // Actually redirect
    CIOINA_sendHeaderLocation($url . $baseName . CIOINA_URL_getCommon($_GET, 'text'));
    unset($url, $baseName);
    exit;
}

if(isset($_SERVER['REQUEST_URI'])
    && ($_SERVER['REQUEST_URI'] .'index.php' === $CIOINA_PHP_SELF))
{
    $url = $GLOBALS['CIOINA_Config']->get('ForceSSL') ? $GLOBALS['CIOINA_Config']->getSSLUri() : $GLOBALS['CIOINA_Config']->get('PmaAbsoluteUri');
    CIOINA_sendHeaderLocation($url . $GLOBALS['CIOINA_Config']->get('HomePage') . CIOINA_URL_getCommon($_GET, 'text'));
    unset($url);
    exit;
}

if($GLOBALS['CIOINA_Config']->get('DownForMaintenance') &&
/*overload*/mb_strpos($_SERVER['REQUEST_URI'], $GLOBALS['CIOINA_Config']->get('LaravelAdminUri')) === false)
{
    CIOINA_setHeaderStatusCode(503);
    CIOINA_jsonMessage($GLOBALS['CIOINA_Config']->get('DownForMaintenanceMessage'), false);
}

/**
 * check for errors occurred while loading configuration
 * this check is done here after loading language files to present errors in locale
 */
$GLOBALS['CIOINA_Config']->checkPermissions();

if (!function_exists('__'))
{
    include_once './libraries/php-gettext/gettext.inc';
}

if ($GLOBALS['CIOINA_Config']->error_config_file)
{
    trigger_error(
        __(
            'Failed to read configuration file! This usually means there is a syntax error in it, please check any errors shown below.'
        ),
        E_USER_ERROR
    );
}

if ($GLOBALS['CIOINA_Config']->error_config_default_file)
{
    trigger_error(
        sprintf(
        __('Could not load default configuration from: %1$s'),
        $GLOBALS['CIOINA_Config']->default_source
        ),
        E_USER_ERROR
    );
}

if (php_sapi_name() !== 'cli')
{
    if ($GLOBALS['CIOINA_Config']->error_CIOINA_uri)
    {
        trigger_error(
            __(
                'The PmaAbsoluteUri directive MUST be set in your configuration file!'
            ),
            E_USER_ERROR
        );
    }
}

if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS']))
{
    CIOINA_fatalError(__("GLOBALS overwrite attempt"));
}

/**
 * protect against possible exploits - there is no need to have so many variables
 */
if (count($_REQUEST) > 1000)
{
    CIOINA_fatalError(__('possible exploit'));
}

/**
 * Check for numeric keys
 * (if register_globals is on, numeric key can be found in $GLOBALS)
 */
foreach ($GLOBALS as $key => $dummy)
{
    if (is_numeric($key))
    {
        CIOINA_fatalError(__('numeric key detected'));
    }
}
unset($dummy);

if (php_sapi_name() != 'cli' && ( getenv('HTTP_PROXY') || !empty($_SERVER['HTTP_PROXY']) ))
{
    CIOINA_fatalError(__('PHP has no reliable mechanism to get environment variables that start with "HTTP_"'));
}


//require './libraries/Security.class.acioina.php';

//$security = new CIOINA_Security;
//CIOINA_arrayWalkRecursive($_GET, function (&$input) use($security){
//            $input = $security->xss_clean($input);
//        }, false);
//CIOINA_arrayWalkRecursive($_POST, function (&$input) use($security){
//            $input = $security->xss_clean($input);
//        }, false);


// Autoload the required files
require_once( '../vendor1/autoload.php' );

use \Mailgun\Mailgun;
//use \Facebook\Facebook;
//use \Facebook\Exceptions\FacebookSDKException;
//use \Facebook\Exceptions\FacebookResponseException;
//use \Facebook\Authentication\AccessToken;

include_once './libraries/database_interface.inc.acioina.php';

if (! defined('TESTSUITE')) {
    $link = $GLOBALS['CIOINA_dbi']->connect(
        $GLOBALS['CIOINA_Config']->get('MySqlUser'),
        $GLOBALS['CIOINA_Config']->get('MySqlPWord'),
        false,
        [
            'host'     => $GLOBALS['CIOINA_Config']->get('MySqlHost'),
            'port'     => $GLOBALS['CIOINA_Config']->get('MySqlPort'),
            'database' => $GLOBALS['CIOINA_Config']->get('MySqlDatabase'),
        ]
      );
    $GLOBALS['userlink'] = $link ;
}

$query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') FROM '
. CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
. '.' . CIOINA_Util::backquote('web_statistics')
. ' WHERE ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
. '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d H:i:s')) . '\','
. '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
. ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
. '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d')) . '\','
. '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d') .  '\')';
$today_before = $GLOBALS['CIOINA_dbi']->fetchValue($query);

if ($today_before < $GLOBALS['CIOINA_Config']->get('TotalStatisticsRecordsPerDay'))
{
    $countQuery = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') FROM '
    . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
    . '.' . CIOINA_Util::backquote('web_statistics')
    . ' WHERE ' . CIOINA_Util::backquote('is_fake_visitor') . ' = 1'
    . ' AND ' . CIOINA_Util::backquote('request_ip_address') . '='
    . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
    . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
    . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d H:i:s')) . '\','
    . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
    . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
    . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d')) . '\','
    . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d') .  '\')';
    $count = $GLOBALS['CIOINA_dbi']->fetchValue($countQuery);

    if($count == 0)
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $query = 'INSERT INTO '
            . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
            . '.' . CIOINA_Util::backquote('web_statistics')
            . ' (request_date, absolute_uri, http_user_agent, referrer, request_session, '
            . ' unique_id, updated_at, created_at, request_ip_address) '
            . ' VALUES('
            . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d H:i:s')) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes(htmlspecialchars((isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'/'))) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes(getenv('HTTP_USER_AGENT')) . '\','
            . ((!isset($_SERVER['HTTP_REFERER']))?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($_SERVER['HTTP_REFERER']) . '\',')
            . ((!isset($_COOKIE['PHPSESSID']))?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($_COOKIE['PHPSESSID']) . '\',')
            . ((getenv('HOSTNAME') =='')?'NULL,':'\''. CIOINA_Util::sqlAddSlashes(getenv('HOSTNAME')) . '\',')
            . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\')';
        $GLOBALS['CIOINA_dbi']->tryQuery($query);

        $query = 'SELECT COUNT('. CIOINA_Util::backquote('id') . ') FROM '
            . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
            . '.' . CIOINA_Util::backquote('web_statistics')
            . ' WHERE ' . CIOINA_Util::backquote('request_ip_address') . '='
            . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
            . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
            . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d H:i:s')) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
            . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
            . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d')) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d') .  '\')';
        $count = $GLOBALS['CIOINA_dbi']->fetchValue($query);

        if ($count < $GLOBALS['CIOINA_Config']->get('TotalSessionsPerIpAddress'))
        {
            $query = ' UPDATE '
            . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
            . '.' . CIOINA_Util::backquote('web_statistics')
            . ' INNER JOIN (SELECT' . CIOINA_Util::backquote('T4') . '.' . CIOINA_Util::backquote('id') .', '.CIOINA_Util::backquote('T4') . '.' . CIOINA_Util::backquote('request_ip_address')
            . ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) . '.' . CIOINA_Util::backquote('web_statistics') . ' AS T4'
            . ' INNER JOIN (SELECT' . CIOINA_Util::backquote('request_ip_address') . ' AS ip_address '  .', COUNT('. CIOINA_Util::backquote('request_ip_address') . ') AS ip_count FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) . '.' . CIOINA_Util::backquote('web_statistics') . ' AS T2'
            . ' WHERE ' . CIOINA_Util::backquote('T2') . '.' . CIOINA_Util::backquote('request_session') .' IS  NULL AND '.CIOINA_Util::backquote('T2') . '.' . CIOINA_Util::backquote('request_ip_address') . ' NOT IN '
            . ' (SELECT' . CIOINA_Util::backquote('request_ip_address') . ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) . '.' . CIOINA_Util::backquote('web_statistics') . ' AS T1'
            . ' WHERE ' . CIOINA_Util::backquote('request_session') . ' IS NOT NULL'
            . ' GROUP BY ' . CIOINA_Util::backquote('T1') . '.' . CIOINA_Util::backquote('request_ip_address') . ' )'
            . ' GROUP BY ' . CIOINA_Util::backquote('T2') . '.' . CIOINA_Util::backquote('request_ip_address') . ' )'
            . ' AS T3 ON '. CIOINA_Util::backquote('T4') . '.' . CIOINA_Util::backquote('request_ip_address') . '=' .  CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('ip_address')
            . ' AND '. CIOINA_Util::backquote('T3') . '.' . CIOINA_Util::backquote('ip_count') . '>' . CIOINA_Util::sqlAddSlashes($GLOBALS['CIOINA_Config']->get('TotalNullSessionPerDay'))
            . ' AND '. CIOINA_Util::backquote('T4') . '.' . CIOINA_Util::backquote('request_date') . '>= UTC_DATE )'
            . ' AS T5 ON '. CIOINA_Util::backquote('web_statistics') . '.' . CIOINA_Util::backquote('id') . '=' .  CIOINA_Util::backquote('T5') . '.' . CIOINA_Util::backquote('id')
            . ' SET '
            . CIOINA_Util::backquote('is_fake_visitor') . '= 1, '
            . CIOINA_Util::backquote('updated_at'). '='
            . '\'' . CIOINA_Util::sqlAddSlashes((new DateTime())->format('Y-m-d H:i:s')) . '\'';

            $GLOBALS['CIOINA_dbi']->tryQuery($query);

            $count = $GLOBALS['CIOINA_dbi']->fetchValue($countQuery);
            if($count == 0)
            {
                if ($today_before == $GLOBALS['CIOINA_Config']->get('TotalStatisticsRecordsPerDay') - 1 )
                {
                    $mgClient = new Mailgun($GLOBALS['CIOINA_Config']->get('MailgunKey'));
                    $domain = $GLOBALS['CIOINA_Config']->get('MailgunDomain');

                    $mgClient->sendMessage($domain, [
                    'from'    => 'Cioina website <postmaster@'.$domain.'>',
                    'to'      =>  'Alexei Cioina <' . $GLOBALS['CIOINA_Config']->get('MailgunRecipient') . '>',
                    'subject' => 'TotalStatisticsRecordsPerDay = '
                    . $GLOBALS['CIOINA_Config']->get('TotalStatisticsRecordsPerDay')
                    . ' last IP' . CIOINA_Util::getIP(),
                    'text'    => 'No more records allowed for web statistics']);
                }

                if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE)
                {
                    include_once './libraries/session.inc.acioina.php';
                }

            }
        }else{
            $query = ' UPDATE '
            . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
            . '.' . CIOINA_Util::backquote('web_statistics')
            . ' SET ' . CIOINA_Util::backquote('is_fake_visitor') . '= 1, '
            . CIOINA_Util::backquote('updated_at'). '='
            . '\'' . CIOINA_Util::sqlAddSlashes((new DateTime())->format('Y-m-d H:i:s')) . '\''
            . ' WHERE (' . CIOINA_Util::backquote('request_ip_address') . '='
            . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\''
            . ' AND ' . CIOINA_Util::backquote('request_date') . '<= STR_TO_DATE('
            . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d H:i:s')) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')'
            . ' AND ' . CIOINA_Util::backquote('request_date') . '>= STR_TO_DATE('
            . '\'' . CIOINA_Util::sqlAddSlashes(gmdate('Y-m-d')) . '\','
            . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d') .  '\')'
            . ')';
            $GLOBALS['CIOINA_dbi']->tryQuery($query);
        }
    }
}

/**
 * If we reach TotalStatisticsRecordsPerDay or TotalSessionsPerIpAddress ( is equivalent with is_fake_visitor = 1, )
 * we show DownForMaintenanceMessage.
 * The normal visitor can access the website next day while the fake visitor cannot.
 * We clear the statistics automatically every PeriodToUpdateFacebookImagesInDays
 * If DownForMaintenance = true, we show DownForMaintenanceMessage immediately and we do not use the logic from the above.
 * This is useful when we update the database.
 * However, the administrator will pass thought DownForMaintenance = true with the above logic in place.
 */
if (php_sapi_name() !== 'cli' &&
    ( !isset($_SESSION) || (session_status() !== PHP_SESSION_ACTIVE)))
{
    if(/*overload*/mb_strpos($_SERVER['REQUEST_URI'], $GLOBALS['CIOINA_Config']->get('LaravelAdminUri')) !== false &&
        $GLOBALS['CIOINA_Config']->get('ExpendableServiceProviderEnabled') )
    {
        if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE)
        {
            include_once './libraries/session.inc.acioina.php';
        }
    }
    else{
        CIOINA_setHeaderStatusCode(503);
        CIOINA_jsonMessage($GLOBALS['CIOINA_Config']->get('DownForMaintenanceMessage'), false);
    }
}

$now = DateTime::createFromFormat('Y-m-d', (new DateTime())->format('Y-m-d'));
$days_ago = new \DateInterval( 'P' . $GLOBALS['CIOINA_Config']->get('PeriodToUpdateFacebookImagesInDays') . 'D' );
$days_ago->invert = 1; //Make it negative.
$now->add( $days_ago );

$query = 'SELECT COUNT('. CIOINA_Util::backquote('id'). ') FROM '
   . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
   . '.' . CIOINA_Util::backquote('sent_emails')
   . ' WHERE ' . CIOINA_Util::backquote('created_at'). '>= STR_TO_DATE('
   . '\'' . CIOINA_Util::sqlAddSlashes($now->format('Y-m-d')) . '\','
   . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d') .  '\')'
   . ' AND ' . CIOINA_Util::backquote('email_type') . '='
   . '\'' . CIOINA_Util::sqlAddSlashes(7) . '\'';
$today_ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

if ($today_ids == 0)
{
    $now = (new DateTime())->format('Y-m-d H:i:s');
    $query = 'INSERT INTO '
    . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
    . '.' . CIOINA_Util::backquote('sent_emails')
    . ' (sent_to_email, email_type, request_session, updated_at, created_at, request_ip_address) '
    . ' VALUES('
    . '\'' . CIOINA_Util::sqlAddSlashes($GLOBALS['CIOINA_Config']->get('MailgunRecipient')) . '\','
    . '\'' . CIOINA_Util::sqlAddSlashes(7) . '\','
    . ((!isset($_COOKIE['PHPSESSID']))?'NULL,':'\''. CIOINA_Util::sqlAddSlashes($_COOKIE['PHPSESSID']) . '\',')
    . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
    . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
    . '\'' . CIOINA_Util::sqlAddSlashes(CIOINA_Util::getIP()) . '\')';
    $GLOBALS['CIOINA_dbi']->tryQuery($query);

    $query = 'DELETE FROM '
    . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
    . '.' . CIOINA_Util::backquote('web_statistics');
    $GLOBALS['CIOINA_dbi']->tryQuery($query);

    $sessionTime = $GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes') + 2;
    $now = \DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime())->format('Y-m-d H:i:s'));
    $minutesAgo = new \DateInterval( 'PT' . $sessionTime . 'M' );
    $minutesAgo->invert = 1; //Make it negative.
    $now->add( $minutesAgo );

    $query = 'DELETE FROM '
    . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
    . '.' . CIOINA_Util::backquote('online_clients')
    . ' WHERE ' . CIOINA_Util::backquote('updated_at') . '< STR_TO_DATE('
    . '\'' . CIOINA_Util::sqlAddSlashes($now->format('Y-m-d H:i:s')) . '\','
    . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';
    $GLOBALS['CIOINA_dbi']->tryQuery($query);

    //CIOINA_Util::deleteTempFiles($GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir') . DIRECTORY_SEPARATOR . 'framework'. DIRECTORY_SEPARATOR .'views');
    //CIOINA_Util::deleteTempFiles($GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir') . DIRECTORY_SEPARATOR . 'framework'. DIRECTORY_SEPARATOR . 'sessions');
    //CIOINA_Util::deleteTempFiles($GLOBALS['CIOINA_Config']->get('PhpSessionsTemp'), true);

    if (! defined('TESTSUITE'))
    {
        $query = 'SELECT '
         . CIOINA_Util::backquote('code_block')
         . ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
         . '.' . CIOINA_Util::backquote('settings')
         . ' WHERE ' . CIOINA_Util::backquote('id') . '= 55';
        $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

        if (isset($records) && count($records) === 1){
            $json = json_decode('{' . $records[0] . '}');
        }


        //$query = 'SELECT '
        //. CIOINA_Util::backquote('fb_email') .','
        //. CIOINA_Util::backquote('fb_token') .','
        //. CIOINA_Util::backquote('fb_id')
        //. ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        //. '.' . CIOINA_Util::backquote('clients')
        //. ' WHERE ' . CIOINA_Util::backquote('id') . '= 1';
        //$fb_user = $GLOBALS['CIOINA_dbi']->fetchResult($query);


        //        // We cannot have LongLive $fb_user[0]['fb_token'], so we need to have a recent Log In with Facebook.
        //        if (isset($fb_user) && count($fb_user) === 1
        //            && isset($json) && isset($json->data)
        //            && isset($json->data->IsFacebookEnabled) && $json->data->IsFacebookEnabled === true
        //            )
        //        {
        //            $query = 'SELECT '
        //            . CIOINA_Util::backquote('id') .','
        //            . CIOINA_Util::backquote('album_id') .','
        //            . CIOINA_Util::backquote('photo_id')
        //            . ' FROM '
        //            . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        //            . '.' . CIOINA_Util::backquote('facebook_images');
        //            $records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

        //            try {
        //                $accessToken = new AccessToken($fb_user[0]['fb_token']);
        //                if (!$accessToken instanceof AccessToken) {
        //                    $accessToken = null;
        //                }
        //                elseif ($accessToken->isExpired())
        //                {
        //                    $accessToken = null;
        //                }
        //            }
        //            catch(FacebookSDKException $e) {
        //                $accessToken = null;
        //                CIOINA_fatalError('Facebook Authentication AccessToken is unknown.');
        //            }

        //            if ( !isset($accessToken ) ) {
        //                CIOINA_fatalError('Facebook Authentication AccessToken is expired');
        //            }

        //            try {
        //                $fb = new Facebook([
        //                 'app_id'     => $GLOBALS['CIOINA_Config']->get('FacebookAppId'),
        //                 'app_secret' => $GLOBALS['CIOINA_Config']->get('FacebookAppSecret'),
        //                 'default_graph_version' => $GLOBALS['CIOINA_Config']->get('FacebookGraphVersion')]);

        //                $fb->setDefaultAccessToken($accessToken);

        //                $response = $fb->get('/' . $fb_user[0]['fb_id'] . '/albums');

        //                $albums = $response->getGraphEdge();
        //                while(isset($albums))
        //                {
        //                    foreach ($albums as $album)
        //                    {
        //                        $response = $fb->get('/'.$album['id'].'/photos?fields=picture');
        //                        $photos = $response->getGraphEdge();
        //                        while(isset($photos))
        //                        {
        //                            foreach ($photos as $photo)
        //                            {
        //                                $response = $fb->get('/'.$photo['id'].'?fields=images');
        //                                $images = $response->getGraphObject();
        //                                foreach ($images as $image)
        //                                {
        //                                    $foundOne = false;
        //                                    $now = (new DateTime())->format('Y-m-d H:i:s');

        //                                    foreach($records as $record)
        //                                    {
        //                                        if($record['album_id'] == $album['id'] && $record['photo_id'] == $photo['id'])
        //                                        {
        //                                            $query = ' UPDATE '
        //                                            . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        //                                            . '.' . CIOINA_Util::backquote('facebook_images')
        //                                            . ' SET '
        //                                            . CIOINA_Util::backquote('small_image_url'). '='
        //                                            . '\'' . CIOINA_Util::sqlAddSlashes($photo['picture']) . '\','

        //                                            . CIOINA_Util::backquote('original_image_url'). '='
        //                                            . '\'' . CIOINA_Util::sqlAddSlashes($image[0]['source']) . '\','

        //                                            . CIOINA_Util::backquote('updated_at'). '='
        //                                            . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\''

        //                                            . ' WHERE (' . CIOINA_Util::backquote('id') . '='
        //                                            . '\'' . CIOINA_Util::sqlAddSlashes($record['id']) . '\''
        //                                            . ')';
        //                                            $GLOBALS['CIOINA_dbi']->tryQuery($query);

        //                                            $foundOne = true;
        //                                            break;
        //                                        }
        //                                    }

        //                                    if(! $foundOne)
        //                                    {
        //                                        $query = 'INSERT INTO '
        //                                        . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
        //                                        . '.' . CIOINA_Util::backquote('facebook_images')
        //                                        . ' (album_id, photo_id, small_image_url, original_image_url, updated_at, created_at, status) '
        //                                        . ' VALUES('
        //                                        . '\'' . CIOINA_Util::sqlAddSlashes($album['id']) . '\','
        //                                        . '\'' . CIOINA_Util::sqlAddSlashes($photo['id']) . '\','
        //                                        . '\'' . CIOINA_Util::sqlAddSlashes($photo['picture']) . '\','
        //                                        . '\'' . CIOINA_Util::sqlAddSlashes($image[0]['source']) . '\','

        //                                        . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
        //                                        . '\'' . CIOINA_Util::sqlAddSlashes($now) . '\','
        //                                        . '\'' . CIOINA_Util::sqlAddSlashes(1) . '\')';
        //                                        $GLOBALS['CIOINA_dbi']->tryQuery($query);
        //                                    }

        //                                    break;
        //                                }
        //                            }
        //                            $photos = $fb->next($photos);
        //                        }
        //                    }
        //                    $albums = $fb->next($albums);
        //                }

        //                $isFacebookEnabled = true;
        //            }
        //            catch(FacebookResponseException $e) {
        //                CIOINA_fatalError('Graph returned an error while updating images.');
        //            }
        //            catch(FacebookSDKException $e) {
        //                CIOINA_fatalError('Facebook SDK returned an error while updating images.');
        //            }

        //        }

        if ( isset($json) && isset($json->data)
            && isset($json->data->IsSendEmailEnabled) && $json->data->IsSendEmailEnabled === true
            )
        {
            $mgClient = new Mailgun($GLOBALS['CIOINA_Config']->get('MailgunKey'));
            $domain = $GLOBALS['CIOINA_Config']->get('MailgunDomain');

            $mgClient->sendMessage($domain,
            [
                'from'    => 'Cioina Blog <postmaster@'.$domain.'>',
                'to'      =>  'Alexei Cioina <' . $GLOBALS['CIOINA_Config']->get('MailgunRecipient') . '>',
                'subject' => 'Deleted Webstatistics' ,
                'text'    => 'IsFacebookEnabled = ' . (isset($isFacebookEnabled) ? $isFacebookEnabled : false),
            ]);
        }
    }
}
unset($url, $query, $today_ids, $now, $days_ago, $mgClient, $domain, $isFacebookEnabled);
?>
