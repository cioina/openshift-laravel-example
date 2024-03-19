<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/** String Functions for ACIOINA
 *
 * If mb_* functions don't exist, we create the ones we need and they'll use the
 * standard string functions.
 *
 * All mb_* functions created by pMA should behave as mb_* functions.
 *
 * @package ACIOINA
 */
if (! defined('ACIOINA')) {
    exit;
}

if (!defined('MULTIBYTES_ON')) {
    define('MULTIBYTES_ON', true);
    define('MULTIBYTES_OFF', false);
}

if (@function_exists('mb_strlen')) {
    if (!defined('MULTIBYTES_STATUS')) {
        define('MULTIBYTES_STATUS', MULTIBYTES_ON);
    }

    include_once 'libraries/stringMb.lib.acioina.php';
} else {
    if (!defined('MULTIBYTES_STATUS')) {
        define('MULTIBYTES_STATUS', MULTIBYTES_OFF);
    }

    include_once 'libraries/stringNative.lib.acioina.php';
}
