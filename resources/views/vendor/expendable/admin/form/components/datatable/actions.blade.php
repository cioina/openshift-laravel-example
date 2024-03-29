@if(PermissionUtil::hasAccess($route.'getView'))
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($route.'getView',$data['id'])) }}" class="btn btn-sm blue filter-submit margin-bottom-10" style="width: 85px;"><i class="glyphicon glyphicon-zoom-in"></i> {{trans('expendable::datatable.view')}}</a>
@endif
@if(PermissionUtil::hasAccess($route.'getEdit'))
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($route.'getEdit',$data['id'])) }}" class="btn btn-sm yellow filter-submit margin-bottom-10" style="width: 85px;"><i class="glyphicon glyphicon-edit"></i> {{trans('expendable::datatable.edit')}}</a>
@endif
@if(PermissionUtil::hasAccess($route.'putDestroy'))
        {!! Form::open([
        'url' => \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($route.'putDestroy')),
        'method' => 'put',
        'class'=>'form-inline']) !!}
        {!! Form::hidden('id',$data['id']) !!}
        {!! Form::button('<i class="glyphicon glyphicon-trash"></i> '.trans('expendable::datatable.remove'),[
            "type"=>"submit",
            "data-toggle"=>"confirmation",
            "data-placement"=>"left",
            "data-singleton"=>"false",
            "data-btn-cancel-label"=>trans('expendable::datatable.no'),
            "data-btn-ok-label"=>trans('expendable::datatable.yes'),
            "data-title"=>trans('expendable::datatable.are_you_sure'),
            "class"=>"btn btn-sm red filter-submit margin-bottom-10",
        ]) !!}
        {!! Form::close() !!}
@endif