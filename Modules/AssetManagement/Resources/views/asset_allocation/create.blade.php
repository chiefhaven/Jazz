<div class="modal-dialog" role="document">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['action' => '\Modules\AssetManagement\Http\Controllers\AssetAllocationController@store', 'id' => 'asset_allocation_form', 'method' => 'post']))->open() !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.asset_allocation')
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.allocation_code') . ':', 'ref_no')->attributes([])!!}
                            {!! html()->text('ref_no', null)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.allocation_code')]); !!}
                            <p class="help-block">
                                @lang('lang_v1.leave_empty_to_autogenerate')
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.asset') . ':*', 'asset_id')->attributes([])!!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('asset_id', $assets['assets'], $asset_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.allocate_to') . ':*', 'receiver')->attributes([])!!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('receiver', $users, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.quantity') . ':*', 'quantity')->attributes([])!!}
                            {!! html()->text('quantity', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.allocate_from') . ':*', 'transaction_datetime')->attributes([])!!}
                            {!! html()->text('transaction_datetime', null)->attributes(['class' => 'form-control datetimepicker', 'placeholder' => __('assetmanagement::lang.allocate_from'), 'readonly', 'required']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.allocated_upto') . ':', 'allocated_upto')->attributes([])!!}
                            {!! html()->text('allocated_upto', null)->attributes(['class' => 'form-control datepicker', 'placeholder' => __('assetmanagement::lang.allocated_upto'), 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.reason') . ':', 'reason')->attributes([]) !!}
                            {!! html()->textarea('reason', null)->attributes(['class' => 'form-control', 'rows' => '3', 'placeholder' => __('assetmanagement::lang.reason')]); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.save')
                </button>
            </div>
        </div>
    {!! html()->form()->close() !!} <!-- /form close -->
</div><!-- /.modal-dialog -->