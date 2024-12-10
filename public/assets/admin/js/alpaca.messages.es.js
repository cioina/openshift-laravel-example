$(function () {
    var Alpaca = $.alpaca;

    Alpaca.regexps.email = /^[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+(?:\.[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,6}$/i;
    Alpaca.regexps.date = /^((\d{2})\D?(\d{2})\D?(\d{4}))?$/;
    Alpaca.regexps.phone = /^(\D?(\d{3})\D?\D?(\d{3})\D?(\d{4}))?$/;
    Alpaca.regexps.password = /^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{2,})\S$/;
    Alpaca.regexps.number = /^([\+\-]?((([0-9_]+(\.)?)|([0-9]*\.[0-9]+))([eE][+-]?[0-9]+)?))$/;
    Alpaca.regexps.integer = /^([\+\-]?([1-9]\d*)|0)$/;

    Alpaca.registerMessages({
        "facebook_disabled": "Lo sentimos, pero el inicio de sesión de Facebook está deshabilitado en este momento.",
        "throttle_login": "Demasiados intentos de inicio de sesión. Vuelve en {0} minutos.",
        "wrong_login": "Nombre de usuario o contraseña incorrectos (ERROR: {0}.)",
        "activation_code_error": "Este código de activación es incorrecto. Por favor revise su correo electrónico y copie y pegue el código de activación.",
        "graph_error": "Algo salió mal {0}",
        "incorrect_today_date": "Asegúrese de haber ingresado la fecha de hoy desde la zona horaria de Greenwich Mean Time (GMT).",
        "total_guest_emails_limit": "Lo sentimos, pero no podemos aceptar más correos electrónicos de invitados hoy. Por favor, vuelve mañana.",
        "json_validation": "Algunas validaciones de JSON para {0} no pasaron.",
        "system_error": "Encontramos un error del sistema. Dile a nuestro programador web que beba un poco menos. ",
        "not_numeric_parameters": "Se espera un parámetro numérico de consulta.",
        "empty_parameters": "Algunos parámetros de consulta están vacíos.",
        "fetch_result_count": "No puede encontrar su registro. Intente actualizar la página",
        "json_decode_exception": "No se puede descifrar su JSON.",
        "no_session_keys": "Este sitio web requiere que las cookies de sesión estén habilitadas (ERROR: {0}.)",
        "no_us_states_selected": "No hay estados de EE. UU. Seleccionados. Lea atentamente ¿Es usted un robot web?",
        "wrong_us_states_count": "Su selección de demasiados o muy pocos estados de los Estados Unidos. Por favor lea cuidadosamente ¿Es usted un robot? Es posible que haya abierto varias pestañas para este formulario de contacto. Intente actualizar la página actual.",
        "wrong_us_states": "Se han seleccionado algunos Estados Unidos equivocados. Por favor lea cuidadosamente ¿Es usted un robot? Es posible que haya abierto varias pestañas para este formulario. Intente actualizar la página actual.",
        "wrong_session": "Su sesión ha expirado. Es posible que haya abierto varias pestañas para este formulario. Intente actualizar la página actual.",
        "wrong_id_parameter": "Parámetro de ID equivocado.",

        "name_validation_hyphen": "Posición incorrecta para el guión (-).",
        "name_validation_apostrophe": "Posición incorrecta para el apóstrofo (&#039;).",
        "name_validation_incorrect_name": "Nombre incorrecto",
        "name_validation_english_alphabet": "Cada palabra debe tener más de una letra del alfabeto inglés y estar separada por espacio, guión (-) o apóstrofo (&#039;).",

        "stringTooShortAfterTrimming": "Este campo debe contener al menos {0} caracteres después del recorte.",
        "invalidDateRangeDef": "La fecha y el rango {0} y {1} deben ser fechas reales.",
        "invalidDateRange": "La fecha debe estar en el rango {0} {1} y {2} {3}. Aquí, [ y ] significa inclusivo.",
        "updateMaxLengthIndicatorMessage1": "Tiene {0} caracteres restantes de {1}.",
        "updateMaxLengthIndicatorMessage2": "Tu mensaje es demasiado largo por {0} caracteres.",
        "disallowValue": "{0} no está permitido.",
        "notOptional": "Este campo es obligatorio.",
        "invalidValueOfEnum": "Debes elegir al menos uno.",
        "invalidPattern": "Este campo {1} debería tener patrón {0}.",
        "stringTooShort": "Este campo debe contener al menos {0} caracteres.",
        "stringTooLong": "Este campo no debe contener más de {0} caracteres.",
        "wordLimitExceeded": "Se ha superado el límite máximo de palabras de {0}.",
        "invalidEmail": "El formato correcto es blablabla@blabla.bla",
        "invalidDate": "El formato de fecha correcto es {0}",
        "stringNotADate": "Debe ser una fecha real formateada como {0}",
        "invalidPhone": "El formato correcto es {0}",
        "invalidPassword": "Contraseña no válida",
        "stringValueTooSmall": "El valor mínimo para este campo es {0}.",
        "stringValueTooLarge": "El valor máximo para este campo es {0}.",
        "stringValueTooSmallExclusive": "El valor de este campo debe ser mayor que {0}.",
        "stringValueTooLargeExclusive": "El valor de este campo debe ser menor que {0}.",
        "stringDivisibleBy": "El valor debe ser divisible por {0}.",
        "stringNotANumber": "Debe ser un número.",
        "stringValueNotMultipleOf": "Este valor no es un múltiplo de {0}.",
        "stringNotAnInteger": "Este valor no es un entero.",
        "tooManyProperties": "Se ha superado el número máximo de propiedades {0}.",
        "tooFewProperties": "No hay suficientes propiedades. Se requiere un mínimo de {0} propiedades.",
        "noneLabel": "Ninguno"
    });
});