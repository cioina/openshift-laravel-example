@section('content')
<div class="row">
	<div class="col-md-12 {{$class}}">
		<div class=" number">
			 {{ $code }}
		</div>
		<div class=" details">
			<h3>{{ trans('user-management::errors.oops') }}</h3>
			@if(config('app.debug'))
			<p>{{ $message }}</p>
			@else
            <p>{{ trans('user-management::errors.fixing_it') }}</p>
			<h3><a href="{{ config('app.url') }}{{ config('user-management.home_page') }}" target="_self">{{ trans('user-management::errors.click_home') }}</a></h3>
            <img src="{{ Config::get('app.url').'/assets/admin/img/broken-robot-banner-750x467.png' }}" alt="{{ trans('user-management::errors.oops') }}" width="100%" />
			@endif
		</div>
	</div>
</div><!-- errors\default.blade row -->
@stop