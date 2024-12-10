@if(\Distilleries\Expendable\Helpers\FormUtils::hasCookies())
<div class="alert alert-danger  alert-dismissible">
    <button class="close" data-dismiss="alert"></button>
    <h3>
        {{trans('user-management::errors.cookies_message')}}
     </h3>
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action('\\Acioina\\UserManagement\\Http\Controllers\\ClientLogoutController@getCookies')) }}" target="_self" class="btn btn-sm green margin-bottom-5"><i class="glyphicon glyphicon-ok"></i> {{trans('user-management::errors.click_gotIt')}}</a>
    <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action('\\Acioina\\UserManagement\\Http\Controllers\\FrontEndController@getIndex')) }}" target="_self" class="btn btn-sm blue margin-bottom-5"><i class="glyphicon glyphicon-info-sign"></i> {{trans('user-management::errors.click_readMore')}}</a>
  </div>
@endif

@if(is_object($errors) and count($errors) > 0)
<div class="alert alert-danger alert-dismissible">
    <button class="close" data-dismiss="alert"></button>
    <ul>
      @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
      @endforeach
     </ul>
  </div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible">
    <button class="close" data-dismiss="alert"></button>
    <ul>
        <li>{{ Session::get('error') }}</li>
     </ul>
  </div>
@endif

@if(Session::has('warnings'))
<div class="alert alert-warning alert-dismissible">
    <button class="close" data-dismiss="alert"></button>
    <ul>
      @foreach (Session::get('warnings') as $warning)
          <li>{{ $warning }}</li>
      @endforeach
     </ul>
  </div>
@endif

@if(Session::has('messages'))
<div class="alert alert-success alert-dismissible">
    <button class="close" data-dismiss="alert"></button>
    <ul>
      @foreach (Session::get('messages') as $message)
          <li>{{ $message }}</li>
      @endforeach
     </ul>
  </div>
@endif

@if(\Distilleries\Expendable\Helpers\FormUtils::hasMessages())
<div class="alert alert-success  alert-dismissible">
    <button class="close" data-dismiss="alert"></button>
    <ul>
      @foreach (\Distilleries\Expendable\Helpers\FormUtils::getMessages() as $message)
          <li>{{ $message }}</li>
      @endforeach
     </ul>
  </div>
@endif

<?php 
\Distilleries\Expendable\Helpers\FormUtils::forgetMessages(); 
?>
