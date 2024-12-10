<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * session library
 *
 * @package PhpMyAdmin
 */

/**
 * tries to secure session from hijacking and fixation
 * should be called before login and after successful login
 * (only required if sensitive information stored in session)
 *
 * @return void
 */
function CIOINA_secureSession()
{
    // prevent session fixation and XSS
    if (session_status() === PHP_SESSION_ACTIVE && ! defined('TESTSUITE')) {
        session_regenerate_id(true);
    }
    CIOINA_generateToken();
}


/**
 * Generates CIOINA_token_token session variable.
 * Token which is used for authenticating access queries.
 * (we use "space CIOINA_token space" to prevent overwriting)
 * 
 * @return void
 */
function CIOINA_generateToken()
{
    if (class_exists('phpseclib\Crypt\Random')) {
        $_SESSION[' CIOINA_token '] = bin2hex(phpseclib\Crypt\Random::string(16));
    } else {
        $_SESSION[' CIOINA_token '] = bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * Check if token is properly generated (the genration can fail, for example
     * due to missing /dev/random for openssl).
     */
    if (empty($_SESSION[' CIOINA_token '])) {
        CIOINA_fatalError(
            'Failed to generate random CSRF token!'
        );
    }
}