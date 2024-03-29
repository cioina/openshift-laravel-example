@section('state.menu')
<?php
$controller = preg_split("/@/", Route::current()->getActionName());
$actionName = is_array($controller) ? $controller[1] : $controller;
?>
<div class="tiles pull-right">

    @foreach($states as $state)
       
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($action.$state['action']))  }}">
        <div class="tile {{ isset($state['color'])?$state['color']:'' }} {{$actionName ==$state['action']?'selected':'' }}">
            @if($actionName ==$state['action'])
                   
            <div class="corner"></div>
            <div class="check"></div>
            @endif
               
            <div class="tile-body">
                @if(isset($state['icon']))
                       
                <i class="glyphicon glyphicon-{{ $state['icon'] }}"></i>
                @endif
               
            </div>
            <div class="tile-object">
                <div class="text-center">
                    {{ trans($state['label']) }}
                   
                </div>
                <div class="number"></div>
            </div>
        </div>
    </a>
    @endforeach
</div>
<div class="clearfix"></div>
@stop