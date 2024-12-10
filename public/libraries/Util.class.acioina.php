<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Hold the CIOINA_Util class
 *
 * @package ACIOINA
 */
if (! defined('ACIOINA')) {
    exit;
}

/**
 * Misc functions used all over the scripts.
 *
 * @package ACIOINA
 */
class CIOINA_Util
{
    public static function deleteTempFiles( $dir = null, $isPhpSession = false)
    {
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ( $ri as $file ) {
            if ($isPhpSession) {
                if(/*overload*/mb_strrpos($file->getFilename(), 'sess_') === 5){
                    @unlink($file);
                }
            }else{
                $file->isDir() ? @rmdir($file) : @unlink($file);
            }
        }
        return true;
    }

    public static function getGUID(){
        if (! function_exists('openssl_random_pseudo_bytes')) {
            $charid = bin2hex(phpseclib\Crypt\Random::string(32));
        } else {
            $charid = bin2hex(openssl_random_pseudo_bytes(32));
        }
        return  md5($charid);
    }

    public static function getIP() {
        $IP = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $IP =getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $IP =getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $IP =getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $IP =getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $IP = getenv('HTTP_FORWARDED');
        } elseif (isset($_SERVER) && isset($_SERVER['REMOTE_ADDR'])) {
            $IP = $_SERVER['REMOTE_ADDR'];
        }
        
        if (($pos = /*overload*/mb_strrpos($IP, ',')) !== false) {
            $IP = trim(/*overload*/mb_substr($IP, $pos+1));
        }

