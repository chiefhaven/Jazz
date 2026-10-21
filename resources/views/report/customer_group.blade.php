@extends('layouts.app')
@section('title', __('lang_v1.customer_groups_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('lang_v1.customer_groups_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

              {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ReportController::class, 'getCustomerGroup']), 'method' => 'get', 'id' => 'cg_report_filter_form' ]))->open() !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! html()->label(__( 'lang_v1.customer_group_name' ) . ':', 'cg_customer_group_id')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('cg_customer_group_id', $customer_group, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'cg_customer_group_id'])); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! html()->label(__('purchase.business_location') . ':', 'cg_location_id')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('cg_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%'])); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! html()->label(__('report.date_range') . ':', 'cg_date_range')->attributes([]) !!}
                        {!! html()->text('date_range', null)->attributes(['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'cg_date_range', 'readonly']); !!}
                    </div>
                </div>

                {!! html()->form()->close() !!}
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="cg_report_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.customer_group')</th>
                            <th>@lang('report.total_sell')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    
    <script type="text/javascript">
        $(document).ready(function(){
            if($('#cg_date_range').length == 1){
                $('#cg_date_range').daterangepicker(
                    dateRangeSettings,
                    function (start, end) {
                        $('#cg_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                        cg_report_table.ajax.reload();
                    }
                );

                $('#cg_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    cg_report_table.ajax.reload();
                });
            }

            cg_report_table = $('#cg_report_table').DataTable({
                            processing: true,
                            serverSide: true,
                            fixedHeader:false,
                            "ajax": {
                                "url": "/reports/customer-group",
                                "data": function ( d ) {
                                    d.location_id = $('#cg_location_id').val();
                                    d.customer_group_id = $('#cg_customer_group_id').val();
                                    d.start_date = $('#cg_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#cg_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [
                                {data: 'name', name: 'CG.name'},
                                {data: 'total_sell', name: 'total_sell', searchable: false}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#cg_report_table'));
                            }
                        });
            //Customer Group report filter
            $('select#cg_location_id, select#cg_customer_group_id, #cg_date_range').change( function(){
                cg_report_table.ajax.reload();
            });
        })
    </script>
@endsection