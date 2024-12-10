<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Holds class CIOINA_Message
 *
 * @package ACIOINA
 */

/**
 * a single message
 *
 * simple usage examples:
 * <code>
 * // display simple error message 'Error'
 * CIOINA_Message::error()->display();
 *
 * // get simple success message 'Success'
 * $message = CIOINA_Message::success();
 *
 * // get special notice
 * $message = CIOINA_Message::notice(__('This is a localized notice'));
 * </code>
 *
 * more advanced usage example:
 * <code>
 * // create a localized success message
 * $message = CIOINA_Message::success('strSomeLocaleMessage');
 *
 * // create another message, a hint, with a localized string which expects
 * // two parameters: $strSomeTooltip = 'Read the %smanual%s'
 * $hint = CIOINA_Message::notice('strSomeTooltip');
 * // replace placeholders with the following params
 * $hint->addParam('[doc@cfg_Example]');
 * $hint->addParam('[/doc]');
 * // add this hint as a tooltip
 * $hint = showHint($hint);
 *
 * // add the retrieved tooltip reference to the original message
 * $message->addMessage($hint);
 *
 * // create another message ...
 * $more = CIOINA_Message::notice('strSomeMoreLocale');
 * $more->addString('strSomeEvenMoreLocale', '<br />');
 * $more->addParam('parameter for strSomeMoreLocale');
 * $more->addParam('more parameter for strSomeMoreLocale');
 *
 * // and add it also to the original message
 * $message->addMessage($more);
 * // finally add another raw message
 * $message->addMessage('some final words', ' - ');
 *
 * // display() will now print all messages in the same order as they are added
 * $message->display();
 * // strSomeLocaleMessage <sup>1</sup> strSomeMoreLocale<br />
 * // strSomeEvenMoreLocale - some final words
 * </code>
 *
 * @package ACIOINA
 */
class CIOINA_Message
{
    const SUCCESS = 1; // 0001
    const NOTICE  = 2; // 0010
    const ERROR   = 8; // 1000

    const SANITIZE_NONE   = 0;  // 0000 0000
    const SANITIZE_STRING = 16; // 0001 0000
    const SANITIZE_PARAMS = 32; // 0010 0000
    const SANITIZE_BOOTH  = 48; // 0011 0000

    /**
     * message levels
     *
     * @var array
     */
    static public $level = array (
        CIOINA_Message::SUCCESS => 'success',
        CIOINA_Message::NOTICE  => 'notice',
        CIOINA_Message::ERROR   => 'error',
    );

    /**
     * The message number
     *
     * @access  protected
     * @var     integer
     */
    protected $number = CIOINA_Message::NOTICE;

    /**
     * The locale string identifier
     *
     * @access  protected
     * @var     string
     */
    protected $string = '';

    /**
     * The formatted message
     *
     * @access  protected
     * @var     string
     */
    protected $message = '';

    /**
     * Whether the message was already displayed
     *
     * @access  protected
     * @var     boolean
     */
    protected $isDisplayed = false;

    /**
     * Unique id
     *
     * @access  protected
     * @var string
     */
    protected $hash = null;

    /**
     * holds parameters
     *
     * @access  protected
     * @var     array
     */
    protected $params = array();

    /**
     * holds additional messages
     *
     * @access  protected
     * @var     array
     */
    protected $addedMessages = array();

    /**
     * Constructor
     *
     * @param string  $string   The message to be displayed
     * @param integer $number   A numeric representation of the type of message
     * @param array   $params   An array of parameters to use in the message
     * @param integer $sanitize A flag to indicate what to sanitize, see
     *                          constant definitions above
     */
    public function __construct($string = '', $number = CIOINA_Message::NOTICE,
        $params = array(), $sanitize = CIOINA_Message::SANITIZE_NONE
    ) {
        $this->setString($string, $sanitize & CIOINA_Message::SANITIZE_STRING);
        $this->setNumber($number);
        $this->setParams($params, $sanitize & CIOINA_Message::SANITIZE_PARAMS);
    }

