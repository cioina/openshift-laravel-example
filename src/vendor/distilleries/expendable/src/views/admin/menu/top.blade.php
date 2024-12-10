@section('menu_top')
<?php 
    $languages = \Distilleries\Expendable\Models\Language::all(); 
    $tasks = Config::get('expendable.menu.tasks');
?>
<div class="page-header navbar">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="{{ Config::get('app.url') }}">
                <img src="{{ Config::get('app.url').'/assets/admin/img/logo.png' }}" alt="" class="logo-default" height="24" />
            </a>
            <div class="menu-toggler sidebar-toggler hide"></div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"></a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <span class="username">{{ \Distilleries\Expendable\Helpers\UserUtils::getDisplayName() }}</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(route('user.profile')) }}">
                                <i class="icon-user"></i>{{ trans('expendable::menu.my_profile') }}</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(route('login.logout')) }}">
                                <i class="icon-key"></i>{{ trans('expendable::menu.log_out') }}</a>
                        </li>
                    </ul>
                </li>

                
                @if(!empty($languages))
                <li class="dropdown dropdown-extended dropdown-notification">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <?php 
                        $locale = app()->getLocale();
                        $flag = $locale ;
                        foreach($languages as $language)
                        {
                            $lang = explode('_', $language->iso);
                            $iso = strtolower($lang[0]);
                            $flag = isset($lang[1]) ? strtolower($lang[1]) : strtolower($lang[0]);
                            if( $iso === $locale )
                            {
                                break; 
                            }
                        }
                        ?>
                        <span class="flags-sprite flags-{{ $flag  }}"></span>
                        <span class="badge badge-grey">{{ count($languages) }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($languages as $language)
                                <?php
                                $lang = explode('_', $language->iso);
                                $iso = isset($lang[1]) ? strtolower($lang[1]) : strtolower($lang[0]);
                                ?>
                        <li>
                            <a href="{{ config('app.url') }}/{{ config('expendable.admin_base_uri') }}/set-lang/{{ $lang[0] }}">
                                <span class="details">
                                    <span class="flags-sprite flags-{{ $iso }}"></span>
                                    {{ $language['label'] }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endif

                @if(!empty($tasks))
				<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="glyphicon glyphicon-tasks"></i>
                        <span class="badge badge-default">{{ count($tasks) }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($tasks as $task)
                             @if(PermissionUtil::hasAccess($task['action']))
                                <li>
                                    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($task['action'])) }}" target="_blank">
                                        <span class="details">
                                            <span class="label label-sm label-icon label-success">
                                                <i class="glyphicon glyphicon-{{ $task['icon'] }}"></i>
                                            </span>
                                            {{ trans($task['label']) }}
                                        </span>
                                    </a>
                                </li>
                              @endif
                        @endforeach
                    </ul>
                </li>
                @endif

            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
@stop