<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Holds class CIOINA_Error
 *
 * @package ACIOINA
 */

if (! defined('ACIOINA')) {
    exit;
}

/**
 * base class
 */
require_once './libraries/Message.class.acioina.php';

/**
 * a single error
 *
 * @package ACIOINA
 */
class CIOINA_Error extends CIOINA_Message
{
    /**
     * Error types
     *
     * @var array
     */
    static public $errortype = array (
        0                    => 'Internal error',
        E_ERROR              => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parsing Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_DEPRECATED         => 'Deprecation Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
    );

    /**
     * Error levels
     *
     * @var array
     */
    static public $errorlevel = array (
        0                    => 'error',
        E_ERROR              => 'error',
        E_WARNING            => 'error',
        E_PARSE              => 'error',
        E_NOTICE             => 'notice',
        E_CORE_ERROR         => 'error',
        E_CORE_WARNING       => 'error',
        E_COMPILE_ERROR      => 'error',
        E_COMPILE_WARNING    => 'error',
        E_USER_ERROR         => 'error',
        E_USER_WARNING       => 'error',
        E_USER_NOTICE        => 'notice',
        E_STRICT             => 'notice',
        E_DEPRECATED         => 'notice',
        E_RECOVERABLE_ERROR  => 'error',
    );

    /**
     * The file in which the error occurred
     *
     * @var string
     */
    protected $file = '';

    /**
     * The line in which the error occurred
     *
     * @var integer
     */
    protected $line = 0;

    /**
     * Holds the backtrace for this error
     *
     * @var array
     */
    protected $backtrace = array();

    /**
     * Unique id
     *
     * @var string
     */
    protected $hash = null;

    /**
     * Constructor
     *
     * @param integer $errno   error number
     * @param string  $errstr  error message
     * @param string  $errfile file
     * @param integer $errline line
     */
    public function __construct($errno, $errstr, $errfile, $errline)
    {
        $this->setNumber($errno);
        $this->setMessage($errstr, false);
        $this->setFile($errfile);
        $this->setLine($errline);

        $backtrace = debug_backtrace();
        // remove last three calls:
        // debug_backtrace(), handleError() and addError()
        $backtrace = array_slice($backtrace, 3);

        $this->setBacktrace($backtrace);
    }

    /**
     * sets CIOINA_Error::$_backtrace
     *
     * @param array $backtrace backtrace
     *
     * @return void
     *
     * @todo This function should store only processed backtrace as full
     *       backtrace requires too much memory (especially with Response
     *       object included). It could probably store only printable
     *       representation as created by getBacktraceDisplay or some
     *       intermediate form.
     */
    public function setBacktrace($backtrace)
    {
        $this->backtrace = $backtrace;
    }

    /**
     * sets CIOINA_Error::$_line
     *
     * @param integer $line the line
     *
     * @return void
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * sets CIOINA_Error::$_file
     *
     * @param string $file the file
     *
     * @return void
     */
    public function setFile($file)
    {
        $this->file = CIOINA_Error::relPath($file);
    }


    /**
     * returns unique CIOINA_Error::$hash, if not exists it will be created
     *
     * @return string CIOINA_Error::$hash
     */
    public function getHash()
    {
        try {
            $backtrace = serialize($this->getBacktrace());
        } catch(Exception $e) {
            $backtrace = '';
        }
        if ($this->hash === null) {
            $this->hash = md5(
                $this->getNumber() .
                $this->getMessage() .
                $this->getFile() .
                $this->getLine() .
                $backtrace
            );
        }

        return $this->hash;
    }

    /**
     * returns CIOINA_Error::$_backtrace for first $count frames
     * pass $count = -1 to get full backtrace.
     * The same can be done by not passing $count at all.
     *
     * @param integer $count Number of stack frames.
     *
     * @return array CIOINA_Error::$_backtrace
     */
    public function getBacktrace($count = -1)
    {
        if ($count != -1) {
            return array_slice($this->backtrace, 0, $count);
        }
        return $this->backtrace;
    }

    /**
     * returns CIOINA_Error::$file
     *
     * @return string CIOINA_Error::$file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * returns CIOINA_Error::$line
     *
     * @return integer CIOINA_Error::$line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * returns type of error
     *
     * @return string  type of error
     */
    public function getType()
    {
        return CIOINA_Error::$errortype[$this->getNumber()];
    }

    /**
     * returns level of error
     *
     * @return string  level of error
     */
    public function getLevel()
    {
        return CIOINA_Error::$errorlevel[$this->getNumber()];
    }

    /**
     * returns title prepared for HTML Title-Tag
     *
     * @return string   HTML escaped and truncated title
     */
    public function getHtmlTitle()
    {
        return htmlspecialchars(
            /*overload*/mb_substr($this->getTitle(), 0, 100)
        );
    }

