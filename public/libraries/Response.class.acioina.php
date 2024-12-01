<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Manages the rendering of pages in PMA
 *
 * @package ACIOINA
 */
if (! defined('ACIOINA')) {
    exit;
}

require_once 'libraries/OutputBuffering.class.acioina.php';

/**
 * Singleton class used to manage the rendering of pages in PMA
 *
 * @package ACIOINA
 */
class CIOINA_Response
{
    /**
     * CIOINA_Response instance
     *
     * @access private
     * @static
     * @var CIOINA_Response
     */
    private static $_instance;
    /**
     * CIOINA_Header instance
     *
     * @access private
     * @var CIOINA_Header
     */
    //private $_header;
    /**
     * HTML data to be used in the response
     *
     * @access private
     * @var string
     */
    private $_HTML;
    /**
     * An array of JSON key-value pairs
     * to be sent back for ajax requests
     *
     * @access private
     * @var array
     */
    private $_JSON;
    /**
     * CIOINA_Footer instance
     *
     * @access private
     * @var CIOINA_Footer
     */
    //private $_footer;
    /**
     * Whether we are servicing an ajax request.
     *
     * @access private
     * @var bool
     */
    private $_isAjax;
    /**
     * Whether there were any errors during the processing of the request
     * Only used for ajax responses
     *
     * @access private
     * @var bool
     */
    private $_isSuccess;
    /**
     * Workaround for PHP bug
     *
     * @access private
     * @var string|bool
     */
    private $_CWD;

    /**
     * Creates a new class instance
     */
    private function __construct()
    {
        if (! defined('TESTSUITE')) {
            $buffer = CIOINA_OutputBuffering::getInstance();
            $buffer->start();
            register_shutdown_function(array($this, 'response'));
        }
        $this->_HTML   = '';
        $this->_JSON   = array();

        $this->_isSuccess  = true;
        $this->_isAjax     = true;
        $this->_CWD = getcwd();
    }

    /**
     * Returns the singleton CIOINA_Response object
     *
     * @return CIOINA_Response object
     */
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new CIOINA_Response();
        }

        return self::$_instance;
    }

    /**
     * Set the status of an ajax response,
     * whether it is a success or an error
     *
     * @param bool $state Whether the request was successfully processed
     *
     * @return void
     */
    public function isSuccess($state)
    {
        $this->_isSuccess = ($state == true);
    }

    /**
     * Returns true or false depending on whether
     * we are servicing an ajax request
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->_isAjax;
    }

    /**
     * Returns the path to the current working directory
     * Necessary to work around a PHP bug where the CWD is
     * reset after the initial script exits
     *
     * @return string
     */
    public function getCWD()
    {
        return $this->_CWD;
    }

    /**
     * Disables the rendering of the header
     * and the footer in responses
     *
     * @return void
     */
    public function disable()
    {
        //$this->_header->disable();
        //$this->_footer->disable();
    }

    /**
     * Returns a CIOINA_Header object
     *
     * @return CIOINA_Header
     */

    /**
     * Add JSON code to the response
     *
     * @param mixed $json  Either a key (string) or an
     *                     array or key-value pairs
     * @param mixed $value Null, if passing an array in $json otherwise
     *                     it's a string value to the key
     *
     * @return void
     */
    public function addJSON($json, $value = null)
    {
        if (is_array($json)) {
            foreach ($json as $key => $value) {
                $this->addJSON($key, $value);
            }
        } else {
            if ($value instanceof CIOINA_Message) {
                $this->_JSON[$json] = $value->getDisplay();
            } else {
                $this->_JSON[$json] = $value;
            }
        }

    }

    /**
     * Sends a JSON response to the browser
     *
     * @return void
     */
    private function _ajaxResponse()
    {
        if ($this->_isSuccess) {
            $this->_JSON['success'] = true;
        } else {
            $this->_JSON['success'] = false;
            if(isset($this->_JSON['message']))
            {
                $this->_JSON['error']   = $this->_JSON['message'];
                unset($this->_JSON['message']);
            }
        }

        // Set the Content-Type header to JSON so that jQuery parses the
        // response correctly.
        CIOINA_headerJSON();

        $result = json_encode($this->_JSON);
        if ($result === false) {
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $error = 'No errors';
                    break;
                case JSON_ERROR_DEPTH:
                    $error = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                case JSON_ERROR_RECURSION:
                    $error = 'One or more recursive references in the value to be encoded';
                    break;
                case JSON_ERROR_INF_OR_NAN:
                    $error = 'One or more NAN or INF values in the value to be encoded';
                    break;
                case JSON_ERROR_UNSUPPORTED_TYPE:
                    $error = 'A value of a type that cannot be encoded was given';
                default:
                    $error = 'Unknown error';
                    break;
            }
            echo json_encode(
                array(
                    'success' => false,
                    'error' => 'JSON encoding failed: ' . $error,
                )
            );
        } else {
            echo $result;
        }
    }

    /**
     * Sends an HTML response to the browser
     *
     * @static
     * @return void
     */
    public static function response()
    {
        $response = CIOINA_Response::getInstance();
        chdir($response->getCWD());

        if (! defined('TESTSUITE')) {
            $buffer = CIOINA_OutputBuffering::getInstance();
            if (empty($response->_HTML)) {
                $response->_HTML = $buffer->getContents();
            }
            $response->_ajaxResponse();
            $buffer->flush();
            exit;
        }else{
            $response->_ajaxResponse();
        }
    }
}
?>
