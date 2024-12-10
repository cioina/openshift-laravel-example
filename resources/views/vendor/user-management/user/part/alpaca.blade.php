            var Alpaca = $.alpaca;

            Alpaca.regexps.email = /^[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+(?:\.[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,6}$/i;
            Alpaca.regexps.date = /^((\d{2})\D?(\d{2})\D?(\d{4}))?$/;
            Alpaca.regexps.phone = /^(\D?(\d{3})\D?\D?(\d{3})\D?(\d{4}))?$/;
            Alpaca.regexps.password = /^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{2,})\S$/;
            Alpaca.regexps.number = /^([\+\-]?((([0-9_]+(\.)?)|([0-9]*\.[0-9]+))([eE][+-]?[0-9]+)?))$/;
            Alpaca.regexps.integer = /^([\+\-]?([1-9]\d*)|0)$/;

            Alpaca.registerMessages({
                "facebook_disabled": "{{ trans( 'user-management::alpaca.facebook_disabled' ) }}",
                "throttle_login": "{{ trans( 'user-management::alpaca.throttle_login' ) }}",
                "wrong_login": "{{ trans( 'user-management::alpaca.wrong_login' ) }}",                
                "activation_code_error": "{{ trans( 'user-management::alpaca.activation_code_error' ) }}",                
                "graph_error": "{{ trans( 'user-management::alpaca.graph_error' ) }}",
                "incorrect_today_date": "{{ trans( 'user-management::alpaca.incorrect_today_date' ) }}",
                "total_guest_emails_limit": "{{ trans( 'user-management::alpaca.total_guest_emails_limit' ) }}",
                "json_validation": "{{ trans( 'user-management::alpaca.json_validation' ) }}",
                "system_error": "{{ trans( 'user-management::alpaca.system_error' ) }}",
                "not_numeric_parameters": "{{ trans( 'user-management::alpaca.not_numeric_parameters' ) }}",
                "empty_parameters": "{{ trans( 'user-management::alpaca.empty_parameters' ) }}",
                "fetch_result_count": "{{ trans( 'user-management::alpaca.fetch_result_count' ) }}",
                "json_decode_exception": "{{ trans( 'user-management::alpaca.json_decode_exception' ) }}",
                "no_session_keys": "{{ trans( 'user-management::alpaca.no_session_keys' ) }}",
                "no_us_states_selected": "{{ trans( 'user-management::alpaca.no_us_states_selected' ) }}",
                "wrong_us_states_count": "{{ trans( 'user-management::alpaca.wrong_us_states_count' ) }}",
                "wrong_us_states": "{{ trans( 'user-management::alpaca.wrong_us_states' ) }}",
                "wrong_session": "{{ trans( 'user-management::alpaca.wrong_session' ) }}",
                "wrong_id_parameter": "{{ trans( 'user-management::alpaca.wrong_id_parameter' ) }}",

                "name_validation_hyphen": "{{ trans( 'user-management::alpaca.name_validation_hyphen' ) }}",
                "name_validation_apostrophe": "{{ trans( 'user-management::alpaca.name_validation_apostrophe' ) }}",
                "name_validation_incorrect_name": "{{ trans( 'user-management::alpaca.name_validation_incorrect_name' ) }}",
                "name_validation_english_alphabet": "{{ trans( 'user-management::alpaca.name_validation_english_alphabet' ) }}",

                "stringTooShortAfterTrimming": "{{ trans( 'user-management::alpaca.stringTooShortAfterTrimming' ) }}",                 
                "invalidDateRangeDef": "{{ trans( 'user-management::alpaca.invalidDateRangeDef' ) }}",                
                "invalidDateRange": "{{ trans( 'user-management::alpaca.invalidDateRange' ) }}",
                "updateMaxLengthIndicatorMessage1": "{{ trans( 'user-management::alpaca.updateMaxLengthIndicatorMessage1' ) }}",
                "updateMaxLengthIndicatorMessage2": "{{ trans( 'user-management::alpaca.updateMaxLengthIndicatorMessage2' ) }}",
                "disallowValue": "{{ trans( 'user-management::alpaca.disallowValue' ) }}",
                "notOptional": "{{ trans( 'user-management::alpaca.notOptional' ) }}",
                "invalidValueOfEnum": "{{ trans( 'user-management::alpaca.invalidValueOfEnum' ) }}",
                "invalidPattern": "{{ trans( 'user-management::alpaca.invalidPattern' ) }}",
                "stringTooShort": "{{ trans( 'user-management::alpaca.stringTooShort' ) }}",
                "stringTooLong": "{{ trans( 'user-management::alpaca.stringTooLong' ) }}",
                "wordLimitExceeded": "{{ trans( 'user-management::alpaca.wordLimitExceeded' ) }}",
                "invalidEmail": "{{ trans( 'user-management::alpaca.invalidEmail' ) }}",
                "invalidDate": "{{ trans( 'user-management::alpaca.invalidDate' ) }}",
                "stringNotADate": "{{ trans( 'user-management::alpaca.stringNotADate' ) }}",
                "invalidPhone": "{{ trans( 'user-management::alpaca.invalidPhone' ) }}",
                "invalidPassword": "{{ trans( 'user-management::alpaca.invalidPassword' ) }}",
                "stringValueTooSmall": "{{ trans( 'user-management::alpaca.stringValueTooSmall' ) }}",
                "stringValueTooLarge": "{{ trans( 'user-management::alpaca.stringValueTooLarge' ) }}",
                "stringValueTooSmallExclusive": "{{ trans( 'user-management::alpaca.stringValueTooSmallExclusive' ) }}",
                "stringValueTooLargeExclusive": "{{ trans( 'user-management::alpaca.stringValueTooLargeExclusive' ) }}",
                "stringDivisibleBy": "{{ trans( 'user-management::alpaca.stringDivisibleBy' ) }}",
                "stringNotANumber": "{{ trans( 'user-management::alpaca.stringNotANumber' ) }}",
                "stringValueNotMultipleOf": "{{ trans( 'user-management::alpaca.stringValueNotMultipleOf' ) }}",
                "stringNotAnInteger": "{{ trans( 'user-management::alpaca.stringNotAnInteger' ) }}",
                "tooManyProperties": "{{ trans( 'user-management::alpaca.tooManyProperties' ) }}",
                "tooFewProperties": "{{ trans( 'user-management::alpaca.tooFewProperties' ) }}",
                "noneLabel": "{{ trans( 'user-management::alpaca.noneLabel' ) }}"
            });
