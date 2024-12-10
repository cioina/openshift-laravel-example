<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Javascript escaping functions.
 *
 * @package ACIOINA
 *
 */
if (! defined('ACIOINA')) {
    exit;
}

/**
 * Format a string so it can be a string inside JavaScript code inside an
 * eventhandler (onclick, onchange, on..., ).
 * This function is used to displays a javascript confirmation box for
 * "DROP/DELETE/ALTER" queries.
 *
 * @param string  $a_string       the string to format
 * @param boolean $add_backquotes whether to add backquotes to the string or not
 *
 * @return string   the formatted string
 *
 * @access  public
 */
function CIOINA_jsFormat($a_string = '', $add_backquotes = true)
{
    if (is_string($a_string)) {
        $a_string = htmlspecialchars($a_string);
        $a_string = CIOINA_escapeJsString($a_string);
        // Needed for inline javascript to prevent some browsers
        // treating it as a anchor
        $a_string = str_replace('#', '\\#', $a_string);
    }

    return (($add_backquotes) ? CIOINA_Util::backquote($a_string) : $a_string);
} // end of the 'CIOINA_jsFormat()' function

/**
 * escapes a string to be inserted as string a JavaScript block
 * enclosed by <![CDATA[ ... ]]>
 * this requires only to escape ' with \' and end of script block
 *
 * We also remove NUL byte as some browsers (namely MSIE) ignore it and
 * inserting it anywhere inside </script would allow to bypass this check.
 *
 * @param string $string the string to be escaped
 *
 * @return string  the escaped string
 */
function CIOINA_escapeJsString($string)
{
    return preg_replace(
        '@</script@i', '</\' + \'script',
        strtr(
            $string,
            array(
                "\000" => '',
                '\\' => '\\\\',
                '\'' => '\\\'',
                '"' => '\"',
                "\n" => '\n',
                "\r" => '\r'
            )
        )
    );
}

/**
 * Formats a value for javascript code.
 *
 * @param string $value String to be formatted.
 *
 * @return string formatted value.
 */
function CIOINA_formatJsVal($value)
{
    if (is_bool($value)) {
        if ($value) {
            return 'true';
        }

        return 'false';
    }

    if (is_int($value)) {
        return (int)$value;
    }

    return '"' . CIOINA_escapeJsString($value) . '"';
}

/**
 * Formats an javascript assignment with proper escaping of a value
 * and support for assigning array of strings.
 *
 * @param string $key    Name of value to set
 * @param mixed  $value  Value to set, can be either string or array of strings
 * @param bool   $escape Whether to escape value or keep it as it is
 *                       (for inclusion of js code)
 *
 * @return string Javascript code.
 */
function CIOINA_getJsValue($key, $value, $escape = true)
{
    $result = $key . ' = ';
    if (!$escape) {
        $result .= $value;
    } elseif (is_array($value)) {
        $result .= '[';
        foreach ($value as $val) {
            $result .= CIOINA_formatJsVal($val) . ",";
        }
        $result .= "];\n";
    } else {
        $result .= CIOINA_formatJsVal($value) . ";\n";
    }
    return $result;
}

/**
 * Prints an javascript assignment with proper escaping of a value
 * and support for assigning array of strings.
 *
 * @param string $key   Name of value to set
 * @param mixed  $value Value to set, can be either string or array of strings
 *
 * @return void
 */
function CIOINA_printJsValue($key, $value)
{
    echo CIOINA_getJsValue($key, $value);
}

?>