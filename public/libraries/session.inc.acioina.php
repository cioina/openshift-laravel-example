<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * session handling
 *
 * @todo    add an option to use mm-module for session handler
 *
 * @package PhpMyAdmin
 * @see     https://secure.php.net/session
 */
if (! defined('ACIOINA')) {
    exit;
}

if (! function_exists('openssl_random_pseudo_bytes')) {
    require_once './libraries/phpseclib/Crypt/Random.php';
}

require_once 'libraries/session.lib.acioina.php';

// verify if PHP supports session, die if it does not

if (!@function_exists('session_name')) {
    CIOINA_warnMissingExtension('session', true);
} elseif (! empty(ini_get('session.auto_start')) && session_name() != 'PHPSESSID' && !empty(session_id())) {
    // Do not delete the existing non empty session, it might be used by other
    // applications; instead just close it.
    if (empty($_SESSION)) {
        /* Ignore errors as this might have been destroyed in other request meanwhile */
        @session_destroy();
    } elseif (function_exists('session_abort')) {
        /* PHP 5.6 and newer */
        session_abort();
    } else {
        session_write_close();
    }
}

// disable starting of sessions before all settings are done
// does not work, besides how it is written in php manual
//ini_set('session.auto_start', '0');

// session cookie settings
session_set_cookie_params(
    0, $GLOBALS['CIOINA_Config']->getRootPath(),
    '', $GLOBALS['CIOINA_Config']->isHttps(), true
);

// cookies are safer (use @ini_set() in case this function is disabled)
@ini_set('session.use_cookies', 'true');

// optionally set session_save_path
$path = $GLOBALS['CIOINA_Config']->get('SessionSavePath');
if (!empty($path)) {
    session_save_path($path);
}

// use cookies only
@ini_set('session.use_only_cookies', '1');
// strict session mode (do not accept random string as session ID)
@ini_set('session.use_strict_mode', '1');
// make the session cookie HttpOnly
@ini_set('session.cookie_httponly', '1');
// do not force transparent session ids
@ini_set('session.use_trans_sid', '0');

// delete session/cookies when browser is closed
@ini_set('session.cookie_lifetime', '0');

// warn but don't work with bug
@ini_set('session.bug_compat_42', 'false');
@ini_set('session.bug_compat_warn', 'true');

// use more secure session ids
@ini_set('session.hash_function', '1');

// some pages (e.g. stylesheet) may be cached on clients, but not in shared
// proxy servers
session_cache_limiter('private');

// start the session
// on some servers (for example, sourceforge.net), we get a permission error
// on the session data directory, so I add some "@"


function CIOINA_sessionFailed($errors)
{
    $messages = array();
    foreach ($errors as $error) {
        /*
         * Remove path from open() in error message to avoid path disclossure
         *
         * This can happen with PHP 5 when nonexisting session ID is provided,
         * since PHP 7, session existence is checked first.
         *
         * This error can also happen in case of session backed error (eg.
         * read only filesystem) on any PHP version.
         *
         * The message string is currently hardcoded in PHP, so hopefully it
         * will not change in future.
         */
        $messages[] = preg_replace(
            '/open\(.*, O_RDWR\)/',
            'open(SESSION_FILE, O_RDWR)',
            htmlspecialchars($error->getMessage())
        );
    }

    /*
     * Session initialization is done before selecting language, so we
     * can not use translations here.
     */
    CIOINA_fatalError(
        'Error during session start; please check your PHP and/or '
        . 'webserver log file and configure your PHP '
        . 'installation properly. Also ensure that cookies are enabled '
        . 'in your browser.'
        . '<br /><br />'
        . implode('<br /><br />', $messages)
    );
}

// See bug #1538132. This would block normal behavior on a cluster
//ini_set('session.save_handler', 'files');

// It looks like Facebook needs this name.
$session_name = 'PHPSESSID';
@session_name($session_name);

if (php_sapi_name() === 'cli')
{
    session_start();
}else{
    // Restore correct sesion ID (it might have been reset by auto started session
    if (isset($_COOKIE[$session_name])) {
        session_id($_COOKIE[$session_name]);
    }

    // on first start of session we check for errors
    // f.e. session dir cannot be accessed - session file not created
    $orig_error_count = $GLOBALS['CIOINA_error_handler']->countErrors(false);

    $session_result = session_start();

    if ($session_result !== true
        || $orig_error_count != $GLOBALS['CIOINA_error_handler']->countErrors(false)
    ) {
        setcookie($session_name, '', 1);
        $errors = $GLOBALS['CIOINA_error_handler']->sliceErrors($orig_error_count);
        CIOINA_sessionFailed($errors);
    }
    unset($orig_error_count, $session_result);

    /**
     * Disable setting of session cookies for further session_start() calls.
     */
    @ini_set('session.use_cookies', 'true');

    /**
     * Token which is used for authenticating access queries.
     * (we use "space CIOINA_token space" to prevent overwriting)
     */
    if (empty($_SESSION[' CIOINA_token '])) 
    {
        CIOINA_generateToken();

        /**
         * Check for disk space on session storage by trying to write it.
         *
         * This seems to be most reliable approach to test if sessions are working,
         * otherwise the check would fail with custom session backends.
         */
        $orig_error_count = $GLOBALS['CIOINA_error_handler']->countErrors();
        session_write_close();
        if ($GLOBALS['CIOINA_error_handler']->countErrors() > $orig_error_count) {
            $errors = $GLOBALS['CIOINA_error_handler']->sliceErrors($orig_error_count);
            CIOINA_sessionFailed($errors);
        }
        session_start();
        if (empty($_SESSION[' CIOINA_token '])) {
            CIOINA_fatalError(
                'Failed to store CSRF token in session! ' .
                'Probably sessions are not working properly.'
            );
        }else{
            $_SESSION['use_cookies'] = true;
        }
    }
}


