<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Main interface for database interactions
 *
 * @package ACIOINA-DBI
 */
if (! defined('ACIOINA')) {
    exit;
}

/**
 * Main interface for database interactions
 *
 * @package ACIOINA-DBI
 */
class CIOINA_DatabaseInterface
{
    /**
     * Force STORE_RESULT method, ignored by classic MySQL.
     */
    const QUERY_STORE = 1;
    /**
     * Do not read whole query.
     */
    const QUERY_UNBUFFERED = 2;
    /**
     * Get session variable.
     */
    const GETVAR_SESSION = 1;
    /**
     * Get global variable.
     */
    const GETVAR_GLOBAL = 2;

    /**
     * @var CIOINA_DBI_Extension
     */
    private $_extension;

    /**
     * Constructor
     *
     * @param CIOINA_DBI_Extension $ext Object to be used for database queries
     */
    public function __construct(CIOINA_DBI_Extension $ext)
    {
        $this->_extension = $ext;
    }

    /**
     * Checks whether database extension is loaded
     *
     * @param string $extension mysql extension to check
     *
     * @return bool
     */
    public static function checkDbExtension($extension = 'mysql')
    {
        if ($extension == 'drizzle' && function_exists('drizzle_create')) {
            return true;
        } else if (function_exists($extension . '_connect')) {
            return true;
        }
        return false;
    }

    /**
     * runs a query and returns the result
     *
     * @param string  $query               query to run
     * @param object  $link                mysql link resource
     * @param integer $options             query options
     * @param bool    $cache_affected_rows whether to cache affected row
     *
     * @return mixed
     */
    public function tryQuery($query, $link = null, $options = 0,
        $cache_affected_rows = true
    ) {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }

        $result = $this->_extension->realQuery($query, $link, $options);

        if ($cache_affected_rows) {
            $GLOBALS['cached_affected_rows'] = $this->affectedRows($link, false);
        }

