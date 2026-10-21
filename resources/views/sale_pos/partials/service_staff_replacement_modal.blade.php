    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="modal-title">@lang('restaurant.service_staff_replacement')</h3>
                    </div>
                    <div class="col-md-3">
                        <h3 class="modal-title pull-right">{{ $transaction->invoice_no }}</h3>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="close mt-3" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
            {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })([
                'route' => ['change_service_staff', $transaction->id],
                'method' => 'put',
                'id' => 'change_service_staff',
            ]))->open() !!}
            {{ method_field('PUT') }}
            <div class="modal-body">
                <div class="row">
                    @php
                        $is_service_staff_required =
                            !empty($pos_settings['is_service_staff_required']) &&
                            $pos_settings['is_service_staff_required'] == 1
                                ? true
                                : false;
                    @endphp
                    @if (in_array('tables', $enabled_modules) || in_array('service_staff', $enabled_modules))
                        <div class="col-sm-8">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user-secret"></i>
                                    </span>
                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('res_waiter_id', $waiters, $transaction->res_waiter_id, [
                                        'class' => 'form-control',
                                        'required' => $is_service_staff_required,
                                        'placeholder' => __('restaurant.select_service_staff'),
                                    ])) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (!empty($pos_settings['inline_service_staff']))
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('sale.product') }} </th>
                                        <th>{{ __('restaurant.service_staff') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sell_details as $key => $sell_detail)
                                        <tr>
                                            @php
                                                $product_name =
                                                    $sell_detail->product_name . '<br/>' . $sell_detail->sub_sku;
                                                if (!empty($sell_detail->brand)) {
                                                    $product_name .= ' ' . $sell_detail->brand;
                                                }
                                            @endphp
                                            <td>
                                                {!! $product_name !!}
                                            </td>
                                            <td>
                                                <input type="hidden" name="sell_details[{{ $key }}][id]"
                                                    value="{{ $sell_detail->id }}">
                                                <div class="form-group">
                                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_details[' . $key . '][res_service_staff_id]', $waiters, $sell_detail->res_service_staff_id, [
                                                            'class' => 'form-control select',
                                                            'placeholder' => __('restaurant.select_service_staff'),
                                                            'required' => $is_service_staff_required
                                                        ])) !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer"> <!-- Footer Section -->
                <div class="col-md-12">
                    <button class="tw-dw-btn tw-dw-btn-primary tw-text-white"> {{ __('messages.save') }} </button>
                    <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">Close</button>
                </div>
            </div>
            {!! html()->form()->close() !!}
        </div>
    </div>
