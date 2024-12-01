<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Configuration handling.
 *
 * @package ACIOINA
 */

if (! defined('ACIOINA')) {
    exit;
}


/**
 * Indication for error handler (see end of this file).
 */
$GLOBALS['CIOINA_config_loading'] = false;

/**
 * Configuration class
 *
 * @package ACIOINA
 */
class CIOINA_Config
{
    /**
     * @var string  default config source
     */
    var $default_source = './libraries/myconfig.acioina.php';

    /**
     * @var array   default configuration settings
     */
    var $default = array();

    /**
     * @var array   configuration settings, without user preferences applied
     */
    var $base_settings = array();

    /**
     * @var array   configuration settings
     */
    var $settings = array();

    /**
     * @var string  config source
     */
    var $source = '';

    /**
     * @var int     source modification time
     */
    var $source_mtime = 0;
    var $default_source_mtime = 0;
    var $set_mtime = 0;

    /**
     * @var boolean
     */
    var $error_config_file = false;

    /**
     * @var boolean
     */
    var $error_config_default_file = false;

    /**
     * @var boolean
     */
    var $error_CIOINA_uri = false;

    /**
     * @var array
     */
    var $default_server = array();

    /**
     * @var boolean whether init is done or not
     * set this to false to force some initial checks
     * like checking for required functions
     */
    var $done = false;

    /**
     * constructor
     *
     * @param string $source source to read config from
     */
    function __construct($source = null)
    {
        $this->settings = array();

        // functions need to refresh in case of config file changed goes in
        $this->load($source);

        // other settings, independent from config file, comes in
        $this->checkSystem();

        $this->isHttps();

        $this->base_settings = $this->settings;
    }

    /**
     * sets system and application settings
     *
     * @return void
     */
    function checkSystem()
    {
        $this->checkPhpVersion();
        $this->checkWebServerOs();
        $this->checkWebServer();
        $this->checkGd2();
        $this->checkClient();
        $this->checkUpload();
        $this->checkUploadSize();
        $this->checkOutputCompression();
    }

    /**
     * whether to use gzip output compression or not
     *
     * @return void
     */
    function checkOutputCompression()
    {
        // If zlib output compression is set in the php configuration file, no
        // output buffering should be run
        if (@ini_get('zlib.output_compression')) {
            $this->set('OBGzip', false);
        }

        // disable output-buffering (if set to 'auto') for IE6, else enable it.
        if (strtolower($this->get('OBGzip')) == 'auto') {
            if ($this->get('CIOINA_USR_BROWSER_AGENT') == 'IE'
                && $this->get('CIOINA_USR_BROWSER_VER') >= 6
                && $this->get('CIOINA_USR_BROWSER_VER') < 7
            ) {
                $this->set('OBGzip', false);
            } else {
                $this->set('OBGzip', true);
            }
        }
    }

