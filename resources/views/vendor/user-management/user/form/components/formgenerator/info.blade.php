@section('form')
<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-eye-open"></i>{{trans('user-management::form.view')}} {{ trans('user-management::form.'.Request::segment(1)) }}
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body form ">
                <div class="form-actions top">
                    <div class="btn-set pull-right">
                        {!! form_widget($form->back) !!}
                    </div>
                </div>
                <div class="form-horizontal form-bordered">
                    {!! form_rest_view($form) !!}
                </div>
                <script>
                    if (typeof (Prism) != 'undefined') {
                        var readyState = document.readyState;
                        if (readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', Prism.highlightAll);
                        } else {
                            if (window.requestAnimationFrame) {
                                window.requestAnimationFrame(Prism.highlightAll);
                            } else {
                                window.setTimeout(Prism.highlightAll, 16);
                            }
                        }
                    }
                </script>
                <div class="form-actions ">
                    <div class="btn-set pull-right">
                        {!! form_widget($form->back) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- formgenerator\info.blade row -->
@stop