    /**
     * magic method: return string representation for this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * get CIOINA_Message of type success
     *
     * shorthand for getting a simple success message
     *
     * @param string $string A localized string
     *                       e.g. __('Your SQL query has been
     *                       executed successfully')
     *
     * @return CIOINA_Message
     * @static
     */
    static public function success($string = '')
    {
        if (empty($string)) {
            $string = __('Your SQL query has been executed successfully.');
        }

        return new CIOINA_Message($string, CIOINA_Message::SUCCESS);
    }

    /**
     * get CIOINA_Message of type error
     *
     * shorthand for getting a simple error message
     *
     * @param string $string A localized string e.g. __('Error')
     *
     * @return CIOINA_Message
     * @static
     */
    static public function error($string = '')
    {
        if (empty($string)) {
            $string = __('Error');
        }

        return new CIOINA_Message($string, CIOINA_Message::ERROR);
    }

    /**
     * get CIOINA_Message of type notice
     *
     * shorthand for getting a simple notice message
     *
     * @param string $string A localized string
     *                       e.g. __('The additional features for working with
     *                       linked tables have been deactivated. To find out
     *                       why click %shere%s.')
     *
     * @return CIOINA_Message
     * @static
     */
    static public function notice($string)
    {
        return new CIOINA_Message($string, CIOINA_Message::NOTICE);
    }

    /**
     * get CIOINA_Message with customized content
     *
     * shorthand for getting a customized message
     *
     * @param string  $message A localized string
     * @param integer $type    A numeric representation of the type of message
     *
     * @return CIOINA_Message
     * @static
     */
    static public function raw($message, $type = CIOINA_Message::NOTICE)
    {
        $r = new CIOINA_Message('', $type);
        $r->setMessage($message);
        return $r;
    }

    /**
     * get CIOINA_Message for number of affected rows
     *
     * shorthand for getting a customized message
     *
     * @param integer $rows Number of rows
     *
     * @return CIOINA_Message
     * @static
     */
    static public function getMessageForAffectedRows($rows)
    {
        $message = CIOINA_Message::success(
            _ngettext('%1$d row affected.', '%1$d rows affected.', $rows)
        );
        $message->addParam($rows);
        return $message;
    }

    /**
     * get CIOINA_Message for number of deleted rows
     *
     * shorthand for getting a customized message
     *
     * @param integer $rows Number of rows
     *
     * @return CIOINA_Message
     * @static
     */
    static public function getMessageForDeletedRows($rows)
    {
        $message = CIOINA_Message::success(
            _ngettext('%1$d row deleted.', '%1$d rows deleted.', $rows)
        );
        $message->addParam($rows);
        return $message;
    }

    /**
     * get CIOINA_Message for number of inserted rows
     *
     * shorthand for getting a customized message
     *
     * @param integer $rows Number of rows
     *
     * @return CIOINA_Message
     * @static
     */
    static public function getMessageForInsertedRows($rows)
    {
        $message = CIOINA_Message::success(
            _ngettext('%1$d row inserted.', '%1$d rows inserted.', $rows)
        );
        $message->addParam($rows);
        return $message;
    }

    /**
     * get CIOINA_Message of type error with custom content
     *
     * shorthand for getting a customized error message
     *
     * @param string $message A localized string
     *
     * @return CIOINA_Message
     * @static
     */
    static public function rawError($message)
    {
        return CIOINA_Message::raw($message, CIOINA_Message::ERROR);
    }

    /**
     * get CIOINA_Message of type notice with custom content
     *
     * shorthand for getting a customized notice message
     *
     * @param string $message A localized string
     *
     * @return CIOINA_Message
     * @static
     */
    static public function rawNotice($message)
    {
        return CIOINA_Message::raw($message, CIOINA_Message::NOTICE);
    }

    /**
     * get CIOINA_Message of type success with custom content
     *
     * shorthand for getting a customized success message
     *
     * @param string $message A localized string
     *
     * @return CIOINA_Message
     * @static
     */
    static public function rawSuccess($message)
    {
        return CIOINA_Message::raw($message, CIOINA_Message::SUCCESS);
    }