        return $IP;
    }

    public static function formatJsonString($data = null, $name='', $value = null, $isComma = true) {
        return ( (is_null($data) || $data === "0") ? '':'"'. $name .'":"'. (is_null($value) ? $data : $value) . '"'. ($isComma ? ',':'') );
    }

    public static function formatJsonBoolean($data = null, $name='', $isFalse = 'false' , $isTrue = 'true',  $isComma = true) {
        return '"'. $name .'":"'. ($data === "0" ? $isFalse : $isTrue) . '"' . ($isComma ? ',':'');
    }

    public static function deleteLastComma($jsonData) {
        $pos = /*overload*/mb_strrpos($jsonData, ',');
        $len = /*overload*/mb_strlen($jsonData);
        if ($pos !== false && $pos === $len -1) {
            $res = /*overload*/mb_substr($jsonData, 0,  $pos);
        }else{
            $res = $jsonData;
        }
        return  $res;
    }

    public static function getFormatedZip($rawZip) {
        if (is_null($rawZip))
        {
            return null;
        }

        return  (/*overload*/mb_strlen($rawZip) === 5 ? $rawZip : vsprintf('%1$05d-%2$04d', sscanf($rawZip,'%05d%04d')));
    }

    public static function getFormatedPhone($rawPhone) {
        if (is_null($rawPhone))
        {
            return null;
        }

        return  (/*overload*/mb_strlen($rawPhone) === 10 ? vsprintf('(%1$03d) %2$03d-%3$04d', sscanf($rawPhone,'%03d%03d%04d')) :
             vsprintf('(%1$03d) %2$03d-%3$04d x %4$05d', sscanf($rawPhone,'%03d%03d%04d%05d')) );
    }

    public static function getFormatedDate($mySqlDate) {
        return is_null($mySqlDate) ? null : vsprintf('%2$02d/%3$02d/%1$04d', sscanf($mySqlDate,'%04d-%02d-%02d'));
    }

    public static function getPlaneDate($mySqlDate) {
        return is_null($mySqlDate) ? null : vsprintf('%2$02d%3$02d%1$04d', sscanf($mySqlDate,'%04d-%02d-%02d'));
    }

    /**
     * Detects which function to use for pow.
     *
     * @return string Function name.
     */
    public static function detectPow()
    {
        if (function_exists('bcpow')) {
            // BCMath Arbitrary Precision Mathematics Function
            return 'bcpow';
        } elseif (function_exists('gmp_pow')) {
            // GMP Function
            return 'gmp_pow';
        } else {
            // PHP function
            return 'pow';
        }
    }

    /**
     * Exponential expression / raise number into power
     *
     * @param string $base         base to raise
     * @param string $exp          exponent to use
     * @param string $use_function pow function to use, or false for auto-detect
     *
     * @return mixed string or float
     */
    public static function pow($base, $exp, $use_function = '')
    {
        static $pow_function = null;

        if ($pow_function == null) {
            $pow_function = self::detectPow();
        }

        if (! $use_function) {
            if ($exp < 0) {
                $use_function = 'pow';
            } else {
                $use_function = $pow_function;
            }
        }

        if (($exp < 0) && ($use_function != 'pow')) {
            return false;
        }

        switch ($use_function) {
            case 'bcpow' :
                // bcscale() needed for testing pow() with base values < 1
                bcscale(10);
                $pow = bcpow($base, $exp);
                break;
            case 'gmp_pow' :
                $pow = gmp_strval(gmp_pow($base, $exp));
                break;
            case 'pow' :
                $base = (float) $base;
                $exp = (int) $exp;
                $pow = pow($base, $exp);
                break;
            default:
                $pow = $use_function($base, $exp);
        }

        return $pow;
    }

    /**
     * Returns the formatted maximum size for an upload
     *
     * @param integer $max_upload_size the size
     *
     * @return string the message
     *
     * @access  public
     */
    public static function getFormattedMaximumUploadSize($max_upload_size)
    {
        // I have to reduce the second parameter (sensitiveness) from 6 to 4
        // to avoid weird results like 512 kKib
        list($max_size, $max_unit) = self::formatByteDown($max_upload_size, 4);
        return '(' . sprintf(__('Max: %s%s'), $max_size, $max_unit) . ')';
    }

    /**
     * Add slashes before "'" and "\" characters so a value containing them can
     * be used in a sql comparison.
     *
     * @param string $a_string the string to slash
     * @param bool   $is_like  whether the string will be used in a 'LIKE' clause
     *                         (it then requires two more escaped sequences) or not
     * @param bool   $crlf     whether to treat cr/lfs as escape-worthy entities
     *                         (converts \n to \\n, \r to \\r)
     * @param bool   $php_code whether this function is used as part of the
     *                         "Create PHP code" dialog
     *
     * @return string   the slashed string
     *
     * @access  public
     */
    public static function sqlAddSlashes(
        $a_string = '', $is_like = false, $crlf = false, $php_code = false
    ) {
        if ($is_like) {
            $a_string = str_replace('\\', '\\\\\\\\', $a_string);
        } else {
            $a_string = str_replace('\\', '\\\\', $a_string);
        }

        if ($crlf) {
            $a_string = strtr(
                $a_string,
                array("\n" => '\n', "\r" => '\r', "\t" => '\t')
            );
        }

        if ($php_code) {
            $a_string = str_replace('\'', '\\\'', $a_string);
        } else {
            $a_string = str_replace('\'', '\'\'', $a_string);
        }

        return $a_string;
    } // end of the 'sqlAddSlashes()' function

    /**
     * Add slashes before "_" and "%" characters for using them in MySQL
     * database, table and field names.
     * Note: This function does not escape backslashes!
     *
     * @param string $name the string to escape
     *
     * @return string the escaped string
     *
     * @access  public
     */
    public static function escapeMysqlWildcards($name)
    {
        return strtr($name, array('_' => '\\_', '%' => '\\%'));
    } // end of the 'escapeMysqlWildcards()' function

    /**
     * removes slashes before "_" and "%" characters
     * Note: This function does not unescape backslashes!
     *
     * @param string $name the string to escape
     *
     * @return string   the escaped string
     *
     * @access  public
     */
    public static function unescapeMysqlWildcards($name)
    {
        return strtr($name, array('\\_' => '_', '\\%' => '%'));
    } // end of the 'unescapeMysqlWildcards()' function

    /**
     * removes quotes (',",`) from a quoted string
     *
     * checks if the string is quoted and removes this quotes
     *
     * @param string $quoted_string string to remove quotes from
     * @param string $quote         type of quote to remove
     *
     * @return string unqoted string
     */
    public static function unQuote($quoted_string, $quote = null)
    {
        $quotes = array();

        if ($quote === null) {
            $quotes[] = '`';
            $quotes[] = '"';
            $quotes[] = "'";
        } else {
            $quotes[] = $quote;
        }

        foreach ($quotes as $quote) {
            if (/*overload*/mb_substr($quoted_string, 0, 1) === $quote
                && /*overload*/mb_substr($quoted_string, -1, 1) === $quote
            ) {
                $unquoted_string = /*overload*/mb_substr($quoted_string, 1, -1);
                // replace escaped quotes
                $unquoted_string = str_replace(
                    $quote . $quote,
                    $quote,
                    $unquoted_string
                );
                return $unquoted_string;
            }
        }

        return $quoted_string;
    }

    /* ----------------------- Set of misc functions ----------------------- */

    /**
     * Adds backquotes on both sides of a database, table or field name.
     * and escapes backquotes inside the name with another backquote
     *
     * example:
     * <code>
     * echo backquote('owner`s db'); // `owner``s db`
     *
     * </code>
     *
     * @param mixed   $a_name the database, table or field name to "backquote"
     *                        or array of it
     * @param boolean $do_it  a flag to bypass this function (used by dump
     *                        functions)
     *
     * @return mixed    the "backquoted" database, table or field name
     *
     * @access  public
     */
    public static function backquote($a_name, $do_it = true)
    {
        if (is_array($a_name)) {
            foreach ($a_name as &$data) {
                $data = self::backquote($data, $do_it);
            }
            return $a_name;
        }

        if (! $do_it) {
            global $CIOINA_SQPdata_forbidden_word;
            $eltNameUpper = /*overload*/mb_strtoupper($a_name);
            if (!in_array($eltNameUpper, $CIOINA_SQPdata_forbidden_word)) {
                return $a_name;
            }
        }

        // '0' is also empty for php :-(
        if (/*overload*/mb_strlen($a_name) && $a_name !== '*') {
            return '`' . str_replace('`', '``', $a_name) . '`';
        } else {
            return $a_name;
        }
    } // end of the 'backquote()' function

    /**
     * Formats $value to byte view
     *
     * @param double|int $value the value to format
     * @param int        $limes the sensitiveness
     * @param int        $comma the number of decimals to retain
     *
     * @return array    the formatted value and its unit
     *
     * @access  public
     */
    public static function formatByteDown($value, $limes = 6, $comma = 0)
    {
        if ($value === null) {
            return null;
        }

        $byteUnits = array(
            /* l10n: shortcuts for Byte */
            __('B'),
            /* l10n: shortcuts for Kilobyte */
            __('KiB'),
            /* l10n: shortcuts for Megabyte */
            __('MiB'),
            /* l10n: shortcuts for Gigabyte */
            __('GiB'),
            /* l10n: shortcuts for Terabyte */
            __('TiB'),
            /* l10n: shortcuts for Petabyte */
            __('PiB'),
            /* l10n: shortcuts for Exabyte */
            __('EiB')
        );

        $dh   = self::pow(10, $comma);
        $li   = self::pow(10, $limes);
        $unit = $byteUnits[0];

        for ($d = 6, $ex = 15; $d >= 1; $d--, $ex-=3) {
            // cast to float to avoid overflow
            $unitSize = (float) $li * self::pow(10, $ex);
            if (isset($byteUnits[$d]) && $value >= $unitSize) {
                // use 1024.0 to avoid integer overflow on 64-bit machines
                $value = round($value / (self::pow(1024, $d) / $dh)) /$dh;
                $unit = $byteUnits[$d];
                break 1;
            } // end if
        } // end for

        if ($unit != $byteUnits[0]) {
            // if the unit is not bytes (as represented in current language)
            // reformat with max length of 5
            // 4th parameter=true means do not reformat if value < 1
            $return_value = self::formatNumber($value, 5, $comma, true);
        } else {
            // do not reformat, just handle the locale
            $return_value = self::formatNumber($value, 0);
        }

        return array(trim($return_value), $unit);
    } // end of the 'formatByteDown' function

    /**
     * Changes thousands and decimal separators to locale specific values.
     *
     * @param string $value the value
     *
     * @return string
     */
    public static function localizeNumber($value)
    {
        return str_replace(
            array(',', '.'),
            array(
                /* l10n: Thousands separator */
                __(','),
                /* l10n: Decimal separator */
                __('.'),
            ),
            $value
        );
    }

    /**
     * Formats $value to the given length and appends SI prefixes
     * with a $length of 0 no truncation occurs, number is only formatted
     * to the current locale
     *
     * examples:
     * <code>
     * echo formatNumber(123456789, 6);     // 123,457 k
     * echo formatNumber(-123456789, 4, 2); //    -123.46 M
     * echo formatNumber(-0.003, 6);        //      -3 m
     * echo formatNumber(0.003, 3, 3);      //       0.003
     * echo formatNumber(0.00003, 3, 2);    //       0.03 m
     * echo formatNumber(0, 6);             //       0
     * </code>
     *
     * @param double  $value          the value to format
     * @param integer $digits_left    number of digits left of the comma
     * @param integer $digits_right   number of digits right of the comma
     * @param boolean $only_down      do not reformat numbers below 1
     * @param boolean $noTrailingZero removes trailing zeros right of the comma
     *                                (default: true)
     *
     * @return string   the formatted value and its unit
     *
     * @access  public
     */
    public static function formatNumber(
        $value, $digits_left = 3, $digits_right = 0,
        $only_down = false, $noTrailingZero = true
    ) {
        if ($value == 0) {
            return '0';
        }

        $originalValue = $value;
        //number_format is not multibyte safe, str_replace is safe
        if ($digits_left === 0) {
            $value = number_format($value, $digits_right);
            if (($originalValue != 0) && (floatval($value) == 0)) {
                $value = ' <' . (1 / self::pow(10, $digits_right));
            }
            return self::localizeNumber($value);
        }

        // this units needs no translation, ISO
        $units = array(
            -8 => 'y',
            -7 => 'z',
            -6 => 'a',
            -5 => 'f',
            -4 => 'p',
            -3 => 'n',
            -2 => '&micro;',
            -1 => 'm',
            0 => ' ',
            1 => 'k',
            2 => 'M',
            3 => 'G',
            4 => 'T',
            5 => 'P',
            6 => 'E',
            7 => 'Z',
            8 => 'Y'
        );

        // check for negative value to retain sign
        if ($value < 0) {
            $sign = '-';
            $value = abs($value);
        } else {
            $sign = '';
        }

        $dh = self::pow(10, $digits_right);

        /*
         * This gives us the right SI prefix already,
         * but $digits_left parameter not incorporated
         */
        $d = floor(log10($value) / 3);
        /*
         * Lowering the SI prefix by 1 gives us an additional 3 zeros
         * So if we have 3,6,9,12.. free digits ($digits_left - $cur_digits)
         * to use, then lower the SI prefix
         */
        $cur_digits = floor(log10($value / self::pow(1000, $d, 'pow'))+1);
        if ($digits_left > $cur_digits) {
            $d -= floor(($digits_left - $cur_digits)/3);
        }

        if ($d < 0 && $only_down) {
            $d = 0;
        }

        $value = round($value / (self::pow(1000, $d, 'pow') / $dh)) /$dh;
        $unit = $units[$d];

        // If we don't want any zeros after the comma just add the thousand separator
        if ($noTrailingZero) {
            $localizedValue = self::localizeNumber(
                preg_replace('/(?<=\d)(?=(\d{3})+(?!\d))/', ',', $value)
            );
        } else {
            //number_format is not multibyte safe, str_replace is safe
            $localizedValue = self::localizeNumber(number_format($value, $digits_right));
        }

        if ($originalValue != 0 && floatval($value) == 0) {
            return ' <' . self::localizeNumber((1 / self::pow(10, $digits_right))) . ' ' . $unit;
        }

        return $sign . $localizedValue . ' ' . $unit;
    } 

    /**
     * Returns the number of bytes when a formatted size is given
     *
     * @param string $formatted_size the size expression (for example 8MB)
     *
     * @return integer  The numerical part of the expression (for example 8)
     */
    public static function extractValueFromFormattedSize($formatted_size)
    {
        $return_value = -1;

        if (preg_match('/^[0-9]+GB$/', $formatted_size)) {
            $return_value = /*overload*/mb_substr($formatted_size, 0, -2)
                * self::pow(1024, 3);
        } elseif (preg_match('/^[0-9]+MB$/', $formatted_size)) {
            $return_value = /*overload*/mb_substr($formatted_size, 0, -2)
                * self::pow(1024, 2);
        } elseif (preg_match('/^[0-9]+K$/', $formatted_size)) {
            $return_value = /*overload*/mb_substr($formatted_size, 0, -1)
                * self::pow(1024, 1);
        }
        return $return_value;
    }

    /**
     * Writes localised date
     *
     * @param integer $timestamp the current timestamp
     * @param string  $format    format
     *
     * @return string   the formatted date
     *
     * @access  public
     */
    public static function localisedDate($timestamp = -1, $format = '')
    {
        $month = array(
            /* l10n: Short month name */
            __('Jan'),
            /* l10n: Short month name */
            __('Feb'),
            /* l10n: Short month name */
            __('Mar'),
            /* l10n: Short month name */
            __('Apr'),
            /* l10n: Short month name */
            _pgettext('Short month name', 'May'),
            /* l10n: Short month name */
            __('Jun'),
            /* l10n: Short month name */
            __('Jul'),
            /* l10n: Short month name */
            __('Aug'),
            /* l10n: Short month name */
            __('Sep'),
            /* l10n: Short month name */
            __('Oct'),
            /* l10n: Short month name */
            __('Nov'),
            /* l10n: Short month name */
            __('Dec'));
        $day_of_week = array(
            /* l10n: Short week day name */
            _pgettext('Short week day name', 'Sun'),
            /* l10n: Short week day name */
            __('Mon'),
            /* l10n: Short week day name */
            __('Tue'),
            /* l10n: Short week day name */
            __('Wed'),
            /* l10n: Short week day name */
            __('Thu'),
            /* l10n: Short week day name */
            __('Fri'),
            /* l10n: Short week day name */
            __('Sat'));

        if ($format == '') {
            /* l10n: See http://www.acioina.php.net/manual/en/function.strftime.acioina.php */
            $format = __('%B %d, %Y at %I:%M %p');
        }

        if ($timestamp == -1) {
            $timestamp = time();
        }

        $date = preg_replace(
            '@%[aA]@',
            $day_of_week[(int)strftime('%w', $timestamp)],
            $format
        );
        $date = preg_replace(
            '@%[bB]@',
            $month[(int)strftime('%m', $timestamp)-1],
            $date
        );

        $ret = strftime($date, $timestamp);
        // Some OSes such as Win8.1 Traditional Chinese version did not produce UTF-8
        // output here. See https://sourceforge.net/p/ACIOINA/bugs/4207/
        if (mb_detect_encoding($ret, 'UTF-8', true) != 'UTF-8') {
            $ret = date('Y-m-d H:i:s', $timestamp);
        }

        return $ret;
    } 

    /**
     * Splits a URL string by parameter
     *
     * @param string $url the URL
     *
     * @return array  the parameter/value pairs, for example [0] db=sakila
     */
    public static function splitURLQuery($url)
    {
        // decode encoded url separators
        $separator = CIOINA_URL_getArgSeparator();
        // on most places separator is still hard coded ...
        if ($separator !== '&') {
            // ... so always replace & with $separator
            $url = str_replace(htmlentities('&'), $separator, $url);
            $url = str_replace('&', $separator, $url);
        }

        $url = str_replace(htmlentities($separator), $separator, $url);
        // end decode

        $url_parts = parse_url($url);

        if (! empty($url_parts['query'])) {
            return explode($separator, $url_parts['query']);
        } else {
            return array();
        }
    }

    /**
     * Returns a given timespan value in a readable format.
     *
     * @param int $seconds the timespan
     *
     * @return string  the formatted value
     */
    public static function timespanFormat($seconds)
    {
        $days = floor($seconds / 86400);
        if ($days > 0) {
            $seconds -= $days * 86400;
        }

        $hours = floor($seconds / 3600);
        if ($days > 0 || $hours > 0) {
            $seconds -= $hours * 3600;
        }

        $minutes = floor($seconds / 60);
        if ($days > 0 || $hours > 0 || $minutes > 0) {
            $seconds -= $minutes * 60;
        }

        return sprintf(
            __('%s days, %s hours, %s minutes and %s seconds'),
            (string)$days, (string)$hours, (string)$minutes, (string)$seconds
        );
    }

    /**
     * Takes a string and outputs each character on a line for itself. Used
     * mainly for horizontalflipped display mode.
     * Takes care of special html-characters.
     * Fulfills https://sourceforge.net/p/ACIOINA/feature-requests/164/
     *
     * @param string $string    The string
     * @param string $Separator The Separator (defaults to "<br />\n")
     *
     * @access  public
     * @todo    add a multibyte safe function $GLOBALS['CIOINA_String']->split()
     *
     * @return string      The flipped string
     */
    public static function flipstring($string, $Separator = "<br />\n")
    {
        $format_string = '';
        $charbuff = false;

        for ($i = 0, $str_len = /*overload*/mb_strlen($string);
             $i < $str_len;
             $i++
        ) {
            $char = $string[$i];
            $append = false;

            if ($char == '&') {
                $format_string .= $charbuff;
                $charbuff = $char;
            } elseif ($char == ';' && ! empty($charbuff)) {
                $format_string .= $charbuff . $char;
                $charbuff = false;
                $append = true;
            } elseif (! empty($charbuff)) {
                $charbuff .= $char;
            } else {
                $format_string .= $char;
                $append = true;
            }

            // do not add separator after the last character
            if ($append && ($i != $str_len - 1)) {
                $format_string .= $Separator;
            }
        }

        return $format_string;
    }

    /**
     * replaces %u in given path with current user name
     *
     * example:
     * <code>
     * $user_dir = userDir('/var/CIOINA_tmp/%u/'); // '/var/CIOINA_tmp/root/'
     *
     * </code>
     *
     * @param string $dir with wildcard for user
     *
     * @return string  per user directory
     */
    public static function userDir($dir)
    {
        // add trailing slash
        if (/*overload*/mb_substr($dir, -1) != '/') {
            $dir .= '/';
        }

        return str_replace('%u', $GLOBALS['cfg']['Server']['user'], $dir);
    }
    
    /**
     * Converts a bit value to printable format;
     * in MySQL a BIT field can be from 1 to 64 bits so we need this
     * function because in PHP, decbin() supports only 32 bits
     * on 32-bit servers
     *
     * @param number  $value  coming from a BIT field
     * @param integer $length length
     *
     * @return string  the printable value
     */
    public static function printableBitValue($value, $length)
    {
        // if running on a 64-bit server or the length is safe for decbin()
        if (PHP_INT_SIZE == 8 || $length < 33) {
            $printable = decbin($value);
        } else {
            // FIXME: does not work for the leftmost bit of a 64-bit value
            $i = 0;
            $printable = '';
            while ($value >= pow(2, $i)) {
                $i++;
            }
            if ($i != 0) {
                $i = $i - 1;
            }

            while ($i >= 0) {
                if ($value - pow(2, $i) < 0) {
                    $printable = '0' . $printable;
                } else {
                    $printable = '1' . $printable;
                    $value = $value - pow(2, $i);
                }
                $i--;
            }
            $printable = strrev($printable);
        }
        $printable = str_pad($printable, $length, '0', STR_PAD_LEFT);
        return $printable;
    }

    /**
     * Verifies whether the value contains a non-printable character
     *
     * @param string $value value
     *
     * @return integer
     */
    public static function containsNonPrintableAscii($value)
    {
        return preg_match('@[^[:print:]]@', $value);
    }

    /**
     * Converts a BIT type default value
     * for example, b'010' becomes 010
     *
     * @param string $bit_default_value value
     *
     * @return string the converted value
     */
    public static function convertBitDefaultValue($bit_default_value)
    {
        return rtrim(ltrim($bit_default_value, "b'"), "'");
    }


    /**
     * Replaces some characters by a displayable equivalent
     *
     * @param string $content content
     *
     * @return string the content with characters replaced
     */
    public static function replaceBinaryContents($content)
    {
        $result = str_replace("\x00", '\0', $content);
        $result = str_replace("\x08", '\b', $result);
        $result = str_replace("\x0a", '\n', $result);
        $result = str_replace("\x0d", '\r', $result);
        $result = str_replace("\x1a", '\Z', $result);
        return $result;
    }

    /**
     * If the string starts with a \r\n pair (0x0d0a) add an extra \n
     *
     * @param string $string string
     *
     * @return string with the chars replaced
     */
    public static function duplicateFirstNewline($string)
    {
        $first_occurence = /*overload*/mb_strpos($string, "\r\n");
        if ($first_occurence === 0) {
            $string = "\n" . $string;
        }
        return $string;
    }
    
    /**
     * Add fractional seconds to time, datetime and timestamp strings.
     * If the string contains fractional seconds,
     * pads it with 0s up to 6 decimal places.
     *
     * @param string $value time, datetime or timestamp strings
     *
     * @return string time, datetime or timestamp strings with fractional seconds
     */
    public static function addMicroseconds($value)
    {
        if (empty($value) || $value == 'CURRENT_TIMESTAMP') {
            return $value;
        }

        if (/*overload*/mb_strpos($value, '.') === false) {
            return $value . '.000000';
        }

        $value .= '000000';
        return /*overload*/mb_substr(
            $value,
            0,
            /*overload*/mb_strpos($value, '.') + 7
        );
    }

    /**
     * Reads the file, detects the compression MIME type, closes the file
     * and returns the MIME type
     *
     * @param resource $file the file handle
     *
     * @return string the MIME type for compression, or 'none'
     */
    public static function getCompressionMimeType($file)
    {
        $test = fread($file, 4);
        $len = strlen($test);
        fclose($file);
        if ($len >= 2 && $test[0] == chr(31) && $test[1] == chr(139)) {
            return 'application/gzip';
        }
        if ($len >= 3 && substr($test, 0, 3) == 'BZh') {
            return 'application/bzip2';
        }
        if ($len >= 4 && $test == "PK\003\004") {
            return 'application/zip';
        }
        return 'none';
    }
}
?>
