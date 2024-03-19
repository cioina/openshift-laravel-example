            var Alpaca = $.alpaca;

            Alpaca.regexps.email = /^[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+(?:\.[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,6}$/i;
            Alpaca.regexps.date = /^((\d{2})\D?(\d{2})\D?(\d{4}))?$/;
            Alpaca.regexps.phone = /^(\D?(\d{3})\D?\D?(\d{3})\D?(\d{4}))?$/;
            Alpaca.regexps.password = /^[0-9a-zA-Z\x20-\x7E]*$/;
            Alpaca.regexps.number = /^([\+\-]?((([0-9]+(\.)?)|([0-9]*\.[0-9]+))([eE][+-]?[0-9]+)?))$/;
            Alpaca.regexps.integer = /^([\+\-]?([1-9]\d*)|0)$/;

            Alpaca.registerMessages({
                "name_validation_hyphen": "{{ trans( 'expendable::alpaca.name_validation_hyphen' ) }}",
                "name_validation_apostrophe": "{{ trans( 'expendable::alpaca.name_validation_apostrophe' ) }}",
                "name_validation_incorrect_name": "{{ trans( 'expendable::alpaca.name_validation_incorrect_name' ) }}",
                "name_validation_english_alphabet": "{{ trans( 'expendable::alpaca.name_validation_english_alphabet' ) }}",
                
                "stringTooShortAfterTrimming": "{{ trans( 'expendable::alpaca.stringTooShortAfterTrimming' ) }}",                 
                "invalidDateRangeDef": "{{ trans( 'expendable::alpaca.invalidDateRangeDef' ) }}",                
                "invalidDateRange": "{{ trans( 'expendable::alpaca.invalidDateRange' ) }}",
                "updateMaxLengthIndicatorMessage1": "{{ trans( 'expendable::alpaca.updateMaxLengthIndicatorMessage1' ) }}",
                "updateMaxLengthIndicatorMessage2": "{{ trans( 'expendable::alpaca.updateMaxLengthIndicatorMessage2' ) }}",
                "disallowValue": "{{ trans( 'expendable::alpaca.disallowValue' ) }}",
                "notOptional": "{{ trans( 'expendable::alpaca.notOptional' ) }}",
                "invalidValueOfEnum": "{{ trans( 'expendable::alpaca.invalidValueOfEnum' ) }}",
                "invalidPattern": "{{ trans( 'expendable::alpaca.invalidPattern' ) }}",
                "stringTooShort": "{{ trans( 'expendable::alpaca.stringTooShort' ) }}",
                "stringTooLong": "{{ trans( 'expendable::alpaca.stringTooLong' ) }}",
                "wordLimitExceeded": "{{ trans( 'expendable::alpaca.wordLimitExceeded' ) }}",
                "invalidEmail": "{{ trans( 'expendable::alpaca.invalidEmail' ) }}",
                "invalidDate": "{{ trans( 'expendable::alpaca.invalidDate' ) }}",
                "stringNotADate": "{{ trans( 'expendable::alpaca.stringNotADate' ) }}",
                "invalidPhone": "{{ trans( 'expendable::alpaca.invalidPhone' ) }}",
                "invalidPassword": "{{ trans( 'expendable::alpaca.invalidPassword' ) }}",
                "stringValueTooSmall": "{{ trans( 'expendable::alpaca.stringValueTooSmall' ) }}",
                "stringValueTooLarge": "{{ trans( 'expendable::alpaca.stringValueTooLarge' ) }}",
                "stringValueTooSmallExclusive": "{{ trans( 'expendable::alpaca.stringValueTooSmallExclusive' ) }}",
                "stringValueTooLargeExclusive": "{{ trans( 'expendable::alpaca.stringValueTooLargeExclusive' ) }}",
                "stringDivisibleBy": "{{ trans( 'expendable::alpaca.stringDivisibleBy' ) }}",
                "stringNotANumber": "{{ trans( 'expendable::alpaca.stringNotANumber' ) }}",
                "stringValueNotMultipleOf": "{{ trans( 'expendable::alpaca.stringValueNotMultipleOf' ) }}",
                "stringNotAnInteger": "{{ trans( 'expendable::alpaca.stringNotAnInteger' ) }}",
                "tooManyProperties": "{{ trans( 'expendable::alpaca.tooManyProperties' ) }}",
                "tooFewProperties": "{{ trans( 'expendable::alpaca.tooFewProperties' ) }}",
                "noneLabel": "{{ trans( 'expendable::alpaca.noneLabel' ) }}"
            });
