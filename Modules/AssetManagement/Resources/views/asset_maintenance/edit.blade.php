<div class="modal-dialog" role="document">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController::class, 'update'], [$maintenance->id]), 'method' => 'put', 'id' => 'asset_maintenance_form', 'files' => true]))->open() !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.asset_maintenance') - #{{$maintenance->maitenance_id}}
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('sale.status') . ':', 'status')->attributes([])!!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('status', $statuses, $maintenance->status, ['class' => 'form-control', 'placeholder' => __('messages.please_select')])); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.priority') . ':', 'priority')->attributes([])!!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('priority', $priorities, $maintenance->priority, ['class' => 'form-control', 'placeholder' => __('messages.please_select')])); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.attachments') . ':', 'attachments')->attributes([]) !!}
                            {!! html()->file('attachments[]')->attributes(['id' => 'attachments', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                            <p class="help-block">
                                @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                @includeIf('components.document_help_text')
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.assigned_to') . ':', 'assigned_to')->attributes([])!!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('assigned_to', $users, $maintenance->assigned_to, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @include('sell.partials.media_table', ['medias' => $maintenance->media, 'delete' => true])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.maintenance_note') . ':', 'maintenance_note')->attributes([])!!}
                            {!! html()->textarea('maintenance_note', $maintenance->maintenance_note)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.maintenance_note'), 'rows' => 3, 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('assetmanagement::lang.send_for_maintenance_details') . ':', 'details')->attributes([])!!}
                            {!! html()->textarea('details', $maintenance->details)->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.send_for_maintenance_details'), 'rows' => 3]); !!}
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
        </div><!-- /.modal-content -->
    {!! html()->form()->close() !!}
</div><!-- /.modal-dialog -->