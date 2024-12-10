$(function () {
    var Alpaca = $.alpaca;

    Alpaca.regexps.email = /^[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+(?:\.[a-z0-9!\#\$%&'\*\-\/=\?\+\-\^_`\{\|\}~]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,6}$/i;
    Alpaca.regexps.date = /^((\d{2})\D?(\d{2})\D?(\d{4}))?$/;
    Alpaca.regexps.phone = /^(\D?(\d{3})\D?\D?(\d{3})\D?(\d{4}))?$/;
    Alpaca.regexps.password = /^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{2,})\S$/;
    Alpaca.regexps.number = /^([\+\-]?((([0-9_]+(\.)?)|([0-9]*\.[0-9]+))([eE][+-]?[0-9]+)?))$/;
    Alpaca.regexps.integer = /^([\+\-]?([1-9]\d*)|0)$/;

    Alpaca.registerMessages({
        "facebook_disabled": "Nous sommes désolés, mais la connexion à Facebook est désactivée pour le moment.",
        "throttle_login": "Trop de tentatives de connexion. Revenez dans {0} minutes.",
        "wrong_login": "Identifiant ou mot de passe incorrect (ERROR: {0}.)",
        "activation_code_error": "Ce code d&#039;activation est incorrect. Veuillez vérifier votre email et copier et coller le code d&#039;activation.",
        "graph_error": "Quelque chose s&#039;est mal passé {0}",
        "incorrect_today_date": "Assurez-vous que vous avez entré la date d&#039;aujourd&#039;hui à partir du fuseau horaire Greenwich Mean Time (GMT).",
        "total_guest_emails_limit": "Nous sommes désolés, mais nous ne pouvons pas accepter plus de courriels d&#039;invité aujourd&#039;hui. Revenez demain.",
        "json_validation": "Certaines validations JSON pour {0} n&#039;ont pas été adoptées.",
        "system_error": "Nous avons rencontré une erreur système. Dites à notre programmeur Web de boire un peu moins.",
        "not_numeric_parameters": "Un paramètre de requête numérique est attendu.",
        "empty_parameters": "Certains paramètres de requête sont vides.",
        "fetch_result_count": "Ne peut pas trouver votre enregistrement. Essayez d&#039;actualiser la page.",
        "json_decode_exception": "Impossible de décoder votre JSON.",
        "no_session_keys": "Ce site nécessite que les cookies de session soient activés (ERROR: {0}.)",
        "no_us_states_selected": "Il n&#039;y a aucun états américains sélectionnés. Veuillez lire attentivement &quot;Êtes-vous un robot web?&quot;",
        "wrong_us_states_count": "Votre sélection de trop nombreux ou trop peu d&#039;états américains. Veuillez lire attentivement &quot;Êtes-vous un robot web?&quot; Vous avez peut-être ouvert plusieurs onglets pour ce formulaire. Essayez de rafraîchir la page actuelle.",
        "wrong_us_states": "Votre sélection de certains états américains erronés. Veuillez lire attentivement &quot;Êtes-vous un robot web?&quot; Vous avez peut-être ouvert plusieurs onglets pour ce formulaire. Essayez de rafraîchir la page actuelle.",
        "wrong_session": "Votre session a expiré. Vous avez peut-être ouvert plusieurs onglets pour ce formulaire. Essayez de rafraîchi la page actuelle.",
        "wrong_id_parameter": "Paramètre d&#039;identification erronée.",

        "name_validation_hyphen": "Position incorrecte pour le tiret (-).",
        "name_validation_apostrophe": "Position incorrecte pour l&#039;apostrophe (&#039;).",
        "name_validation_incorrect_name": "Nom incorrect",
        "name_validation_english_alphabet": "Chaque mot doit comporter plus d&#039;une lettre d&#039;alphabet anglais et être séparé par l&#039;espace, le tiret (-) ou l&#039;apostrophe (&#039;).",

        "stringTooShortAfterTrimming": "Ce champ doit contenir au moins {0} caractères après la coupe.",
        "invalidDateRangeDef": "La date et la plage {0} et {1} doivent être des dates réelles.",
        "invalidDateRange": "La date doit être dans la plage {0} {1} et {2} {3}. Ici, [ et ] signifie inclusivement.",
        "updateMaxLengthIndicatorMessage1": "Vous avez {0} caractères restant de {1}.",
        "updateMaxLengthIndicatorMessage2": "Votre message est trop long par {0} caractères.",
        "disallowValue": "{0} n&#039;est pas autorisé.",
        "notOptional": "Ce champ est requis.",
        "invalidValueOfEnum": "Vous devez choisir au moins un.",
        "invalidPattern": "Ce champ {1} doit avoir un motif {0}.",
        "stringTooShort": "Ce champ doit contenir au moins {0} caractères.",
        "stringTooLong": "Ce champ ne doit pas contenir plus de {0} caractères.",
        "wordLimitExceeded": "La limite maximale de mot de {0} a été dépassée.",
        "invalidEmail": "Le format correct est blablabla@blabla.bla",
        "invalidDate": "Le format de date correct est {0}",
        "stringNotADate": "Il doit s&#039;agir d&#039;une date réelle formatée comme {0}",
        "invalidPhone": "Le format correct est {0}",
        "invalidPassword": "Mot de passe invalide",
        "stringValueTooSmall": "La valeur minimale pour ce champ est {0}.",
        "stringValueTooLarge": "La valeur maximale pour ce champ est {0}.",
        "stringValueTooSmallExclusive": "La valeur de ce champ doit être supérieure à {0}.",
        "stringValueTooLargeExclusive": "La valeur de ce champ doit être inférieure à {0}.",
        "stringDivisibleBy": "La valeur doit être divisible par {0}.",
        "stringNotANumber": "Il doit s&#039;agir d&#039;un nombre.",
        "stringValueNotMultipleOf": "Cette valeur n&#039;est pas un multiple de {0}.",
        "stringNotAnInteger": "Cette valeur n&#039;est pas un nombre entier.",
        "tooManyProperties": "Le nombre maximum de propriétés {0} a été dépassé.",
        "tooFewProperties": "Il n&#039;y a pas assez de propriétés. Le minimum de {0} les propriétés sont nécessaires.",
        "noneLabel": "None"
    });
});
