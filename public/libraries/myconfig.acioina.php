<?php

if (! defined('ACIOINA')) {
    exit;
}

$cfg['PmaAbsoluteUri'] = '';

$cfg['APP_KEY'] = getenv('APP_KEY');
$cfg['APP_URL'] = getenv('APP_URL');

$cfg['FacebookAppId'] =         getenv('FACEBOOK_APP_ID');
$cfg['FacebookAppSecret'] =     getenv('FACEBOOK_APP_SECRET');
$cfg['FacebookGraphVersion'] =  'v15.0';
$cfg['FacebookRedirectUri'] =   $cfg['APP_URL'] . '/facebookini.php';
$cfg['FacebookLoginUri'] =      $cfg['APP_URL'] . '/%s/login';

$cfg['LaravelWebUser'] =     getenv('LARAVEL_WEB_USER');
$cfg['LaravelWebPassword'] = getenv('LARAVEL_WEB_PASSWORD');
$cfg['LaravelUser'] =        getenv('LARAVEL_USER');
$cfg['LaravelPassword'] =    getenv('LARAVEL_PASSWORD');
$cfg['LaravelAdminUri'] =    getenv('LARAVEL_ADMIN_URL');

$cfg['JwtSecret'] =         getenv('JWT_SECRET');

$cfg['LaravelStorage'] =   'storage/data_f1EF3a5E15504C288FB142F962961b9';

$cfg['MailgunKey'] =       getenv('MAILGUN_KEY');
$cfg['MailgunDomain'] =    getenv('MAILGUN_DOMAIN');
$cfg['MailgunRecipient'] = getenv('MAILGUN_RECIPIENT');

$cfg['SSLPort'] =      '443';
$cfg['DownForMaintenanceMessage'] = 'This website is down for maintenance. Please come back later!';

$cfg['MySqlHost'] =     'mysql';
$cfg['MySqlDatabase'] = 'sampledb';
$cfg['MySqlPort'] =     '3306';
$cfg['MySqlUser'] =     getenv('DB_USERNAME');
$cfg['MySqlPWord'] =    getenv('DB_PASSWORD');

$cfg['phpMyAdminBaseDir'] = getenv('PMA_BASE_DIR');
$cfg['phpMyAdminUri'] = $cfg['APP_URL'] .'/%s/index.php';

$cfg['MoxieManagerBaseDir'] = '/opt/app-root/src/storage/data_f1EF3a5E15504C288FB142F962961b9';
$cfg['TwigTempDir']         = '/opt/app-root/src/storage/data_f1EF3a5E15504C288FB142F962961b9/twig';
$cfg['AssetsDir']           = '/opt/app-root/src/storage/data_f1EF3a5E15504C288FB142F962961b9/assets';
$cfg['PhpSessionsTemp']     = '/tmp/sessions';
$cfg['NewPostsDir'] = 'newPosts';

$cfg['ExpendableServiceProviderEnabled'] = true;
$cfg['DownForMaintenance'] = false;
$cfg['ForceSSL'] = true;
$cfg['HomePage'] = 'blog';
$cfg['CacheVersion'] = '0.0.3';

$cfg['PeriodToUpdateFacebookImagesInDays'] = 90;
$cfg['PhpExecutionInMinutes'] = 10;
$cfg['SessionPeriodInMinutes'] = 30;
$cfg['MaxClientLoginAttempts'] = 7;
$cfg['LockoutPeriodInMinutes'] = 25;
$cfg['TotalOnlineClientLogins'] = 5;

$cfg['TotalStatisticsRecordsPerDay'] = 100000;
$cfg['TotalSessionsPerIpAddress'] = 3000;
$cfg['TotalNullSessionPerDay'] = 10;
$cfg['TotalGuestEmailsPerDay'] = 10;
$cfg['TotalFacebookEmailsPerDay'] = 50;

/**
 * use GZIP output buffering if possible (true|false|'auto')
 *
 * @global string $cfg['OBGzip']
 */
$cfg['OBGzip'] = 'auto';

/**
 * Path for storing session data (session_save_path PHP parameter).
 *
 * @global integer $cfg['SessionSavePath']
 */
$cfg['SessionSavePath'] = '';

/**
 * maximum allocated bytes ('-1' for no limit)
 * this is a string because '16M' is a valid value; we must put here
 * a string as the default value so that /setup accepts strings
 *
 * @global string $cfg['MemoryLimit']
 */
$cfg['MemoryLimit'] = '-1';

/*******************************************************************************
 * For the export features...
 */

/**
 * Allow for the use of zip compression (requires zip support to be enabled)
 *
 * @global boolean $cfg['ZipDump']
 */
$cfg['ZipDump'] = true;

/**
 * Allow for the use of gzip compression (requires zlib)
 *
 * @global boolean $cfg['GZipDump']
 */
$cfg['GZipDump'] = true;

/**
 * Allow for the use of bzip2 decompression (requires bz2 extension)
 *
 * @global boolean $cfg['BZipDump']
 */
$cfg['BZipDump'] = true;

