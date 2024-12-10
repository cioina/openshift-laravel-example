
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

    <div class= "col-md-8" >
    @if ($showField)
        @if(isset($noEdit) and $noEdit === true)
        {!!$options['default_value'] !!}
        @endif
    @endif
    </div>
    @if ($showField && !$options['is_child'])
    </div>
@endif

@endif
