    <?php $id = uniqid(); ?>
    @if ($showField)
        <div {!! $options['wrapperAttrs']!!}  >
            @endif

            @if ($showLabel)
                <?php $options['label_attr']['class'] .= ' col-md-3'; ?>
                {!! Form::label($name, $options['label'], $options['label_attr']) !!}
            @endif

            <div class="col-md-8">
                <?php $options['attr']['class'] .= ' '.$id; ?>
                @if ($showField)
                    @if(isset($noEdit) and $noEdit === true)
                        {!! $options['default_value'] !!}
                    @else
                        {!! Form::textarea($name, $options['default_value'], $options['attr']) !!}
                    @endif
                @endif

                @if ($showError && isset($errors))
                    {!! $errors->first( Arr::get($options, 'real_name', $name), '<div '.$options['errorAttrs'].'>:message</div>') !!}
                @endif
            </div>
            @if ($showField)
        </div>
    @endif
    @if(empty($noEdit))
        <script type="text/javascript">
            $(function () {
                //https://www.tiny.cloud/docs/configure/editor-appearance/
                tinymce.remove(".{!!$id!!}");
                tinymce.init({
                    selector: ".{!!$id!!}",
                    plugins: [
                        "advlist autolink lists link image charmap hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen",
                        "insertdatetime nonbreaking save table directionality",
                        "paste textpattern"
                    ],
                    toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code image forecolor backcolor",
                    //image_advtab: true,
                    //relative_urls: false,
                    //body_class: "content",
                    //convert_urls: false,
                    branding: false,
                    min_height: 300,
                    height: 300,
                    //width : 300,
                    max_height: 600,
                    max_width: 600,
                    skin: "oxide",
                    theme: 'silver',
                    toolbar_drawer: 'floating',
                    menubar: 'file edit insert view format table tools help',
                    menu: {
                        file: { title: 'File', items: 'newdocument restoredraft | preview | print ' },
                        edit: { title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace' },
                        view: { title: 'View', items: 'code | visualaid visualchars visualblocks | spellchecker | preview fullscreen' },
                        insert: { title: 'Insert', items: 'image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime' },
                        format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align | forecolor backcolor | removeformat' },
                        tools: { title: 'Tools', items: 'spellchecker spellcheckerlanguage | code wordcount' },
                        table: { title: 'Table', items: 'inserttable tableprops deletetable row column cell' },
                        help: { title: 'Help', items: 'help' }
                    },
                    
                });
            });

        </script>
    @endif