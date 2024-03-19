@section('footer')
<div class="copyright">
</div>
<div class="scroll-to-top">
    <i class="glyphicon glyphicon-upload"></i>
</div>

<script src="{{ Config::get('app.url').'/assets/front/js/app.min.js?v='.$version  }}"></script>
@include('user-management::user.part.validation')
@yield('javascript')
@stop