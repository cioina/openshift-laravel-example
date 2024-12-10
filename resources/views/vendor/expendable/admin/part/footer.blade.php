@section('footer')
<div class="copyright">
</div>
<div class="scroll-to-top">
    <i class="glyphicon glyphicon-upload"></i>
</div>

<script src="{{ Config::get('app.url').'/assets/admin/js/app.admin.min.js?v='.$version }}"></script>
@include('expendable::admin.part.validation')
@yield('javascript')
@stop