    /**
     * returns title for error
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getType() . ': ' . $this->getMessage();
    }

    /**
     * Get HTML backtrace
     *
     * @return string
     */
    public function getBacktraceDisplay()
    {
        return CIOINA_Error::formatBacktrace(
            $this->getBacktrace(),
            "<br />\n",
            "<br />\n"
        );
    }

    /**
     * return formatted backtrace field
     *
     * @param array  $backtrace Backtrace data
     * @param string $separator Arguments separator to use
     * @param string $lines     Lines separator to use
     *
     * @return string formatted backtrace
     * @static
     */
    static function formatBacktrace($backtrace, $separator, $lines)
    {
        $retval = '';

        foreach ($backtrace as $step) {
            if (isset($step['file']) && isset($step['line'])) {
                $retval .= CIOINA_Error::relPath($step['file'])
                    . '#' . $step['line'] . ': ';
            }
            if (isset($step['class'])) {
                $retval .= $step['class'] . $step['type'];
            }
            $retval .= CIOINA_Error::getFunctionCall($step, $separator);
            $retval .= $lines;
        }

        return $retval;
    }

    /**
     * Formats function call in a backtrace
     *
     * @param array  $step      backtrace step
     * @param string $separator Arguments separator to use
     *
     * @return string
     * @static
     */
    static function getFunctionCall($step, $separator)
    {
        $retval = $step['function'] . '(';
        if (isset($step['args'])) {
            if (count($step['args']) > 1) {
                $retval .= $separator;
                foreach ($step['args'] as $arg) {
                    $retval .= "\t";
                    $retval .= CIOINA_Error::getArg($arg, $step['function']);
                    $retval .= ',' . $separator;
                }
            } elseif (count($step['args']) > 0) {
                foreach ($step['args'] as $arg) {
                    $retval .= CIOINA_Error::getArg($arg, $step['function']);
                }
            }
        }
        $retval .= ')';
        return $retval;
    }

    /**
     * Get a single function argument
     *
     * if $function is one of include/require
     * the $arg is converted to a relative path
     *
     * @param string $arg      argument to process
     * @param string $function function name
     *
     * @return string
     * @static
     */
    static function getArg($arg, $function)
    {
        $retval = '';
        $include_functions = array(
            'include',
            'include_once',
            'require',
            'require_once',
        );
        $connect_functions = array(
            'mysql_connect',
            'mysql_pconnect',
            'mysqli_connect',
            'mysqli_real_connect',
            'connect',
            '_realConnect'
        );

        if (in_array($function, $include_functions)) {
            $retval .= CIOINA_Error::relPath($arg);
        } elseif (in_array($function, $connect_functions)
            && getType($arg) === 'string'
        ) {
            $retval .= getType($arg) . ' ********';
        } elseif (is_scalar($arg)) {
            $retval .= getType($arg) . ' '
                . htmlspecialchars(var_export($arg, true));
        } else {
            $retval .= getType($arg);
        }

        return $retval;
    }

    /**
     * Gets the error as string of HTML
     *
     * @return string
     */
    public function getDisplay()
    {
        $this->isDisplayed(true);
        $retval = '<div class="' . $this->getLevel() . '">';
        if (! $this->isUserError()) {
            $retval .= '<strong>' . $this->getType() . '</strong>';
            $retval .= ' in ' . $this->getFile() . '#' . $this->getLine();
            $retval .= "<br />\n";
        }
        $retval .= $this->getMessage();
        if (! $this->isUserError()) {
            $retval .= "<br />\n";
            $retval .= "<br />\n";
            $retval .= "<strong>Backtrace</strong><br />\n";
            $retval .= "<br />\n";
            $retval .= $this->getBacktraceDisplay();
        }
        $retval .= '</div>';

        return $retval;
    }

    /**
     * whether this error is a user error
     *
     * @return boolean
     */
    public function isUserError()
    {
        return $this->getNumber() & (E_USER_WARNING | E_USER_ERROR | E_USER_NOTICE);
    }

    /**
     * return short relative path to ACIOINA basedir
     *
     * prevent path disclosure in error message,
     * and make users feel safe to submit error reports
     *
     * @param string $dest path to be shorten
     *
     * @return string shortened path
     * @static
     */
    static function relPath($dest)
    {
        $dest = realpath($dest);

        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $separator = '\\';
        } else {
            $separator = '/';
        }

        $Ahere = explode(
            $separator,
            realpath(__DIR__ . $separator . '..')
        );
        $Adest = explode($separator, $dest);

        $result = '.';
        // && count ($Adest)>0 && count($Ahere)>0 )
        while (implode($separator, $Adest) != implode($separator, $Ahere)) {
            if (count($Ahere) > count($Adest)) {
                array_pop($Ahere);
                $result .= $separator . '..';
            } else {
                array_pop($Adest);
            }
        }
        $path = $result . str_replace(implode($separator, $Adest), '', $dest);
        return str_replace(
            $separator . $separator,
            $separator,
            $path
        );
    }
}
?>