/**
 * Will compress gzip exports on the fly without the need for much memory.
 * If you encounter problems with created gzip files disable this feature.
 *
 * @global boolean $cfg['CompressOnFly']
 */
$cfg['CompressOnFly'] = true;


/*******************************************************************************
 * Language and character set conversion settings
 */

/**
 * Default language to use, if not browser-defined or user-defined
 *
 * @global string $cfg['DefaultLang']
 */
$cfg['DefaultLang'] = 'en';

/**
 * Default connection collation
 *
 * @global string $cfg['DefaultConnectionCollation']
 */
$cfg['DefaultConnectionCollation'] = 'utf8_unicode_ci';

/**
 * Force: always use this language, e.g. 'en'
 *
 * @global string $cfg['Lang']
 */
$cfg['Lang'] = 'en';

/**
 * Regular expression to limit listed languages, e.g. '^(cs|en)' for Czech and
 * English only
 *
 * @global string $cfg['FilterLanguages']
 */
$cfg['FilterLanguages'] = '';

/**
 * You can select here which functions will be used for character set conversion.
 * Possible values are:
 *      auto   - automatically use available one (first is tested iconv, then
 *               recode)
 *      iconv  - use iconv or libiconv functions
 *      recode - use recode_string function
 *      mb     - use mbstring extension
 *      none   - disable encoding conversion
 *
 * @global string $cfg['RecodingEngine']
 */
$cfg['RecodingEngine'] = 'auto';

/**
 * Specify some parameters for iconv used in character set conversion. See iconv
 * documentation for details:
 * http://www.gnu.org/software/libiconv/documentation/libiconv/iconv_open.3.html
 *
 * @global string $cfg['IconvExtraParams']
 */
$cfg['IconvExtraParams'] = '//TRANSLIT';

/**
 * Available character sets for MySQL conversion. currently contains all which could
 * be found in lang/* files and few more.
 * Character sets will be shown in same order as here listed, so if you frequently
 * use some of these move them to the top.
 *
 * @global array $cfg['AvailableCharsets']
 */
$cfg['AvailableCharsets'] = array(
    'iso-8859-1',
    'iso-8859-2',
    'iso-8859-3',
    'iso-8859-4',
    'iso-8859-5',
    'iso-8859-6',
    'iso-8859-7',
    'iso-8859-8',
    'iso-8859-9',
    'iso-8859-10',
    'iso-8859-11',
    'iso-8859-12',
    'iso-8859-13',
    'iso-8859-14',
    'iso-8859-15',
    'windows-1250',
    'windows-1251',
    'windows-1252',
    'windows-1256',
    'windows-1257',
    'koi8-r',
    'big5',
    'gb2312',
    'utf-16',
    'utf-8',
    'utf-7',
    'x-user-defined',
    'euc-jp',
    'ks_c_5601-1987',
    'tis-620',
    'SHIFT_JIS'
);

/*******************************************************************************
 * Web server upload/save/import directories
 */

/**
 * Directory for uploaded files that can be executed by ACIOINA.
 * For example './upload'. Leave empty for no upload directory support.
 * Use %u for username inclusion.
 *
 * @global string $cfg['UploadDir']
 */
$cfg['UploadDir'] = '';

/**
 * Directory where ACIOINA can save exported data on server.
 * For example './save'. Leave empty for no save directory support.
 * Use %u for username inclusion.
 *
 * @global string $cfg['SaveDir']
 */
$cfg['SaveDir'] = '';

/**
 * Directory where ACIOINA can save temporary files.
 *
 * @global string $cfg['TempDir']
 */
$cfg['TempDir'] = '';


/**
 * Misc. settings
 */

/**
 * Is GD >= 2 available? Set to yes/no/auto. 'auto' does auto-detection,
 * which is the only safe way to determine GD version.
 *
 * @global string $cfg['GD2Available']
 */
$cfg['GD2Available'] = 'auto';

/**
 * Lists proxy IP and HTTP header combinations which are trusted for IP allow/deny
 *
 * @global array $cfg['TrustedProxies']
 */
$cfg['TrustedProxies'] = array();

/**
 * We normally check the permissions on the configuration file to ensure
 * it's not world writable. However, ACIOINA could be installed on
 * a NTFS filesystem mounted on a non-Windows server, in which case the
 * permissions seems wrong but in fact cannot be detected. In this case
 * a sysadmin would set the following to false.
 */
$cfg['CheckConfigurationPermissions'] = true;

/**
 * Limit for length of URL in links. When length would be above this limit, it
 * is replaced by form with button.
 * This is required as some web servers (IIS) have problems with long URLs.
 * The recommended limit is 2000
 * (see http://www.boutell.com/newfaq/misc/urllength.html) but we put
 * 1000 to accommodate Suhosin, see bug #3358750.
 */
$cfg['LinkLengthLimit'] = 1000;

/**
 * Additional string to allow in CSP headers.
 */
$cfg['CSPAllow'] = '';


/**
 * Zero Configuration mode.
 *
 * @global boolean $cfg['ZeroConf']
 */
$cfg['ZeroConf'] = true;

?>
