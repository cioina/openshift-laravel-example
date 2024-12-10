@if(isset($code_block_route) and PermissionUtil::hasAccess($code_block_route.'getEdit'))
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($code_block_route.'getEdit', isset($code_block_data) && isset($code_block_data['id'])?$code_block_data['id']:'' ))}}" class="btn btn-sm yellow-casablanca filter-submit margin-bottom-10" style="width: 107px;"><i class="glyphicon glyphicon-edit"></i> {{trans('expendable::datatable.edit_code')}}</a>
@endif
@if(isset($image_route) and PermissionUtil::hasAccess($image_route.'getEdit'))
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($image_route.'getEdit', isset($image_data) && isset($image_data['id'])?$image_data['id']:'' ))}}" class="btn btn-sm yellow-saffron filter-submit margin-bottom-10"><i class="glyphicon glyphicon-edit"></i> {{trans('expendable::datatable.edit_image')}}</a>
@endif
@if(isset($video_route) and PermissionUtil::hasAccess($video_route.'getEdit'))
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($video_route.'getEdit', isset($video_data) && isset($video_data['id'])?$video_data['id']:'' ))}}" class="btn btn-sm yellow filter-submit margin-bottom-10"><i class="glyphicon glyphicon-edit"></i> {{trans('expendable::datatable.edit_video')}}</a>
@endif
@if(isset($topic_route) and PermissionUtil::hasAccess($topic_route.'getEdit'))
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($topic_route.'getEdit', isset($topic_data) && isset($topic_data['id'])?$topic_data['id']:'' ))}}" class="btn btn-sm yellow-gold filter-submit margin-bottom-10" style="width: 107px;"><i class="glyphicon glyphicon-edit"></i> {{trans('expendable::datatable.edit_topic')}}</a>
@endif
@if(isset($setting_route) and PermissionUtil::hasAccess($setting_route.'getEdit'))
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($setting_route.'getEdit', isset($setting_data) && isset($setting_data['id'])?$setting_data['id']:'' ))}}" class="btn btn-sm yellow-gold filter-submit margin-bottom-10" style="width: 107px;"><i class="glyphicon glyphicon-edit"></i> {{trans('expendable::datatable.edit_setting')}}</a>
@endif
