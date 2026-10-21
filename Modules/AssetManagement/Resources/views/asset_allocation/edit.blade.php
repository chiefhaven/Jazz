<div class="modal-dialog" role="document">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\AssetManagement\Http\Controllers\AssetAllocationController::class, 'update'], [$asset_allocated->id]), 'method' => 'put', 'id' => 'asset_allocation_form']))->open() !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.edit')
                    <code>({{$asset_allocated->ref_no}})</code>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.asset') . ':*', 'asset_id')->attributes([])!!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('asset_id', $assets['assets'], $asset_allocated->asset_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;', 'disabled'])); !!}
                        </div>
                        <input type="hidden" name="asset_id" value="{{$asset_allocated->asset_id}}">
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.allocate_to') . ':*', 'receiver')->attributes([])!!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('receiver', $users, $asset_allocated->receiver, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if(!empty($asset_allocated->revokeTransaction))
                        @php
                            $revoked_quantities = $asset_allocated->revokeTransaction->pluck('quantity')->toArray();
                            $total_rq = 0;
                            foreach ($revoked_quantities as $key => $quantity) {
                                $total_rq += (int)$quantity;
                            }

                            $current_allocated_qty = $asset_allocated->quantity - $total_rq;
                            $max_qty = $current_allocated_qty + $total_available_asset;
                        @endphp                    
                    @endif
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.quantity') . ':*', 'quantity')->attributes([])!!}
                            {!! html()->text('quantity', !empty($asset_allocated->quantity) ? @format_quantity($asset_allocated->quantity): null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1, 'max' => (int)$max_qty]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.allocate_from') . ':*', 'transaction_datetime')->attributes([])!!}
                            {!! html()->text('transaction_datetime', !empty($asset_allocated->transaction_datetime) ? @format_datetime($asset_allocated->transaction_datetime) : null)->attributes(['class' => 'form-control datetimepicker', 'placeholder' => __('assetmanagement::lang.allocate_from'), 'readonly', 'required']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.allocated_upto') . ':', 'allocated_upto')->attributes([])!!}
                            {!! html()->text('allocated_upto', !empty($asset_allocated->allocated_upto) ? @format_date($asset_allocated->allocated_upto) : null)->attributes(['class' => 'form-control datepicker', 'placeholder' => __('assetmanagement::lang.allocated_upto'), 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.reason') . ':', 'reason')->attributes([]) !!}
                            {!! html()->textarea('reason', $asset_allocated->reason)->attributes(['class' => 'form-control', 'rows' => '3', 'placeholder' => __('assetmanagement::lang.reason')]); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.update')
                </button>
            </div>
        </div>
    {!! html()->form()->close() !!} <!-- /form close -->
</div><!-- /.modal-dialog -->