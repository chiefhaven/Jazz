<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['action' => '\Modules\AssetManagement\Http\Controllers\AssetController@store', 'id' => 'asset_form', 'method' => 'post', 'files' => true]))->open() !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">    <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                @lang('assetmanagement::lang.add_asset')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    @if(count($business_locations) == 1)
                        @php 
                            $default_location = current(array_keys($business_locations->toArray()));
                        @endphp
                    @else
                        @php
                            $default_location = null;
                        @endphp
                    @endif
                    <div class="form-group">
                        {!! html()->label(__('business.business_location') . ':*', 'location_id')->attributes([])!!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $default_location, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('assetmanagement::lang.asset_code') . ':', 'asset_code')->attributes([])!!}
                        {!! html()->text('asset_code', null)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_code')]); !!}
                        <p class="help-block">
                            @lang('lang_v1.leave_empty_to_autogenerate')
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('assetmanagement::lang.asset_name') . ':*', 'name')->attributes([])!!}
                        {!! html()->text('name', null)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_name'), 'required']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.quantity') . ':*', 'quantity')->attributes([])!!}
                        {!! html()->text('quantity', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! html()->label(__('assetmanagement::lang.model_no') . ':', 'model')->attributes([])!!}
                    {!! html()->text('model', null)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.model_no')]); !!}
                </div>
                <div class="col-md-4">
                    {!! html()->label(__('assetmanagement::lang.serial_no') . ':', 'serial_no')->attributes([])!!}
                    {!! html()->text('serial_no', null)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.serial_no')]); !!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    {!! html()->label(__('assetmanagement::lang.asset_category') . ':', 'category_id')->attributes([])!!}
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('category_id', $asset_category, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('purchase.purchase_date') . ':', 'purchase_date')->attributes([])!!}
                        {!! html()->text('purchase_date', null)->attributes(['class' => 'form-control datepicker', 'placeholder' => __('purchase.purchase_date'), 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! html()->label(__('assetmanagement::lang.purchase_type') . ':', 'purchase_type')->attributes([])!!}
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('purchase_type', $purchase_types, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;'])); !!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('sale.unit_price') . ':*', 'unit_price')->attributes([])!!}
                        {!! html()->text('unit_price', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('sale.unit_price'), 'required']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('assetmanagement::lang.depreciation') . ':', 'depreciation')->attributes([])!!}
                        {!! html()->text('depreciation', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.depreciation')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.image') . ':', 'image')->attributes([]) !!}
                            {!! html()->file('image')->attributes(['id' => 'upload_asset_image', 'accept' => 'image/*']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                </p>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_allocatable" value="1">
                                @lang('assetmanagement::lang.is_allocatable')

                                @show_tooltip(__('assetmanagement::lang.allocatable_tooltip'))
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.description') . ':', 'description')->attributes([]) !!}
                        {!! html()->textarea('description', null)->attributes(['class' => 'form-control', 'rows' => '3', 'placeholder' => __('lang_v1.description')]); !!}
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h4>@lang('lang_v1.warranties')</h4>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary btn-sm pull-right" id="add_more_warranty"><i class="fas fa-plus"></i> @lang('assetmanagement::lang.add_more')</button>
                </div>
                <div class="col-md-12">
                <table class="table" id="asset_warranty_table">
                    <thead>
                        <tr>
                            <th>@lang('business.start_date')</th>
                            <th>@lang('assetmanagement::lang.warranty_months')</th>
                            <th>@lang('assetmanagement::lang.additional_cost')</th>
                            <th>@lang('purchase.additional_notes')</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {!! html()->text('start_dates[]', null)->attributes(['class' => 'form-control datepicker', 'placeholder' => __('business.start_date'), 'readonly' ]); !!}
                            </td>
                            <td>
                                {!! html()->text('months[]', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.warranty_months')]); !!}
                            </td>
                            <td>
                                {!! html()->text('additional_cost[]', 0)->attributes(['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.additional_cost')]); !!}
                            </td>
                            <td>
                                {!! html()->textarea('additional_note[]', null)->attributes(['class' => 'form-control', 'placeholder' => __('purchase.additional_notes'), 'rows' => 3]); !!}
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
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
    {!! html()->form()->close() !!}
</div>