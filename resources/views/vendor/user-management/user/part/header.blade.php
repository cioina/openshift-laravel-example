@section('header')
<meta charset="utf-8" />
<title>{{ $title }}</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta http-equiv="Content-type" content="text/html; charset=utf-8 /">
<meta content="" name="description" />
<meta content="" name="author" />
<link rel="shortcut icon" href="favicon.ico" />
<base href="{{ Config::get('app.url') }}" />
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('app.url').'/assets/admin/css/app.admin.min.css?v='.$version }}" />
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('app.url').'/assets/admin/css/blog.min.css?v='.$version }}" />
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('app.url').'/assets/admin/css/alpaca.min.css?v='.$version }}" />

<script src="{{ Config::get('app.url').'/assets/admin/js/jquery.min.js?v='.$version }}"></script>
<script src="{{ Config::get('app.url').'/assets/admin/js/jquery.acioina.min.js?v='.$version }}"></script>

@if(\Distilleries\Expendable\Helpers\FormUtils::hasForms())
<script src="{{ Config::get('app.url').'/assets/admin/js/handlebars.min.js?v='.$version }}"></script>
<script src="{{ Config::get('app.url').'/assets/admin/js/alpaca.min.js?v='.$version }}"></script>
<script src="{{ Config::get('app.url').'/assets/admin/js/alpaca.messages.'.app()->getLocale().'.js?v='.$version }}"></script>
<script type="text/javascript">
  @include('user-management::user.part.angular')
</script>
<script src="{{ Config::get('app.url').'/assets/admin/js/app.angular.min.js?v='.$version }}"></script>
@endif

<?php 
\Distilleries\Expendable\Helpers\FormUtils::forgetForms(); 
?>

@stop