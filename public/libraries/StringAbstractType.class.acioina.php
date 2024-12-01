<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Common functions for classes based on CIOINA_StringByte interface.
 *
 * @package ACIOINA-String
 */
if (! defined('ACIOINA')) {
    exit;
}

require_once 'libraries/StringType.int.acioina.php';

/**
 * Implements CIOINA_StringByte interface using native PHP functions.
 *
 * @package ACIOINA-String
 */
abstract class CIOINA_StringAbstractType implements CIOINA_StringType
{
    /**
     * Checks if a number is in a range
     *
     * @param integer $num   number to check for
     * @param integer $lower lower bound
     * @param integer $upper upper bound
     *
     * @return boolean  whether the number is in the range or not
     */
    public function numberInRangeInclusive($num, $lower, $upper)
    {
        return ($num >= $lower && $num <= $upper);
    }
}
?>