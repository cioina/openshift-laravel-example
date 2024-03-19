<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Holds class CIOINA_Error_Handler
 *
 * @package ACIOINA
 */

if (! defined('ACIOINA')) {
    exit;
}

/**
 *
 */
require_once './libraries/Error.class.acioina.php';

/**
 * handling errors
 *
 * @package ACIOINA
 */
class CIOINA_Error_Handler
{
    /**
     * holds errors to be displayed or reported later ...
     *
     * @var array of CIOINA_Error
     */
    protected $errors = array();

    /**
     * Constructor - set PHP error handler
     *
     */
    public function __construct()
    {
        /**
         * Do not set ourselves as error handler in case of testsuite.
         *
         * This behavior is not tested there and breaks other tests as they
         * rely on PHPUnit doing it's own error handling which we break here.
         */
        if (!defined('TESTSUITE')) {
            set_error_handler(array($this, 'handleError'));
        }
    }

    /**
     * Destructor
     *
     * stores errors in session
     *
     */
    public function __destruct()
    {
        if (isset($_SESSION)) {
            if (! isset($_SESSION['errors'])) {
                $_SESSION['errors'] = array();
            }

            // remember only not displayed errors
            foreach ($this->errors as $key => $error) {
                /**
                 * We don't want to store all errors here as it would
                 * explode user session.
                 */
                if (count($_SESSION['errors']) >= 10) {
                    $error = new CIOINA_Error(
                        0,
                        __('Too many error messages, some are not displayed.'),
                        __FILE__,
                        __LINE__
                    );
                    $_SESSION['errors'][$error->getHash()] = $error;
                    break;
                } else if (($error instanceof CIOINA_Error)
                    && ! $error->isDisplayed()
                ) {
                    $_SESSION['errors'][$key] = $error;
                }
            }
        }
    }

    /**
     * returns array with all errors
     *
     * @param bool $check Whether to check for session errors
     *
     * @return Error[]
     */
    public function getErrors($check=true)
    {
        if ($check) {
            $this->checkSavedErrors();
        }
        return $this->errors;
    }

    /**
     * returns the errors occurred in the current run only.
     * Does not include the errors save din the SESSION
     *
     * @return array of current errors
     */
    public function getCurrentErrors()
    {
        return $this->errors;
    }

    /**
     * Error handler - called when errors are triggered/occurred
     *
     * This calls the addError() function, escaping the error string
     * Ignores the errors wherever Error Control Operator (@) is used.
     *
     * @param integer $errno   error number
     * @param string  $errstr  error string
     * @param string  $errfile error file
     * @param integer $errline error line
     *
     * @return void
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        // check if Error Control Operator (@) was used.
        if (error_reporting() == 0) {
            return;
        }
        $this->addError($errstr, $errno, $errfile, $errline, true);
    }

    /**
     * Add an error; can also be called directly (with or without escaping)
     *
     * The following error types cannot be handled with a user defined function:
     * E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR,
     * E_COMPILE_WARNING,
     * and most of E_STRICT raised in the file where set_error_handler() is called.
     *
     * Do not use the context parameter as we want to avoid storing the
     * complete $GLOBALS inside $_SESSION['errors']
     *
     * @param string  $errstr  error string
     * @param integer $errno   error number
     * @param string  $errfile error file
     * @param integer $errline error line
     * @param boolean $escape  whether to escape the error string
     *
     * @return void
     */
    public function addError($errstr, $errno, $errfile, $errline, $escape = true)
    {
        if ($escape) {
            $errstr = htmlspecialchars($errstr);
        }
        // create error object
        $error = new CIOINA_Error(
            $errno,
            $errstr,
            $errfile,
            $errline
        );

        // do not repeat errors
        $this->errors[$error->getHash()] = $error;

        switch ($error->getNumber()) {
            case E_STRICT:
            case E_DEPRECATED:
            case E_NOTICE:
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
            case E_USER_NOTICE:
            case E_USER_WARNING:
            case E_USER_ERROR:
                // just collect the error
                // display is called from outside
                break;
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            default:
                // FATAL error, exit
                exit;
        }
    }


    /**
     * log error to configured log facility
     *
     * @param CIOINA_Error $error the error
     *
     * @return bool
     *
     * @todo finish!
     */
    protected function logError($error)
    {
        return error_log($error->getMessage());
    }

    /**
     * trigger a custom error
     *
     * @param string  $errorInfo   error message
     * @param integer $errorNumber error number
     *
     * @return void
     */
    public function triggerError($errorInfo, $errorNumber = null)
    {
        // we could also extract file and line from backtrace
        // and call handleError() directly
        trigger_error($errorInfo, $errorNumber);
    }

    /**
     * look in session for saved errors
     *
     * @return void
     */
    protected function checkSavedErrors()
    {
        if (isset($_SESSION['errors'])) {

            // restore saved errors
            foreach ($_SESSION['errors'] as $hash => $error) {
                if ($error instanceof CIOINA_Error && ! isset($this->errors[$hash])) {
                    $this->errors[$hash] = $error;
                }
            }
 
            // delete stored errors
            $_SESSION['errors'] = array();
            unset($_SESSION['errors']);
        }
    }

    /**
     * return count of errors
     *
     * @param bool $check Whether to check for session errors
     *
     * @return integer number of errors occurred
     */
    public function countErrors($check=true)
    {
        return count($this->getErrors($check));
    }

    /**
     * return count of user errors
     *
     * @return integer number of user errors occurred
     */
    public function countUserErrors()
    {
        $count = 0;
        if ($this->countErrors()) {
            foreach ($this->getErrors() as $error) {
                if ($error->isUserError()) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * whether use errors occurred or not
     *
     * @return boolean
     */
    public function hasUserErrors()
    {
        return (bool) $this->countUserErrors();
    }

    /**
     * whether errors occurred or not
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return (bool) $this->countErrors();
    }

     /**
     * Deletes previously stored errors in SESSION.
     * Saves current errors in session as previous errors.
     * Required to save current errors in case  'ask'
     *
     * @return void
     */
    public function savePreviousErrors()
    {
        unset($_SESSION['prev_errors']);
        $_SESSION['prev_errors'] = $GLOBALS['CIOINA_error_handler']->getCurrentErrors();
    }

    /**
     * Pops recent errors from the storage
     *
     * @param int $count Old error count
     *
     * @return Error[]
     */
    public function sliceErrors($count)
    {
        $errors = $this->getErrors(false);
        $this->errors = array_splice($errors, 0, $count);
        return array_splice($errors, $count);
    }

 }
?>
