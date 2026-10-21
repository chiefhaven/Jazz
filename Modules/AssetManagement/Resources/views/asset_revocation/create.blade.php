<div class="modal-dialog" role="document">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['action' => '\Modules\AssetManagement\Http\Controllers\RevokeAllocatedAssetController@store', 'id' => 'revoke_asset_form', 'method' => 'post']))->open() !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.revoke_asset', ['allocation_code' => $allocated_asset->ref_no])
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <input type="hidden" name="parent_id" value="{{$allocated_asset->id}}">
                        <input type="hidden" name="asset_id" value="{{$allocated_asset->asset_id}}">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.revoke_code') . ':', 'ref_no')->attributes([])!!}
                            @show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
                            {!! html()->text('ref_no', null)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.revoke_code')]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if(!empty($allocated_asset->quantity))
                            @php
                                $max_qty = $allocated_asset->quantity - $total_revoked_asset;
                            @endphp
                        @endif
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.quantity') . ':*', 'quantity')->attributes([])!!}
                            {!! html()->text('quantity', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1, 'max' => (int)$max_qty]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.revoked_at') . ':*', 'transaction_datetime')->attributes([])!!}
                            {!! html()->text('transaction_datetime', null)->attributes(['class' => 'form-control datetimepicker', 'placeholder' => __('assetmanagement::lang.revoked_at'), 'readonly', 'required']); !!}
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
        </div><!-- /.modal-content -->
    {!! html()->form()->close() !!}
</div><!-- /.modal-dialog -->