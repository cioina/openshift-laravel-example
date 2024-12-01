$(function () {
    var Alpaca = $.alpaca;

    Alpaca.regexps.email = /^[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+(?:\.[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,6}$/i;
    Alpaca.regexps.date = /^((\d{2})\D?(\d{2})\D?(\d{4}))?$/;
    Alpaca.regexps.phone = /^(\D?(\d{3})\D?\D?(\d{3})\D?(\d{4}))?$/;
    Alpaca.regexps.password = /^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{2,})\S$/;
    Alpaca.regexps.number = /^([\+\-]?((([0-9_]+(\.)?)|([0-9]*\.[0-9]+))([eE][+-]?[0-9]+)?))$/;
    Alpaca.regexps.integer = /^([\+\-]?([1-9]\d*)|0)$/;

    Alpaca.registerMessages({
        "facebook_disabled": "We are sorry, but Facebook login is disabled at this time.",
        "throttle_login": "Too many login attempts. Come back in {0} minutes.",
        "wrong_login": "Incorrect login or password (ERROR: {0}.)",
        "activation_code_error": "This activation code is incorrect. Please check out your email and copy and paste the activation code.",
        "graph_error": "Something went wrong {0}",
        "incorrect_today_date": "Please make sure you entered today&#039;s date from Greenwich Mean Time (GMT) time zone.",
        "total_guest_emails_limit": "We are sorry, but we cannot accept more guest emails today. Please come back tomorrow.",
        "json_validation": "Some JSON validations for {0} did not pass.",
        "system_error": "We encountered a system error. Tell our web programmer to drink a little less.",
        "not_numeric_parameters": "A numeric query parameter is expected.",
        "empty_parameters": "Some query parameters are empty.",
        "fetch_result_count": "Cannot find your record. Try to refresh the page or try to log in.",
        "json_decode_exception": "Cannot decode your JSON.",
        "no_session_keys": "Your session has been expired. Click on Home page and try again.",
        "no_us_states_selected": "There are no US states selected. Please read carefully &quot;Are you a web robot?&quot;",
        "wrong_us_states_count": "You selected too many or too few US states. Please read carefully &quot;Are you a web robot?&quot; Possible you have opened multiple tabs for this form. If so, try to refresh current page.",
        "wrong_us_states": "You selected some wrong US states. Please read carefully &quot;Are you a web robot?&quot; Possible you have opened multiple tabs for this form. If so, try to refresh current page.",
        "wrong_session": "Your session has been expired. Possible you have opened multiple tabs for this form. If so, try to refresh current page.",
        "wrong_id_parameter": "Wrong Id Parameter.",

        "name_validation_hyphen": "Incorrect position for hyphen (-).",
        "name_validation_apostrophe": "Incorrect position for apostrophe (&#039;).",
        "name_validation_incorrect_name": "Incorrect name",
        "name_validation_english_alphabet": "Each word must have more than one letter of English alphabet and be separated by space, hyphen (-), or apostrophe (&#039;).",

        "stringTooShortAfterTrimming": "This field should contain at least {0} characters after trimming.",
        "invalidDateRangeDef": "The date and the range {0} and {1} must be real dates.",
        "invalidDateRange": "The date must be in range {0} {1} and {2} {3}. Here, square brackets [ and ] mean inclusive.",
        "updateMaxLengthIndicatorMessage1": "You have {0} characters remaining from {1}.",
        "updateMaxLengthIndicatorMessage2": "Your message is too long by {0} characters.",
        "disallowValue": "{0} is not allowed.",
        "notOptional": "This field is required.",
        "invalidValueOfEnum": "You must choose at least one.",
        "invalidPattern": "This field {1} should have pattern {0}.",
        "stringTooShort": "This field should contain at least {0} characters.",
        "stringTooLong": "This field should contain no more than {0} characters.",
        "wordLimitExceeded": "The maximum word limit of {0} has been exceeded.",
        "invalidEmail": "Incorrect email format.",
        "invalidDate": "The correct date format is {0}",
        "stringNotADate": "It must be a real date formated as {0}",
        "invalidPhone": "The correct format is {0}",
        "invalidPassword": "Your password must contain at least one upper case letter, one lower case letter, one number, and no spaces.",
        "stringValueTooSmall": "The minimum value for this field is {0}.",
        "stringValueTooLarge": "The maximum value for this field is {0}.",
        "stringValueTooSmallExclusive": "Value of this field must be greater than {0}.",
        "stringValueTooLargeExclusive": "Value of this field must be less than {0}.",
        "stringDivisibleBy": "The value must be divisible by {0}.",
        "stringNotANumber": "It must be a number.",
        "stringValueNotMultipleOf": "This value is not a multiple of {0}.",
        "stringNotAnInteger": "This value is not an integer.",
        "tooManyProperties": "The maximum number of properties {0} has been exceeded.",
        "tooFewProperties": "There are not enough properties. Minimum of {0} properties are required.",
        "noneLabel": "None"
    });
});
