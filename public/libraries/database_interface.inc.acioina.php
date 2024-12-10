<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Creates the database interface required for database interactions
 * and add it to GLOBALS.
 *
 * @package ACIOINA-DBI
 */
if (! defined('ACIOINA')) {
    exit;
}

require_once './libraries/DatabaseInterface.class.acioina.php';

include_once './libraries/dbi/DBIMysqli.class.acioina.php';

$extension = new CIOINA_DBI_Mysqli();
$GLOBALS['CIOINA_dbi'] = new CIOINA_DatabaseInterface($extension);
?>