    /**
     * returns whether this message is a success message or not
     * and optionally makes this message a success message
     *
     * @param boolean $set Whether to make this message of SUCCESS type
     *
     * @return boolean whether this is a success message or not
     */
    public function isSuccess($set = false)
    {
        if ($set) {
            $this->setNumber(CIOINA_Message::SUCCESS);
        }

        return $this->getNumber() === CIOINA_Message::SUCCESS;
    }

    /**
     * returns whether this message is a notice message or not
     * and optionally makes this message a notice message
     *
     * @param boolean $set Whether to make this message of NOTICE type
     *
     * @return boolean whether this is a notice message or not
     */
    public function isNotice($set = false)
    {
        if ($set) {
            $this->setNumber(CIOINA_Message::NOTICE);
        }

        return $this->getNumber() === CIOINA_Message::NOTICE;
    }

    /**
     * returns whether this message is an error message or not
     * and optionally makes this message an error message
     *
     * @param boolean $set Whether to make this message of ERROR type
     *
     * @return boolean Whether this is an error message or not
     */
    public function isError($set = false)
    {
        if ($set) {
            $this->setNumber(CIOINA_Message::ERROR);
        }

        return $this->getNumber() === CIOINA_Message::ERROR;
    }

    /**
     * set raw message (overrides string)
     *
     * @param string  $message  A localized string
     * @param boolean $sanitize Whether to sanitize $message or not
     *
     * @return void
     */
    public function setMessage($message, $sanitize = false)
    {
        if ($sanitize) {
            $message = CIOINA_Message::sanitize($message);
        }
        $this->message = $message;
    }

    /**
     * set string (does not take effect if raw message is set)
     *
     * @param string  $string   string to set
     * @param boolean $sanitize whether to sanitize $string or not
     *
     * @return void
     */
    public function setString($string, $sanitize = true)
    {
        if ($sanitize) {
            $string = CIOINA_Message::sanitize($string);
        }
        $this->string = $string;
    }

    /**
     * set message type number
     *
     * @param integer $number message type number to set
     *
     * @return void
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * add parameter, usually in conjunction with strings
     *
     * usage
     * <code>
     * $message->addParam('strLocale', false);
     * $message->addParam('[em]some string[/em]');
     * $message->addParam('<img src="img" />', false);
     * </code>
     *
     * @param mixed   $param parameter to add
     * @param boolean $raw   whether parameter should be passed as is
     *                       without html escaping
     *
     * @return void
     */
    public function addParam($param, $raw = true)
    {
        if ($param instanceof CIOINA_Message) {
            $this->params[] = $param;
        } elseif ($raw) {
            $this->params[] = htmlspecialchars($param);
        } else {
            $this->params[] = CIOINA_Message::notice($param);
        }
    }

    /**
     * add another string to be concatenated on displaying
     *
     * @param string $string    to be added
     * @param string $separator to use between this and previous string/message
     *
     * @return void
     */
    public function addString($string, $separator = ' ')
    {
        $this->addedMessages[] = $separator;
        $this->addedMessages[] = CIOINA_Message::notice($string);
    }

    /**
     * add a bunch of messages at once
     *
     * @param array  $messages  to be added
     * @param string $separator to use between this and previous string/message
     *
     * @return void
     */
    public function addMessages($messages, $separator = ' ')
    {
        foreach ($messages as $message) {
            $this->addMessage($message, $separator);
        }
    }

    /**
     * add another raw message to be concatenated on displaying
     *
     * @param mixed  $message   to be added
     * @param string $separator to use between this and previous string/message
     *
     * @return void
     */
    public function addMessage($message, $separator = ' ')
    {
        if (/*overload*/mb_strlen($separator)) {
            $this->addedMessages[] = $separator;
        }

        if ($message instanceof CIOINA_Message) {
            $this->addedMessages[] = $message;
        } else {
            $this->addedMessages[] = CIOINA_Message::rawNotice($message);
        }
    }

