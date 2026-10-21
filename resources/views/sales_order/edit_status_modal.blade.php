<div class="modal-dialog" role="document">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\SalesOrderController::class, 'postEditSalesOrderStatus'], ['id' => $id]), 'method' => 'put', 'id' => 'update_so_status_form']))->open() !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('lang_v1.edit_status')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! html()->label(__('sale.status') . ':', 'so_status')->attributes([]) !!}
                        <select name="status" id="so_status" class="form-control" style="width: 100%;">
                            @foreach($statuses as $key => $so_status)
                                <option value="{{$key}}" @if($key == $status) selected @endif>
                                    {{$so_status['label']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                @lang('messages.close')
            </button>
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white ladda-button">
                @lang('messages.update')
            </button>
        </div>
    </div><!-- /.modal-content -->
    {!! html()->form()->close() !!}
</div><!-- /.modal-dialog -->
