
@if($type == 'hidden')
    {!! Form::input($type, $name, $options['default_value'], $options['attr']) !!}
@else
@if ($showField && !$options['is_child'])
    <div {!! $options['wrapperAttrs'] !!}  >
    @endif

    @if ($showLabel)
    <?php $options['label_attr']['class'] .= ' col-md-3'; ?>
        {!! Form::label($name, $options['label'], $options['label_attr']) !!}
    @endif

    <?php $textClass = isset($options['is_title']) ? 'col-md-8' : 'col-md-4'; ?>
    <div class= "<?php  echo $textClass; ?>" >
    @if ($showField)
        @if(isset($noEdit) and $noEdit === true)
            @if (isset($options['is_title']))
                <div style="text-align: center;">
            @endif
        
            {!!$options['default_value'] !!}

            @if (isset($options['is_title']))
                </div>
            @endif
        @else
            {!! Form::input($type, $name, $options['default_value'], $options['attr']) !!}
        @endif
    @endif

    @if ($showError && isset($errors))
        {!!$errors->first( Arr::get($options, 'real_name', $name), '<span '.$options['errorAttrs'].'>:message</span>')!!}
    @endif
    @if(isset($options['help']))
    <span class="help-block">{!!$options['help']!!} </span>
    @endif

    </div>
    @if ($showField && !$options['is_child'])
    </div>
@endif

@endif
