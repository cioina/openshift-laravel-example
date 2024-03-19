<?php $choices = []; ?>
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>Id</th>
        @foreach($options['choices'] as $header)
            <?php $choices = $header['choices']; ?>
            <th>

            <div class="input-group">
                <div class="icheck-inline">
                    <label>{!! $header['label'] !!} </label>
                </div>
            </div>

            </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
        @if(!empty($choices))
            <?php $iterator = 0; ?>
            @foreach($choices as $label=>$choice)
                <tr>
                    <td>{!! $label !!} </td>
                    @foreach($options['choices'] as $header)
                        <td>
                            <div class="input-group">
                                <div class="icheck-list">
                                    @foreach($choice as $ch)
                                        <label>
                                        {!! Form::checkbox($name.'['.$header['id'].'][]', $ch['id'], in_array($ch['id'],$options['selected'][$header['id']]), [
                                            'class'=>'icheck',
                                            'data-checkbox'=>(in_array($ch['id'],$options['selected'][$header['id']]))?'icheckbox_line-blue':'icheckbox_line-grey',
                                            'data-label'=>$ch['label'],
                                        ]) !!}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </td>
                    @endforeach
                </tr>
                <?php $iterator ++; ?>
            @endforeach
        @endif
    </tbody>
    </table>
</div>