    /**
     * set all params at once, usually used in conjunction with string
     *
     * @param array|string $params   parameters to set
     * @param boolean      $sanitize whether to sanitize params
     *
     * @return void
     */
    public function setParams($params, $sanitize = false)
    {
        if ($sanitize) {
            $params = CIOINA_Message::sanitize($params);
        }
        $this->params = $params;
    }

    /**
     * return all parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * return all added messages
     *
     * @return array
     */
    public function getAddedMessages()
    {
        return $this->addedMessages;
    }

    /**
     * Sanitizes $message
     *
     * @param mixed $message the message(s)
     *
     * @return mixed  the sanitized message(s)
     * @access  public
     * @static
     */
    static public function sanitize($message)
    {
        if (is_array($message)) {
            foreach ($message as $key => $val) {
                $message[$key] = CIOINA_Message::sanitize($val);
            }

            return $message;
        }

        return htmlspecialchars($message);
    }

    /**
     * decode $message, taking into account our special codes
     * for formatting
     *
     * @param string $message the message
     *
     * @return string  the decoded message
     * @access  public
     * @static
     */
    static public function decodeBB($message)
    {
        return CIOINA_sanitize($message, false, true);
    }

    /**
     * wrapper for sprintf()
     *
     * @return string formatted
     */
    static public function format()
    {
        $params = func_get_args();
        if (isset($params[1]) && is_array($params[1])) {
            array_unshift($params[1], $params[0]);
            $params = $params[1];
        }

        return call_user_func_array('sprintf', $params);
    }

    /**
     * returns unique CIOINA_Message::$hash, if not exists it will be created
     *
     * @return string CIOINA_Message::$hash
     */
    public function getHash()
    {
        if (null === $this->hash) {
            $this->hash = md5(
                $this->getNumber() .
                $this->string .
                $this->message
            );
        }

        return $this->hash;
    }

    /**
     * returns compiled message
     *
     * @return string complete message
     */
    public function getMessage()
    {
        $message = $this->message;

        if (0 === /*overload*/mb_strlen($message)) {
            $string = $this->getString();
            if (isset($GLOBALS[$string])) {
                $message = $GLOBALS[$string];
            } elseif (0 === /*overload*/mb_strlen($string)) {
                $message = '';
            } else {
                $message = $string;
            }
        }

        if (count($this->getParams()) > 0) {
            $message = CIOINA_Message::format($message, $this->getParams());
        }

        $message = CIOINA_Message::decodeBB($message);

        foreach ($this->getAddedMessages() as $add_message) {
            $message .= $add_message;
        }

        return $message;
    }

    /**
    * Returns only message string without image & other HTML.
    *
    * @return string
    */
    public function getOnlyMessage()
    {
        return $this->message;
    }


    /**
     * returns CIOINA_Message::$string
     *
     * @return string CIOINA_Message::$string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * returns CIOINA_Message::$number
     *
     * @return integer CIOINA_Message::$number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * returns level of message
     *
     * @return string  level of message
     */
    public function getLevel()
    {
        return CIOINA_Message::$level[$this->getNumber()];
    }

    /**
     * Displays the message in HTML
     *
     * @return void
     */
    public function display()
    {
        echo $this->getDisplay();
        $this->isDisplayed(true);
    }

    /**
     * returns HTML code for displaying this message
     *
     * @return string whole message box
     */
    public function getDisplay()
    {
        $this->isDisplayed(true);
        return '<div class="' . $this->getLevel() . '">'
            . $this->getMessage() . '</div>';
    }

    /**
     * sets and returns whether the message was displayed or not
     *
     * @param boolean $isDisplayed whether to set displayed flag
     *
     * @return boolean CIOINA_Message::$isDisplayed
     */
    public function isDisplayed($isDisplayed = false)
    {
        if ($isDisplayed) {
            $this->isDisplayed = true;
        }

        return $this->isDisplayed;
    }

}
?>
