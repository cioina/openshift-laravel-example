@section('menu_left')
<?php 
$collapsed = Config::get('user-management.menu_left_collapsed'); 
$items = Config::get('user-management.menu.left'); 
$controller = preg_split("/@/", Route::current()->getActionName());
$controller = is_array($controller) ? $controller[0] : $controller;
$curl = Request::path();
$language = app()->getLocale();
$locale = '-'. $language;
?>
<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapsee collapse">
			<ul class="page-sidebar-menu {{$collapsed?'page-sidebar-menu-closed':''}} "
			data-keep-expanded="{{$collapsed?'false':'true'}}"
			data-auto-scroll="true"
			data-slide-speed="200">
				<li class="sidebar-toggler-wrapper">
					<div class="sidebar-toggler"></div>
				</li>
				@foreach($items as $key=>$item)

                   <?php 
                   $label = is_array($item['label']) ? $item['label'][$language] : trans($item['label']);
                   $action = isset($item['action']) ? preg_replace('/index/i', '', \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($item['action']))) : '';
                   $action = !empty($action) && isset($item['slug']) ? $action . '/' .$item['slug'].$locale : $action;
                   $activeClass=(isset($item['slug']) ? 
                       (strpos($curl, '/'.$item['slug'].$locale) !== false ? 
                       ( Request::segment(3)==$item['slug'].$locale ? 'active' : '') : ''):(isset($item['action']) ? 
                       (strpos($item['action'], $controller) !== false && !Str::endsWith(Request::segment(3),$locale) ? 'active' : ''):''));
                   ?>
				    <li class="{{ ($key == 0)?'start':''}} {{ ($key == count($items)-1)?'last':''}} {{ $activeClass }}">
				        <a href="{{ (!empty($item['action']))?$action:'javascript;'  }}">
                            @if($item['icon'])
                                <i class="glyphicon glyphicon-{{ $item['icon'] }}"></i>
                            @endif
                            <span class="title">{{ $label }}</span>
                            @if (!empty($activeClass))
                            <span class="selected"></span>
                            <span class="arrow open "></span>
                            @else
                                <span class="arrow"></span>
                            @endif
                        </a>
                        @if(!empty($item['submenu']))
                        <ul class="sub-menu">
                            @foreach($item['submenu'] as $subItem)
                              <?php 
                              $action = isset($subItem['action']) ? preg_replace('/index/i', '', \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($subItem['action']))) : '';
                              $action = !empty($action) && isset($subItem['slug']) ? $action . '/' .$subItem['slug'] : $action;
                              $activeClass=(isset($subItem['slug']) ? 
                                  (strpos($curl, '/'.$subItem['slug']) !== false ? 
                                  ( Request::segment(3)==$subItem['slug'] ? 'active' : '') : ''):(isset($subItem['action']) ? 
                                  (strpos($subItem['action'], $controller) !== false ? 'active' : ''):''));
                              ?>
                                    <li class="{{ $activeClass }}">
                                        <a href="{{ (!empty($subItem['action']))?$action:'javascript;'  }}">
                                        @if($subItem['icon'])
                                            <i class="glyphicon glyphicon-{{ $subItem['icon'] }}"></i>
                                        @endif
                                        {{ trans($subItem['label'],['component'=>trans($subItem['label'])]) }}</a>
                                    </li>
                            @endforeach
                        </ul>
				       @endif
                 </li>
				@endforeach
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
@stop