    /**
     * Determines platform (OS), browser and version of the user
     * Based on a phpBuilder article:
     *
     * @see http://www.acioina.phpbuilder.net/columns/tim20000821.acioina.php
     *
     * @return void
     */
    function checkClient()
    {
        if (CIOINA_getenv('HTTP_USER_AGENT')) {
            $HTTP_USER_AGENT = CIOINA_getenv('HTTP_USER_AGENT');
        } else {
            $HTTP_USER_AGENT = '';
        }

        // 1. Platform
        if (/*overload*/mb_strstr($HTTP_USER_AGENT, 'Win')) {
            $this->set('CIOINA_USR_OS', 'Win');
        } elseif (/*overload*/mb_strstr($HTTP_USER_AGENT, 'Mac')) {
            $this->set('CIOINA_USR_OS', 'Mac');
        } elseif (/*overload*/mb_strstr($HTTP_USER_AGENT, 'Linux')) {
            $this->set('CIOINA_USR_OS', 'Linux');
        } elseif (/*overload*/mb_strstr($HTTP_USER_AGENT, 'Unix')) {
            $this->set('CIOINA_USR_OS', 'Unix');
        } elseif (/*overload*/mb_strstr($HTTP_USER_AGENT, 'OS/2')) {
            $this->set('CIOINA_USR_OS', 'OS/2');
        } else {
            $this->set('CIOINA_USR_OS', 'Other');
        }

        // 2. browser and version
        // (must check everything else before Mozilla)

        $is_mozilla = preg_match(
            '@Mozilla/([0-9].[0-9]{1,2})@',
            $HTTP_USER_AGENT,
            $mozilla_version
        );

        if (preg_match(
            '@Opera(/| )([0-9].[0-9]{1,2})@',
            $HTTP_USER_AGENT,
            $log_version
        )) {
            $this->set('CIOINA_USR_BROWSER_VER', $log_version[2]);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'OPERA');
        } elseif (preg_match(
            '@(MS)?IE ([0-9]{1,2}.[0-9]{1,2})@',
            $HTTP_USER_AGENT,
            $log_version
        )) {
            $this->set('CIOINA_USR_BROWSER_VER', $log_version[2]);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'IE');
        } elseif (preg_match(
            '@Trident/(7)\.0@',
            $HTTP_USER_AGENT,
            $log_version
        )) {
            $this->set('CIOINA_USR_BROWSER_VER', intval($log_version[1]) + 4);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'IE');
        } elseif (preg_match(
            '@OmniWeb/([0-9].[0-9]{1,2})@',
            $HTTP_USER_AGENT,
            $log_version
        )) {
            $this->set('CIOINA_USR_BROWSER_VER', $log_version[1]);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'OMNIWEB');
            // Konqueror 2.2.2 says Konqueror/2.2.2
            // Konqueror 3.0.3 says Konqueror/3
        } elseif (preg_match(
            '@(Konqueror/)(.*)(;)@',
            $HTTP_USER_AGENT,
            $log_version
        )) {
            $this->set('CIOINA_USR_BROWSER_VER', $log_version[2]);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'KONQUEROR');
            // must check Chrome before Safari
        } elseif ($is_mozilla
            && preg_match('@Chrome/([0-9.]*)@', $HTTP_USER_AGENT, $log_version)
        ) {
            $this->set('CIOINA_USR_BROWSER_VER', $log_version[1]);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'CHROME');
            // newer Safari
        } elseif ($is_mozilla
            && preg_match('@Version/(.*) Safari@', $HTTP_USER_AGENT, $log_version)
        ) {
            $this->set(
                'CIOINA_USR_BROWSER_VER', $log_version[1]
            );
            $this->set('CIOINA_USR_BROWSER_AGENT', 'SAFARI');
            // older Safari
        } elseif ($is_mozilla
            && preg_match('@Safari/([0-9]*)@', $HTTP_USER_AGENT, $log_version)
        ) {
            $this->set(
                'CIOINA_USR_BROWSER_VER', $mozilla_version[1] . '.' . $log_version[1]
            );
            $this->set('CIOINA_USR_BROWSER_AGENT', 'SAFARI');
            // Firefox
        } elseif (! /*overload*/mb_strstr($HTTP_USER_AGENT, 'compatible')
            && preg_match('@Firefox/([\w.]+)@', $HTTP_USER_AGENT, $log_version)
        ) {
            $this->set(
                'CIOINA_USR_BROWSER_VER', $log_version[1]
            );
            $this->set('CIOINA_USR_BROWSER_AGENT', 'FIREFOX');
        } elseif (preg_match('@rv:1.9(.*)Gecko@', $HTTP_USER_AGENT)) {
            $this->set('CIOINA_USR_BROWSER_VER', '1.9');
            $this->set('CIOINA_USR_BROWSER_AGENT', 'GECKO');
        } elseif ($is_mozilla) {
            $this->set('CIOINA_USR_BROWSER_VER', $mozilla_version[1]);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'MOZILLA');
        } else {
            $this->set('CIOINA_USR_BROWSER_VER', 0);
            $this->set('CIOINA_USR_BROWSER_AGENT', 'OTHER');
        }
    }

    /**
     * Whether GD2 is present
     *
     * @return void
     */
    function checkGd2()
    {
        if ($this->get('GD2Available') == 'yes') {
            $this->set('CIOINA_IS_GD2', 1);
            return;
        }

        if ($this->get('GD2Available') == 'no') {
            $this->set('CIOINA_IS_GD2', 0);
            return;
        }

        if (!@function_exists('imagecreatetruecolor')) {
            $this->set('CIOINA_IS_GD2', 0);
            return;
        }

        if (@function_exists('gd_info')) {
            $gd_nfo = gd_info();
            if (/*overload*/mb_strstr($gd_nfo["GD Version"], '2.')) {
                $this->set('CIOINA_IS_GD2', 1);
            } else {
                $this->set('CIOINA_IS_GD2', 0);
            }
        } else {
            $this->set('CIOINA_IS_GD2', 0);
        }
    }

    /**
     * Whether the Web server php is running on is IIS
     *
     * @return void
     */
    function checkWebServer()
    {
        // some versions return Microsoft-IIS, some Microsoft/IIS
        // we could use a preg_match() but it's slower
        if (CIOINA_getenv('SERVER_SOFTWARE')
            && stristr(CIOINA_getenv('SERVER_SOFTWARE'), 'Microsoft')
            && stristr(CIOINA_getenv('SERVER_SOFTWARE'), 'IIS')
        ) {
            $this->set('CIOINA_IS_IIS', 1);
        } else {
            $this->set('CIOINA_IS_IIS', 0);
        }
    }

    /**
     * Whether the os php is running on is windows or not
     *
     * @return void
     */
    function checkWebServerOs()
    {
        // Default to Unix or Equiv
        $this->set('CIOINA_IS_WINDOWS', 0);
        // If PHP_OS is defined then continue
        if (defined('PHP_OS')) {
            if (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) {
                // Is it some version of Windows
                $this->set('CIOINA_IS_WINDOWS', 1);
            } elseif (stristr(PHP_OS, 'OS/2')) {
                // Is it OS/2 (No file permissions like Windows)
                $this->set('CIOINA_IS_WINDOWS', 1);
            }
        }
    }

    /**
     * detects PHP version
     *
     * @return void
     */
    function checkPhpVersion()
    {
        $match = array();
        if (! preg_match(
            '@([0-9]{1,2}).([0-9]{1,2}).([0-9]{1,2})@',
            phpversion(),
            $match
        )) {
            preg_match(
                '@([0-9]{1,2}).([0-9]{1,2})@',
                phpversion(),
                $match
            );
        }
        if (isset($match) && ! empty($match[1])) {
            if (! isset($match[2])) {
                $match[2] = 0;
            }
            if (! isset($match[3])) {
                $match[3] = 0;
            }
            $this->set(
                'CIOINA_PHP_INT_VERSION',
                (int) sprintf('%d%02d%02d', $match[1], $match[2], $match[3])
            );
        } else {
            $this->set('CIOINA_PHP_INT_VERSION', 0);
        }
        $this->set('CIOINA_PHP_STR_VERSION', phpversion());
    }


    /**
     * loads default values from default source
     *
     * @return boolean     success
     */
    function loadDefaults()
    {
        $cfg = array();
        if (! file_exists($this->default_source)) {
            $this->error_config_default_file = true;
            return false;
        }
        include $this->default_source;

        $this->default_source_mtime = filemtime($this->default_source);

        $this->default = $cfg;
        $this->settings = CIOINA_arrayMergeRecursive($this->settings, $cfg);

        $this->error_config_default_file = false;

        return true;
    }

    /**
     * loads configuration from $source, usually the config file
     * should be called on object creation
     *
     * @param string $source config file
     *
     * @return bool
     */
    function load($source = null)
    {
        //loads .php file
        $this->loadDefaults();
        // check .ini file
        if (null !== $source) {
            $this->source = trim($source);
        }

        if (! $this->checkConfigSource()) {
            // even if no config file, set collation_connection
            $this->checkCollationConnection();
            return false;
        }

        $cfg = array();
        $eval_result = true;

        /**
         * Parses the configuration file, we throw away any errors or
         * output.
         */
        //$old_error_reporting = error_reporting(0);
        //ob_start();
        //$GLOBALS['CIOINA_config_loading'] = true;
        //try {
        //    if (!$settings = parse_ini_file( $this->getSource(), true)) 
        //    {
        //        throw new exception('Unable to open .ini'); 
        //    }

        //    $cfg['LaravelWebUser'] =     $settings['laravel']['web_user'];
        //    $cfg['LaravelWebPassword'] = $settings['laravel']['web_password'];
        //    $cfg['LaravelAdminUri'] = $settings['laravel']['login'];

        //    $cfg['MailgunKey'] =    $settings['mailgun']['key'];
        //    $cfg['MailgunDomain'] = $settings['mailgun']['domain'];
        //    $cfg['MailgunRecipient'] = $settings['mailgun']['recipient'];

          
        //    $cfg['SSLPort'] =       $settings['external_website']['ssl_port'];
        //    $cfg['MyBlogUrl'] =     $settings['external_website']['blog_url'];
        //    $cfg['HtmlRedirect'] =  $settings['external_website']['redirect_page'];
           
        //    $cfg['MySqlHost'] =     $settings['mysql']['host'];
        //    $cfg['MySqlPort'] =     $settings['mysql']['port'];
        //    $cfg['MySqlUser'] =     $settings['mysql']['user'];
        //    $cfg['MySqlPWord'] =    $settings['mysql']['pword'];
        //    $cfg['MySqlDatabase'] = $settings['mysql']['database'];
        //}
        //catch ( Exception $ex ) {
        //    $eval_result = false;
        //}
        //$GLOBALS['CIOINA_config_loading'] = false;
        //ob_end_clean();
        //error_reporting($old_error_reporting);

        if ($eval_result === false) {
            $this->error_config_file = true;
        } else {
            $this->error_config_file = false;
            $this->source_mtime = filemtime($this->getSource());
        }

        $this->settings = CIOINA_arrayMergeRecursive($this->settings, $cfg);
        $this->checkPmaAbsoluteUri();

        // Handling of the collation must be done after merging of $cfg
        // (from config.inc.acioina.php) so that $cfg['DefaultConnectionCollation']
        // can have an effect.
        $this->checkCollationConnection();

        return true;
    }


    /**
     * check config source
     *
     * @return boolean whether source is valid or not
     */
    function checkConfigSource()
    {
        if (! $this->getSource()) {
            // no configuration file set at all
            return false;
        }

        if (! file_exists($this->getSource())) {
            $this->source_mtime = 0;
            return false;
        }

        if (! is_readable($this->getSource())) {
            // manually check if file is readable
            // might be bug #3059806 Supporting running from CIFS/Samba shares

            $contents = false;
            $handle = @fopen($this->getSource(), 'r');
            if ($handle !== false) {
                $contents = @fread($handle, 1); // reading 1 byte is enough to test
                @fclose($handle);
            }
            if ($contents === false) {
                $this->source_mtime = 0;
                CIOINA_fatalError(
                    sprintf(
                        function_exists('__')
                        ? __('Existing configuration file (%s) is not readable.')
                        : 'Existing configuration file (%s) is not readable.',
                        $this->getSource()
                    )
                );
                return false;
            }
        }

        return true;
    }

    /**
     * verifies the permissions on config file (if asked by configuration)
     * (must be called after config.inc.acioina.php has been merged)
     *
     * @return void
     */
    function checkPermissions()
    {
        // Check for permissions (on platforms that support it):
        if ($this->get('CheckConfigurationPermissions')) {
            $perms = @fileperms($this->getSource());
            if (!($perms === false) && ($perms & 2)) {
                // This check is normally done after loading configuration
                $this->checkWebServerOs();
                if ($this->get('CIOINA_IS_WINDOWS') == 0) {
                    $this->source_mtime = 0;
                    CIOINA_fatalError(
                        __(
                            'Wrong permissions on configuration file, '
                            . 'should not be world writable!'
                        )
                    );
                }
            }
        }
    }

    /**
     * returns specific config setting
     *
     * @param string $setting config setting
     *
     * @return mixed value
     */
    function get($setting)
    {
        if (isset($this->settings[$setting])) {
            return $this->settings[$setting];
        }
        return null;
    }

    /**
     * sets configuration variable
     *
     * @param string $setting configuration option
     * @param mixed  $value   new value for configuration option
     *
     * @return void
     */
    function set($setting, $value)
    {
        if (! isset($this->settings[$setting])
            || $this->settings[$setting] !== $value
        ) {
            $this->settings[$setting] = $value;
            $this->set_mtime = time();
        }
    }

    /**
     * returns source for current config
     *
     * @return string  config source
     */
    function getSource()
    {
        return $this->source;
    }

    /**
     * returns a unique value to force a CSS reload if either the config
     * or the theme changes
     * must also check the CIOINA_fontsize cookie in case there is no
     * config file
     *
     * @return int Summary of unix timestamps and fontsize,
     * to be unique on theme parameters change
     */
    function getThemeUniqueValue()
    {
        if (null !== $this->get('fontsize')) {
            $fontsize = intval($this->get('fontsize'));
        } elseif (isset($_COOKIE['CIOINA_fontsize'])) {
            $fontsize = intval($_COOKIE['CIOINA_fontsize']);
        } else {
            $fontsize = 0;
        }
        return (
            $fontsize +
            $this->source_mtime +
            $this->default_source_mtime +
            $this->get('user_preferences_mtime') +
            $_SESSION['CIOINA_Theme']->mtime_info +
            $_SESSION['CIOINA_Theme']->filesize_info);
    }

    /**
     * $cfg['PmaAbsoluteUri'] is a required directive else cookies won't be
     * set properly and, depending on browsers, inserting or updating a
     * record might fail
     *
     * @return void
     */
    function checkPmaAbsoluteUri()
    {
        // Setup a default value to let the people and lazy sysadmins work anyway,
        // they'll get an error if the autodetect code doesn't work
        $CIOINA_absolute_uri = $this->get('PmaAbsoluteUri');
        $is_https = $this->detectHttps();

        if (/*overload*/mb_strlen($CIOINA_absolute_uri) < 5) {
            $url = array();

            // If we don't have scheme, we didn't have full URL so we need to
            // dig deeper
            if (empty($url['scheme'])) {
                // Scheme
                if ($is_https) {
                    $url['scheme'] = 'https';
                } else {
                    $url['scheme'] = 'http';
                }

                // Host and port
                if (CIOINA_getenv('HTTP_HOST')) {
                    // Prepend the scheme before using parse_url() since this
                    // is not part of the RFC2616 Host request-header
                    $parsed_url = parse_url(
                        $url['scheme'] . '://' . CIOINA_getenv('HTTP_HOST')
                    );
                    if (!empty($parsed_url['host'])) {
                        $url = $parsed_url;
                    } else {
                        $url['host'] = CIOINA_getenv('HTTP_HOST');
                    }
                } elseif (CIOINA_getenv('SERVER_NAME')) {
                    $url['host'] = CIOINA_getenv('SERVER_NAME');
                } else {
                    $this->error_CIOINA_uri = true;
                    return;
                }

                // If we didn't set port yet...
                if (empty($url['port']) && CIOINA_getenv('SERVER_PORT')) {
                    $url['port'] = CIOINA_getenv('SERVER_PORT');
                }

                // And finally the path could be already set from REQUEST_URI
                if (empty($url['path'])) {
                    // we got a case with nginx + php-fpm where PHP_SELF
                    // was not set, so CIOINA_PHP_SELF was not set as well
                    if (isset($GLOBALS['CIOINA_PHP_SELF'])) {
                        $path = parse_url($GLOBALS['CIOINA_PHP_SELF']);
                    } else {
                        $path = parse_url(CIOINA_getenv('REQUEST_URI'));
                    }
                    $url['path'] = $path['path'];
                }
            }

            // Make url from parts we have
            $CIOINA_absolute_uri = $url['scheme'] . '://';
            // Was there user information?
            if (!empty($url['user'])) {
                $CIOINA_absolute_uri .= $url['user'];
                if (!empty($url['pass'])) {
                    $CIOINA_absolute_uri .= ':' . $url['pass'];
                }
                $CIOINA_absolute_uri .= '@';
            }
            // Add hostname
            $CIOINA_absolute_uri .= $url['host'];
            // Add port, if it not the default one
            if (! empty($url['port'])
                && (($url['scheme'] == 'http' && $url['port'] != 80)
                || ($url['scheme'] == 'https' && $url['port'] != 443))
            ) {
                $CIOINA_absolute_uri .= ':' . $url['port'];
            }
            // And finally path, without script name, the 'a' is there not to
            // strip our directory, when path is only /pmadir/ without filename.
            // Backslashes returned by Windows have to be changed.
            // Only replace backslashes by forward slashes if on Windows,
            // as the backslash could be valid on a non-Windows system.
            $this->checkWebServerOs();
            if ($this->get('CIOINA_IS_WINDOWS') == 1) {
                $path = str_replace("\\", "/", dirname($url['path'] . 'a'));
            } else {
                $path = dirname($url['path'] . 'a');
            }

            // To work correctly within javascript
            if (defined('CIOINA_PATH_TO_BASEDIR') && CIOINA_PATH_TO_BASEDIR == '../') {
                if ($this->get('CIOINA_IS_WINDOWS') == 1) {
                    $path = str_replace("\\", "/", dirname($path));
                } else {
                    $path = dirname($path);
                }
            }

            // PHP's dirname function would have returned a dot
            // when $path contains no slash
            if ($path == '.') {
                $path = '';
            }
            // in vhost situations, there could be already an ending slash
            if (/*overload*/mb_substr($path, -1) != '/') {
                $path .= '/';
            }
            $CIOINA_absolute_uri .= $path;

            // This is to handle the case of a reverse proxy
            if ($this->get('ForceSSL')) {
                $this->set('PmaAbsoluteUri', $CIOINA_absolute_uri);
                $CIOINA_absolute_uri = $this->getSSLUri();
                $this->isHttps();
            }

            // We used to display a warning if PmaAbsoluteUri wasn't set, but now
            // the autodetect code works well enough that we don't display the
            // warning at all. The user can still set PmaAbsoluteUri manually.

        } else {
            // The URI is specified, however users do often specify this
            // wrongly, so we try to fix this.

            // Adds a trailing slash et the end of the ACIOINA uri if it
            // does not exist.
            if (/*overload*/mb_substr($CIOINA_absolute_uri, -1) != '/') {
                $CIOINA_absolute_uri .= '/';
            }

            // If URI doesn't start with http:// or https://, we will add
            // this.
            if (/*overload*/mb_substr($CIOINA_absolute_uri, 0, 7) != 'http://'
                && /*overload*/mb_substr($CIOINA_absolute_uri, 0, 8) != 'https://'
            ) {
                $CIOINA_absolute_uri
                    = ($is_https ? 'https' : 'http')
                    . ':'
                    . (
                        /*overload*/mb_substr($CIOINA_absolute_uri, 0, 2) == '//'
                        ? ''
                        : '//'
                    )
                    . $CIOINA_absolute_uri;
            }
        }
        $this->set('PmaAbsoluteUri', $CIOINA_absolute_uri);
    }

    /**
     * Converts currently used PmaAbsoluteUri to SSL based variant.
     *
     * @return String witch adjusted URI
     */
    function getSSLUri()
    {
        // grab current URL
        $url = $this->get('PmaAbsoluteUri');
        // Parse current URL
        $parsed = parse_url($url);
        // In case parsing has failed do stupid string replacement
        if ($parsed === false) {
            // Replace http protocol
            return preg_replace('@^http:@', 'https:', $url);
        }

        // Reconstruct URL using parsed parts
        return 'https://' . $parsed['host'] . ':' . $this->get('SSLPort') . $parsed['path'];
    }

    /**
     * Sets collation_connection based on user preference. First is checked
     * value from request, then cookies with fallback to default.
     *
     * After setting it here, cookie is set in common.inc.acioina.php to persist
     * the selection.
     *
     * @todo check validity of collation string
     *
     * @return void
     */
    function checkCollationConnection()
    {
        // https://github.com/phpmyadmin/phpmyadmin/commit/ad7f7fd80192bd9f7f22f4d8d9a8818dd69f3e0c
        // (here, do not use $_REQUEST[] as it can be crafted)

        if (! empty($_POST['collation_connection'])) {
            $collation = strip_tags($_POST['collation_connection']);
        } elseif (! empty($_COOKIE['CIOINA_collation_connection'])) {
            $collation = strip_tags($_COOKIE['CIOINA_collation_connection']);
        } else {
            $collation = $this->get('DefaultConnectionCollation');
        }
        $this->set('collation_connection', $collation);
    }


    /**
     * checks if upload is enabled
     *
     * @return void
     */
    function checkUpload()
    {
        if (!ini_get('file_uploads')) {
            $this->set('enable_upload', false);
            return;
        }

        $this->set('enable_upload', true);
        // if set "php_admin_value file_uploads Off" in httpd.conf
        // ini_get() also returns the string "Off" in this case:
        if ('off' == strtolower(ini_get('file_uploads'))) {
            $this->set('enable_upload', false);
        }
    }

    /**
     * Maximum upload size as limited by PHP
     * Used with permission from Moodle (http://moodle.org) by Martin Dougiamas
     *
     * this section generates $max_upload_size in bytes
     *
     * @return void
     */
    function checkUploadSize()
    {
        if (! $filesize = ini_get('upload_max_filesize')) {
            $filesize = "5M";
        }

        if ($postsize = ini_get('post_max_size')) {
            $this->set(
                'max_upload_size',
                min(CIOINA_getRealSize($filesize), CIOINA_getRealSize($postsize))
            );
        } else {
            $this->set('max_upload_size', CIOINA_getRealSize($filesize));
        }
    }
    
    /**
     * removes cookie
     *
     * @param string $cookie name of cookie to remove
     *
     * @return boolean result of setcookie()
     */
    public function removeCookie($cookie)
    {
        if (defined('TESTSUITE')) {
            if (isset($_COOKIE[$cookie])) {
                unset($_COOKIE[$cookie]);
            }
            return true;
        }
        return setcookie(
            $cookie,
            '',
            time() - 3600,
            $this->getRootPath(),
            '',
            $this->isHttps()
        );
    }

    /**
     * sets cookie if value is different from current cookie value,
     * or removes if value is equal to default
     *
     * @param string $cookie   name of cookie to remove
     * @param mixed  $value    new cookie value
     * @param string $default  default value
     * @param int    $validity validity of cookie in seconds (default is one month)
     * @param bool   $httponly whether cookie is only for HTTP (and not for scripts)
     *
     * @return boolean result of setcookie()
     */
    public function setCookie($cookie, $value, $default = null,
        $validity = null, $httponly = true
    ) {
        if (strlen($value) > 0 && null !== $default && $value === $default
        ) {
            // default value is used
            if (isset($_COOKIE[$cookie])) {
                // remove cookie
                return $this->removeCookie($cookie);
            }
            return false;
        }

        if (strlen($value) === 0 && isset($_COOKIE[$cookie])) {
            // remove cookie, value is empty
            return $this->removeCookie($cookie);
        }

        if (! isset($_COOKIE[$cookie]) || $_COOKIE[$cookie] !== $value) {
            // set cookie with new value
            /* Calculate cookie validity */
            if ($validity === null) {
                /* Valid for one month */
                $validity = time() + 2592000;
            } elseif ($validity == 0) {
                /* Valid for session */
                $validity = 0;
            } else {
                $validity = time() + $validity;
            }
            if (defined('TESTSUITE')) {
                $_COOKIE[$cookie] = $value;
                return true;
            }
            return setcookie(
                $cookie,
                $value,
                $validity,
                $this->getRootPath(),
                '',
                $this->isHttps(),
                $httponly
            );
        }

        // cookie has already $value as value
        return true;
    }

    /**
     * Get phpMyAdmin root path
     *
     * @return string
     */
    public function getRootPath()
    {
        static $cookie_path = null;

        if (null !== $cookie_path && !defined('TESTSUITE')) {
            return $cookie_path;
        }

        $url = $this->get('PmaAbsoluteUri');

        if (! empty($url)) {
            $path = parse_url($url, PHP_URL_PATH);
            if (! empty($path)) {
                if (substr($path, -1) != '/') {
                    return $path . '/';
                }
                return $path;
            }
        }

        $parsed_url = parse_url($GLOBALS['CIOINA_PHP_SELF']);

        $parts = explode(
            '/',
            rtrim(str_replace('\\', '/', $parsed_url['path']), '/')
        );

        /* Remove filename */
        if (substr($parts[count($parts) - 1], -4) == '.php') {
            $parts = array_slice($parts, 0, count($parts) - 1);
        }

        $parts[] = '';

        return implode('/', $parts);
    }

    /**
     * Checks if protocol is https
     *
     * This function checks if the https protocol on the active connection.
     *
     * @return bool
     */
    public function isHttps()
    {
        if (null !== $this->get('is_https')) {
            return $this->get('is_https');
        }

        $url = $this->get('PmaAbsoluteUri');

        $is_https = false;
        if (! empty($url) && parse_url($url, PHP_URL_SCHEME) === 'https') {
            $is_https = true;
        } elseif (strtolower(CIOINA_getenv('HTTP_SCHEME')) == 'https') {
            $is_https = true;
        } elseif (strtolower(CIOINA_getenv('HTTPS')) == 'on') {
            $is_https = true;
        } elseif (substr(strtolower(CIOINA_getenv('REQUEST_URI')), 0, 6) == 'https:') {
            $is_https = true;
        } elseif (strtolower(CIOINA_getenv('HTTP_HTTPS_FROM_LB')) == 'on') {
            // A10 Networks load balancer
            $is_https = true;
        } elseif (strtolower(CIOINA_getenv('HTTP_FRONT_END_HTTPS')) == 'on') {
            $is_https = true;
        } elseif (strtolower(CIOINA_getenv('HTTP_X_FORWARDED_PROTO')) == 'https') {
            $is_https = true;
        } elseif (CIOINA_getenv('SERVER_PORT') == 443) {
            $is_https = true;
        }

        $this->set('is_https', $is_https);

        return $is_https;
    }

    /**
     * Detects whether https appears to be used.
     *
     * This function checks if the https protocol is used in the current connection
     * with the webserver, based on environment variables.
     * Please note that this just detects what we see, so
     * it completely ignores things like reverse proxies.
     *
     * @return bool
     */
    function detectHttps()
    {
        $url = array();

        // At first we try to parse REQUEST_URI, it might contain full URL,
        if (CIOINA_getenv('REQUEST_URI')) {
            // produces E_WARNING if it cannot get parsed, e.g. '/foobar:/'
            $url = @parse_url(CIOINA_getenv('REQUEST_URI'));
            if ($url === false) {
                $url = array();
            }
        }

        // If we don't have scheme, we didn't have full URL so we need to
        // dig deeper
        if (empty($url['scheme'])) {
            // Scheme
            if (CIOINA_getenv('HTTP_SCHEME')) {
                $url['scheme'] = CIOINA_getenv('HTTP_SCHEME');
            } elseif (CIOINA_getenv('HTTPS')
                && strtolower(CIOINA_getenv('HTTPS')) == 'on'
            ) {
                $url['scheme'] = 'https';
                // A10 Networks load balancer:
            } elseif (CIOINA_getenv('HTTP_HTTPS_FROM_LB')
                && strtolower(CIOINA_getenv('HTTP_HTTPS_FROM_LB')) == 'on'
            ) {
                $url['scheme'] = 'https';
            } elseif (CIOINA_getenv('HTTP_X_FORWARDED_PROTO')) {
                $url['scheme'] = /*overload*/mb_strtolower(
                    CIOINA_getenv('HTTP_X_FORWARDED_PROTO')
                );
            } elseif (CIOINA_getenv('HTTP_FRONT_END_HTTPS')
                && strtolower(CIOINA_getenv('HTTP_FRONT_END_HTTPS')) == 'on'
            ) {
                $url['scheme'] = 'https';
            } else {
                $url['scheme'] = 'http';
            }
        }

        if (isset($url['scheme']) && $url['scheme'] == 'https') {
            $is_https = true;
        } else {
            $is_https = false;
        }

        return $is_https;
    }

    /**
     * enables backward compatibility
     *
     * @return void
     */
    function enableBc()
    {
        $GLOBALS['cfg']             = $this->settings;
        
        $defines = array(
            'CIOINA_PHP_STR_VERSION',
            'CIOINA_PHP_INT_VERSION',
            'CIOINA_IS_WINDOWS',
            'CIOINA_IS_IIS',
            'CIOINA_IS_GD2',
            'CIOINA_USR_OS',
            'CIOINA_USR_BROWSER_VER',
            'CIOINA_USR_BROWSER_AGENT'
            );

        foreach ($defines as $define) {
            if (! defined($define)) {
                define($define, $this->get($define));
            }
        }
    }
}// end CIOINA_Config


/**
 * Error handler to catch fatal errors when loading configuration
 * file
 *
 * @return void
 */
function CIOINA_Config_fatalErrorHandler()
{
    if (isset($GLOBALS['CIOINA_config_loading']) && $GLOBALS['CIOINA_config_loading']) {
        $error = error_get_last();
        if ($error !== null) {
            CIOINA_fatalError(
                sprintf(
                    'Failed to load ACIOINA configuration (%s:%s): %s',
                    CIOINA_Error::relPath($error['file']),
                    $error['line'],
                    $error['message']
                )
            );
        }
    }
}

register_shutdown_function('CIOINA_Config_fatalErrorHandler');
?>
