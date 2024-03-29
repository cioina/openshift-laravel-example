@if ($showLabel && $showField)
<div {!! $options['wrapperAttrs']!!}  >
@endif

    @if ($showLabel)
     <?php $options['label_attr']['class'] .= ' col-md-3'; ?>
    {!! Form::label($name, $options['label'], $options['label_attr']) !!}
    @endif

    <div class="col-md-8">
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
        @if(isset($options['help']))
        <span class="help-block">{!!$options['help']!!} </span>
        @endif
     </div>
@if ($showLabel && $showField)
</div>
@endif