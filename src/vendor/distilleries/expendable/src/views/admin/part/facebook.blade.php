@section('content')
    <h3 style="text-align: center;"><a href="{{ config('app.url') }}" target="_self">{{ trans('expendable::form.facebook_click_home') }}</a></h3>
<div id="loading_{{ $id }}" style="text-align: center;">
    <img src="{{ Config::get('app.url').'/assets/admin/css/images/cb/loading.gif' }}" alt="{{ trans($modalMessage) }}" />
</div>
<div id="form1_{{ $id }}" style="text-align: center;">
</div>
<div id="form2_{{ $id }}" class="hidden">
</div>
    <script type="text/javascript">
        $(function () {
            "use strict";
            var
            modal,
            $form2 = $("#form2_{{ $id }}"),
            $loading = $("#loading_{{ $id }}"),
            json1 = {
                "data": {
                    "info": ""
                },
                "options": {
                    "fields": {
                        "info": {
                            "name": "info",
                            "type": "textarea"
                        }
                    }
                },
                "schema": {
                    "title": "",
                    "description": "",
                    "type": "object",
                    "properties": {
                        "info": {
                            "title": "",
                            "description": "",
                            "type": "string",
                        }
                    }
                },
                "view": "web-display",
                "postRender": ""
            },
            createPrompt = function (title, message) {
                $form2.removeClass("hidden");

                json1.data.info = message;
                json1.data.title = title;

                $form2.alpaca(json1);
            },

             json2 = {
                 "data": {
                     "info": "{{ trans( 'expendable::form.facebook_data_must_have' ) }}",
                     "promptTitle": "{{ trans($modalTitle) }}",
                     "promptMessage": "{{ trans($modalMessage) }}",
                     "writeMessage": "{{ trans($closeMessage) }}",
                     "facebookUrl": "{!! $facebookLink !!}"
                 },
                 "options": {
                     "hideInitValidationError": true,
                     "focus": false,
                     "form": {
                         "toggleSubmitValidState": false,
                         "buttons": {
                             "goFacebookLogin": {
                                 "title": "{{ trans($buttonText) }}",
                                 "click": ""
                             },

                             "goClose": {
                                 "title": "{{ trans( 'expendable::form.facebook_button_close' ) }}",
                                 "click": ""
                             }
                         }
                     },
                     "fields": {
                         "info": {
                             "name": "info",
                             "type": "textarea"
                         }
                     }
                 },
                 "schema": {
                     "title": "{{ trans($titleForm) }}",
                     "description": "",
                     "type": "object",
                     "properties": {
                         "info": {
                             "title": "{{ trans( 'expendable::form.facebook_info' ) }}",
                             "description": "",
                             "type": "string",
                         }
                     }
                 },
                 "view": "web-display",
                 "postRender": ""
             };

            json2.options.form.buttons.goFacebookLogin.click = function () {
                $.acioina.destroyAlpacaForm(0);
                createPrompt(json2.data.promptTitle, json2.data.promptMessage);
                window.location = json2.data.facebookUrl;
            };

            json2.options.form.buttons.goClose.click = function () {
                $.acioina.writeFormMessage(0, json2.data.writeMessage, "#form1_{{ $id }}");
            };

            json2.postRender = function (f) {
                json1.postRender = function (m) {
                    modal = m;
                    $.acioina($.acioina.showPrompt(
                        $form2, function () {
                            $form2.addClass("hidden");
                            modal.destroy();
                            $loading.removeClass("hidden");
                        }));
                };

                $.acioina.setAlpacaForm(0, f);
                if (!$loading.hasClass("hidden")) {
                    $loading.addClass("hidden");
                }
            };

            $("#form1_{{ $id }}").alpaca(json2);
        });
    </script>
@stop