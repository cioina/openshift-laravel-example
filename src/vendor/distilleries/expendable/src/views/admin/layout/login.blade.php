<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    @yield('header')
</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="{{ !empty($class_layout)?$class_layout:''  }}">
    <!-- BEGIN LOGO -->
    <div class="logo">
        <a href="{{ Config::get('app.url') }}">
            <img src="{{ Config::get('app.url').'/assets/admin/img/logo.png' }}" alt="" />
        </a>
    </div>
    <!-- END LOGO -->
    <!-- BEGIN LOGIN -->
    <div class="content">
        @yield('content')
  
    </div>

    @yield('footer')
</body>
<!-- END BODY -->
</html>
