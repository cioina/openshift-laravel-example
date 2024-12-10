<script type="text/javascript">
    $(function () {
        // dynamic table
        oTable = jQuery('#{{ $id }}').dataTable({

            "language": {
                "paginate":{
                    "first": "{{ trans('datatable-builder::datatable.first') }}",
                    "last": "{{ trans('datatable-builder::datatable.last') }}",
                    "next": "{{ trans('datatable-builder::datatable.next') }}",
                    "previous": "{{ trans('datatable-builder::datatable.previous') }}"
                },
                "aria": {
                    "sortAscending":"{{ trans('datatable-builder::datatable.activate_to_sort_asc') }}",
                    "sortDescending": "{{ trans('datatable-builder::datatable.activate_to_sort_desc') }}"
                },
                "emptyTable": "{{ trans('datatable-builder::datatable.no_data') }}",
                "info": "{{ trans('datatable-builder::datatable.info') }}",
                "infoEmpty": "{{ trans('datatable-builder::datatable.infoEmpty') }}",
                "infoFiltered": "{{ trans('datatable-builder::datatable.infoFiltered') }}",
                "lengthMenu": "{{ trans('datatable-builder::datatable.lengthMenu') }}",
                "search": "{{ trans('datatable-builder::datatable.search') }}",
                "zeroRecords": "{{ trans('datatable-builder::datatable.zeroRecords') }}"
            },
            'fnServerData': function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'text json',
                    'type': 'GET',
                    'url': sSource,
                    'data': dist.Form.Fields.DatatableUtils.addSessionFilters(aoData,$('#{{ $id }}')),
                    'success': fnCallback
                });
            },
            'fnDrawCallback': function (oSettings) {
                $('.img_group1').acioina({ rel: 'img_group1', slideshow: true, maxWidth: '100%', maxHeight: '100%' });
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
            },
        @foreach ($options as $k => $o)
        {!! json_encode($k) !!}: {!! json_encode($o) !!},
    @endforeach

        @foreach ($callbacks as $k => $o)
        {!! json_encode($k) !!}: {!! $o !!},
    @endforeach

    }).on( 'draw.dt', function () {

        if(typeof(Metronic) != 'undefined'){
            Metronic.initAjax();
        }

    });

    if(typeof(jQuery.select2) != 'undefined'){
        jQuery('select',jQuery('#{{ $id }}_wrapper')).select2();
    }
    dist.Form.Fields.DatatableUtils.initFilters('.filter-cancel','.filter-submit');
    // custom values are available via $values array
    });
</script>