        return $result;
    }

    /**
     * Run multi query statement and return results
     *
     * @param string $multi_query multi query statement to execute
     * @param mysqli $link        mysqli object
     *
     * @return mysqli_result collection | boolean(false)
     */
    public function tryMultiQuery($multi_query = '', $link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }

        return $this->_extension->realMultiQuery($link, $multi_query);
    }

    /**
     * Function called just after a connection to the MySQL database server has
     * been established. It sets the connection collation, and determines the
     * version of MySQL which is running.
     *
     * @param mixed $link mysql link resource|object
     *
     * @return void
     */
    public function postConnect($link)
    {
        $version = $this->fetchSingleRow(
            'SELECT @@version, @@version_comment',
            'ASSOC',
            $link
        );

        if ($version) {
            $match = explode('.', $version['@@version']);
            define('CIOINA_MYSQL_MAJOR_VERSION', (int)$match[0]);
            define(
                'CIOINA_MYSQL_INT_VERSION',
                (int) sprintf(
                    '%d%02d%02d', $match[0], $match[1], intval($match[2])
                )
            );
            define('CIOINA_MYSQL_STR_VERSION', $version['@@version']);
            define(
                'CIOINA_MYSQL_VERSION_COMMENT',
                $version['@@version_comment']
            );
        } else {
            define('CIOINA_MYSQL_INT_VERSION', 50501);
            define('CIOINA_MYSQL_MAJOR_VERSION', 5);
            define('CIOINA_MYSQL_STR_VERSION', '5.05.01');
            define('CIOINA_MYSQL_VERSION_COMMENT', '');
        }
        
        /* Detect Drizzle - it does not support charsets */
        $charset_result = $this->tryQuery(
            "SHOW VARIABLES LIKE 'character_set_results'",
            $link
        );
        if ($this->numRows($charset_result) == 0) {
            define('CIOINA_DRIZZLE', true);
        } else {
            define('CIOINA_DRIZZLE', false);
        }
        $this->freeResult($charset_result);


        // Skip charsets for Drizzle
        if (!CIOINA_DRIZZLE) {
            if (CIOINA_MYSQL_INT_VERSION >  50503) {
                $default_charset = 'utf8mb4';
                $default_collation = 'utf8mb4_general_ci';
            } else {
                $default_charset = 'utf8';
                $default_collation = 'utf8_general_ci';
            }
            
            
            if (! empty($GLOBALS['collation_connection'])) {
                $this->tryQuery(
                    "SET CHARACTER SET '$default_charset';",
                    $link,
                    self::QUERY_STORE
                );
                /* Automatically adjust collation to mb4 variant */
                if ($default_charset == 'utf8mb4'
                    && strncmp('utf8_', $GLOBALS['collation_connection'], 5) == 0
                ) {
                    $GLOBALS['collation_connection'] = 'utf8mb4_' . substr(
                        $GLOBALS['collation_connection'],
                        5
                    );
                }
                $this->tryQuery(
                    "SET collation_connection = '"
                    . CIOINA_Util::sqlAddSlashes($GLOBALS['collation_connection'])
                    . "';",
                    $link,
                    self::QUERY_STORE
                );
            } else {
                $this->tryQuery(
                    "SET NAMES '$default_charset' COLLATE '$default_collation';",
                    $link,
                    self::QUERY_STORE
                );
            }
        }
    }

    /**
     * returns a single value from the given result or query,
     * if the query or the result has more than one row or field
     * the first field of the first row is returned
     *
     * <code>
     * $sql = 'SELECT `name` FROM `user` WHERE `id` = 123';
     * $user_name = $GLOBALS['CIOINA_dbi']->fetchValue($sql);
     * // produces
     * // $user_name = 'John Doe'
     * </code>
     *
     * @param string         $query      The query to execute
     * @param integer        $row_number row to fetch the value from,
     *                                   starting at 0, with 0 being default
     * @param integer|string $field      field to fetch the value from,
     *                                   starting at 0, with 0 being default
     * @param object         $link       mysql link
     *
     * @return mixed value of first field in first row from result
     *               or false if not found
     */
    public function fetchValue($query, $row_number = 0, $field = 0, $link = null)
    {
        $value = false;

        $result = $this->tryQuery(
            $query,
            $link,
            self::QUERY_STORE,
            false
        );
        if ($result === false) {
            return false;
        }

        // return false if result is empty or false
        // or requested row is larger than rows in result
        if ($this->numRows($result) < ($row_number + 1)) {
            return $value;
        }

        // if $field is an integer use non associative mysql fetch function
        if (is_int($field)) {
            $fetch_function = 'fetchRow';
        } else {
            $fetch_function = 'fetchAssoc';
        }

        // get requested row
        for ($i = 0; $i <= $row_number; $i++) {
            $row = $this->$fetch_function($result);
        }
        $this->freeResult($result);

        // return requested field
        if (isset($row[$field])) {
            $value = $row[$field];
        }

        return $value;
    }

    /**
     * returns only the first row from the result
     *
     * <code>
     * $sql = 'SELECT * FROM `user` WHERE `id` = 123';
     * $user = $GLOBALS['CIOINA_dbi']->fetchSingleRow($sql);
     * // produces
     * // $user = array('id' => 123, 'name' => 'John Doe')
     * </code>
     *
     * @param string $query The query to execute
     * @param string $type  NUM|ASSOC|BOTH returned array should either
     *                      numeric associative or both
     * @param object $link  mysql link
     *
     * @return array|boolean first row from result
     *                       or false if result is empty
     */
    public function fetchSingleRow($query, $type = 'ASSOC', $link = null)
    {
        $result = $this->tryQuery(
            $query,
            $link,
            self::QUERY_STORE,
            false
        );
        if ($result === false) {
            return false;
        }

        // return false if result is empty or false
        if (! $this->numRows($result)) {
            return false;
        }

        switch ($type) {
            case 'NUM' :
                $fetch_function = 'fetchRow';
                break;
            case 'ASSOC' :
                $fetch_function = 'fetchAssoc';
                break;
            case 'BOTH' :
            default :
                $fetch_function = 'fetchArray';
                break;
        }

        $row = $this->$fetch_function($result);
        $this->freeResult($result);
        return $row;
    }

    /**
     * Returns row or element of a row
     *
     * @param array       $row   Row to process
     * @param string|null $value Which column to return
     *
     * @return mixed
     */
    private function _fetchValue($row, $value)
    {
        if (is_null($value)) {
            return $row;
        } else {
            return $row[$value];
        }
    }

    /**
     * returns all rows in the resultset in one array
     *
     * <code>
     * $sql = 'SELECT * FROM `user`';
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql);
     * // produces
     * // $users[] = array('id' => 123, 'name' => 'John Doe')
     *
     * $sql = 'SELECT `id`, `name` FROM `user`';
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql, 'id');
     * // produces
     * // $users['123'] = array('id' => 123, 'name' => 'John Doe')
     *
     * $sql = 'SELECT `id`, `name` FROM `user`';
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql, 0);
     * // produces
     * // $users['123'] = array(0 => 123, 1 => 'John Doe')
     *
     * $sql = 'SELECT `id`, `name` FROM `user`';
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql, 'id', 'name');
     * // or
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql, 0, 1);
     * // produces
     * // $users['123'] = 'John Doe'
     *
     * $sql = 'SELECT `name` FROM `user`';
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql);
     * // produces
     * // $users[] = 'John Doe'
     *
     * $sql = 'SELECT `group`, `name` FROM `user`'
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql, array('group', null), 'name');
     * // produces
     * // $users['admin'][] = 'John Doe'
     *
     * $sql = 'SELECT `group`, `name` FROM `user`'
     * $users = $GLOBALS['CIOINA_dbi']->fetchResult($sql, array('group', 'name'), 'id');
     * // produces
     * // $users['admin']['John Doe'] = '123'
     * </code>
     *
     * @param string               $query   query to execute
     * @param string|integer|array $key     field-name or offset
     *                                      used as key for array
     *                                      or array of those
     * @param string|integer       $value   value-name or offset
     *                                      used as value for array
     * @param object               $link    mysql link
     * @param integer              $options query options
     *
     * @return array resultrows or values indexed by $key
     */
    public function fetchResult($query, $key = null, $value = null,
        $link = null, $options = 0
    ) {
        $resultrows = array();

        $result = $this->tryQuery($query, $link, $options, false);

        // return empty array if result is empty or false
        if ($result === false) {
            return $resultrows;
        }

        $fetch_function = 'fetchAssoc';

        // no nested array if only one field is in result
        if (null === $key && 1 === $this->numFields($result)) {
            $value = 0;
            $fetch_function = 'fetchRow';
        }

        // if $key is an integer use non associative mysql fetch function
        if (is_int($key)) {
            $fetch_function = 'fetchRow';
        }

        if (null === $key) {
            while ($row = $this->$fetch_function($result)) {
                $resultrows[] = $this->_fetchValue($row, $value);
            }
        } else {
            if (is_array($key)) {
                while ($row = $this->$fetch_function($result)) {
                    $result_target =& $resultrows;
                    foreach ($key as $key_index) {
                        if (null === $key_index) {
                            $result_target =& $result_target[];
                            continue;
                        }

                        if (! isset($result_target[$row[$key_index]])) {
                            $result_target[$row[$key_index]] = array();
                        }
                        $result_target =& $result_target[$row[$key_index]];
                    }
                    $result_target = $this->_fetchValue($row, $value);
                }
            } else {
                while ($row = $this->$fetch_function($result)) {
                    $resultrows[$row[$key]] = $this->_fetchValue($row, $value);
                }
            }
        }

        $this->freeResult($result);
        return $resultrows;
    }

    /**
     * Formats database error message in a friendly way.
     * This is needed because some errors messages cannot
     * be obtained by mysql_error().
     *
     * @param int    $error_number  Error code
     * @param string $error_message Error message as returned by server
     *
     * @return string HML text with error details
     */
    public function formatError($error_number, $error_message)
    {
        if (! empty($error_message)) {
            $error_message = $this->convertMessage($error_message);
        }

        $error_message = htmlspecialchars($error_message);

        $error = '#' . ((string) $error_number);

        if ($error_number == 2002) {
            $error .= ' - ' . $error_message;
            $error .= '<br />';
            $error .= __(
                'The server is not responding (or the local server\'s socket'
                . ' is not correctly configured).'
            );
        } elseif ($error_number == 2003) {
            $error .= ' - ' . $error_message;
            $error .= '<br />' . __('The server is not responding.');
        } elseif ($error_number == 1005) {
            if (strpos($error_message, 'errno: 13') !== false) {
                $error .= ' - ' . $error_message;
                $error .= '<br />'
                    . __('Please check privileges of directory containing database.');
            } else {
                /* InnoDB constraints, see
                 * http://dev.mysql.com/doc/refman/5.0/en/
                 *  innodb-foreign-key-constraints.html
                 */
                $error .= ' - ' . $error_message .
                    ' (<a href="server_engines.acioina.php' .
                    CIOINA_URL_getCommon(
                        array('engine' => 'InnoDB', 'page' => 'Status')
                    ) . '">' . __('Details…') . '</a>)';
            }
        } else {
            $error .= ' - ' . $error_message;
        }

        return $error;
    }

    /**
     * connects to the database server
     *
     * @param string $user                 user name
     * @param string $password             user password
     * @param bool   $is_controluser       whether this is a control user connection
     * @param array  $server               host/port/socket/persistent
     * @param bool   $auxiliary_connection (when true, don't go back to login if
     *                                     connection fails)
     *
     * @return mixed false on error or a connection object on success
     */
    public function connect(
        $user, $password, $is_controluser = false, $server = null,
        $auxiliary_connection = false
    ) {
        $result = $this->_extension->connect(
            $user, $password, $is_controluser, $server, $auxiliary_connection
        );

        if ($result) {
            if (! $auxiliary_connection && ! $is_controluser) {
                $GLOBALS['CIOINA_dbi']->postConnect($result);
            }
            return $result;
        }

        if ($is_controluser) {
            trigger_error(
                __(
                    'Connection for controluser as defined in your '
                    . 'configuration failed.'
                ),
                E_USER_WARNING
            );
            return false;
        }

        return $result;
    }

    /**
     * selects given database
     *
     * @param string $dbname database name to select
     * @param object $link   connection object
     *
     * @return boolean
     */
    public function selectDb($dbname, $link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        return $this->_extension->selectDb($dbname, $link);
    }

    /**
     * returns array of rows with associative and numeric keys from $result
     *
     * @param object $result result set identifier
     *
     * @return array
     */
    public function fetchArray($result)
    {
        return $this->_extension->fetchArray($result);
    }

    /**
     * returns array of rows with associative keys from $result
     *
     * @param object $result result set identifier
     *
     * @return array
     */
    public function fetchAssoc($result)
    {
        return $this->_extension->fetchAssoc($result);
    }

    /**
     * returns array of rows with numeric keys from $result
     *
     * @param object $result result set identifier
     *
     * @return array
     */
    public function fetchRow($result)
    {
        return $this->_extension->fetchRow($result);
    }

    /**
     * Adjusts the result pointer to an arbitrary row in the result
     *
     * @param object  $result database result
     * @param integer $offset offset to seek
     *
     * @return bool true on success, false on failure
     */
    public function dataSeek($result, $offset)
    {
        return $this->_extension->dataSeek($result, $offset);
    }

    /**
     * Frees memory associated with the result
     *
     * @param object $result database result
     *
     * @return void
     */
    public function freeResult($result)
    {
        $this->_extension->freeResult($result);
    }

    /**
     * Check if there are any more query results from a multi query
     *
     * @param object $link the connection object
     *
     * @return bool true or false
     */
    public function moreResults($link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        return $this->_extension->moreResults($link);
    }

    /**
     * Prepare next result from multi_query
     *
     * @param object $link the connection object
     *
     * @return bool true or false
     */
    public function nextResult($link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        return $this->_extension->nextResult($link);
    }

    /**
     * Store the result returned from multi query
     *
     * @param object $link the connection object
     *
     * @return mixed false when empty results / result set when not empty
     */
    public function storeResult($link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        return $this->_extension->storeResult($link);
    }

    /**
     * Returns a string representing the type of connection used
     *
     * @param object $link mysql link
     *
     * @return string type of connection used
     */
    public function getHostInfo($link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        return $this->_extension->getHostInfo($link);
    }

    /**
     * Returns the version of the MySQL protocol used
     *
     * @param object $link mysql link
     *
     * @return integer version of the MySQL protocol used
     */
    public function getProtoInfo($link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        return $this->_extension->getProtoInfo($link);
    }

    /**
     * returns a string that represents the client library version
     *
     * @return string MySQL client library version
     */
    public function getClientInfo()
    {
        return $this->_extension->getClientInfo();
    }

    /**
     * returns last error message or false if no errors occurred
     *
     * @param object $link connection link
     *
     * @return string|bool $error or false
     */
    public function getError($link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        return $this->_extension->getError($link);
    }

    /**
     * returns the number of rows returned by last query
     *
     * @param object $result result set identifier
     *
     * @return string|int
     */
    public function numRows($result)
    {
        return $this->_extension->numRows($result);
    }

    /**
     * returns last inserted auto_increment id for given $link
     * or $GLOBALS['userlink']
     *
     * @param object $link the connection object
     *
     * @return int|boolean
     */
    public function insertId($link = null)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }
        // If the primary key is BIGINT we get an incorrect result
        // (sometimes negative, sometimes positive)
        // and in the present function we don't know if the PK is BIGINT
        // so better play safe and use LAST_INSERT_ID()
        //
        // When no controluser is defined, using mysqli_insert_id($link)
        // does not always return the last insert id due to a mixup with
        // the tracking mechanism, but this works:
        return $GLOBALS['CIOINA_dbi']->fetchValue('SELECT LAST_INSERT_ID();', 0, 0, $link);
    }

    /**
     * returns the number of rows affected by last query
     *
     * @param object $link           the connection object
     * @param bool   $get_from_cache whether to retrieve from cache
     *
     * @return int|boolean
     */
    public function affectedRows($link = null, $get_from_cache = true)
    {
        $link = $this->getLink($link);
        if ($link === false) {
            return false;
        }

        if ($get_from_cache) {
            return $GLOBALS['cached_affected_rows'];
        } else {
            return $this->_extension->affectedRows($link);
        }
    }

    /**
     * returns metainfo for fields in $result
     *
     * @param object $result result set identifier
     *
     * @return array meta info for fields in $result
     */
    public function getFieldsMeta($result)
    {
        return $this->_extension->getFieldsMeta($result);
    }

    /**
     * return number of fields in given $result
     *
     * @param object $result result set identifier
     *
     * @return int field count
     */
    public function numFields($result)
    {
        return $this->_extension->numFields($result);
    }

    /**
     * returns the length of the given field $i in $result
     *
     * @param object $result result set identifier
     * @param int    $i      field
     *
     * @return int length of field
     */
    public function fieldLen($result, $i)
    {
        return $this->_extension->fieldLen($result, $i);
    }

    /**
     * returns name of $i. field in $result
     *
     * @param object $result result set identifier
     * @param int    $i      field
     *
     * @return string name of $i. field in $result
     */
    public function fieldName($result, $i)
    {
        return $this->_extension->fieldName($result, $i);
    }

    /**
     * returns concatenated string of human readable field flags
     *
     * @param object $result result set identifier
     * @param int    $i      field
     *
     * @return string field flags
     */
    public function fieldFlags($result, $i)
    {
        return $this->_extension->fieldFlags($result, $i);
    }

    /**
     * Gets server connection port
     *
     * @param array|null $server host/port/socket/persistent
     *
     * @return null|integer
     */
    public function getServerPort($server = null)
    {
        if (empty($server['port'])) {
            return null;
        } else {
            return intval($server['port']);
        }
    }

    /**
     * Gets correct link object.
     *
     * @param object $link optional database link to use
     *
     * @return object|boolean
     */
    public function getLink($link = null)
    {
        if ( ! is_null($link) && $link !== false) {
            return $link;
        }

        if (isset($GLOBALS['userlink']) && !is_null($GLOBALS['userlink'])) {
            return $GLOBALS['userlink'];
        } else {
            return false;
        }
    }

    public function isSuperuser()
    {
        return false;
    }


}
?>
