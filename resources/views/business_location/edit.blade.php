<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\BusinessLocationController::class, 'update'], [$location->id]), 'method' => 'PUT', 'id' => 'business_location_add_form' ]))->open() !!}

        {!! html()->hidden('hidden_id', $location->id)->attributes(['id' => 'hidden_id']); !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'business.edit_business_location' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! html()->label(__( 'invoice.name' ) . ':*', 'name')->attributes([]) !!}
                        {!! html()->text('name', $location->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'invoice.name' ) ]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'lang_v1.location_id' ) . ':', 'location_id')->attributes([]) !!}
                        {!! html()->text('location_id', $location->location_id)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.location_id' ) ]); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.landmark' ) . ':', 'landmark')->attributes([]) !!}
                        {!! html()->text('landmark', $location->landmark)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.landmark' ) ]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.city' ) . ':*', 'city')->attributes([]) !!}
                        {!! html()->text('city', $location->city)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.city'), 'required' ]); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.zip_code' ) . ':*', 'zip_code')->attributes([]) !!}
                        {!! html()->text('zip_code', $location->zip_code)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.zip_code'), 'required' ]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.state' ) . ':*', 'state')->attributes([]) !!}
                        {!! html()->text('state', $location->state)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.state'), 'required' ]); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.country' ) . ':*', 'country')->attributes([]) !!}
                        {!! html()->text('country', $location->country)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.country'), 'required' ]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.mobile' ) . ':', 'mobile')->attributes([]) !!}
                        {!! html()->text('mobile', $location->mobile)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.mobile')]); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.alternate_number' ) . ':', 'alternate_number')->attributes([]) !!}
                        {!! html()->text('alternate_number', $location->alternate_number)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.alternate_number')]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'business.email' ) . ':', 'email')->attributes([]) !!}
                        {!! html()->email('email', $location->email)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.email')]); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__( 'lang_v1.website' ) . ':', 'website')->attributes([]) !!}
                        {!! html()->text('website', $location->website)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.website')]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('invoice.invoice_scheme_for_pos') . ':*', 'invoice_scheme_id')->attributes([]) !!} @show_tooltip(__('tooltip.invoice_scheme'))
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('invoice_scheme_id', $invoice_schemes, $location->invoice_scheme_id, ['class' => 'form-control', 'required',
                        'placeholder' => __('messages.please_select')])); !!}
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('invoice.invoice_scheme_for_sale') . ':*', 'sale_invoice_scheme_id')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sale_invoice_scheme_id', $invoice_schemes, $location->sale_invoice_scheme_id, ['class' => 'form-control', 'required',
                        'placeholder' => __('messages.please_select')])); !!}
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.invoice_layout_for_pos') . ':*', 'invoice_layout_id')->attributes([]) !!} @show_tooltip(__('tooltip.invoice_layout'))
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('invoice_layout_id', $invoice_layouts, $location->invoice_layout_id, ['class' => 'form-control', 'required',
                        'placeholder' => __('messages.please_select')])); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.invoice_layout_for_sale') . ':*', 'sale_invoice_layout_id')->attributes([]) !!} @show_tooltip(__('tooltip.invoice_layout'))
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sale_invoice_layout_id', $invoice_layouts, $location->sale_invoice_layout_id, ['class' => 'form-control', 'required',
                        'placeholder' => __('messages.please_select')])); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.default_selling_price_group') . ':', 'selling_price_group_id')->attributes([]) !!} @show_tooltip(__('lang_v1.location_price_group_help'))
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('selling_price_group_id', $price_groups, $location->selling_price_group_id, ['class' => 'form-control',
                        'placeholder' => __('messages.please_select')])); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                @php
                $custom_labels = json_decode(session('business.custom_labels'), true);
                $location_custom_field1 = !empty($custom_labels['location']['custom_field_1']) ? $custom_labels['location']['custom_field_1'] : __('lang_v1.location_custom_field1');
                $location_custom_field2 = !empty($custom_labels['location']['custom_field_2']) ? $custom_labels['location']['custom_field_2'] : __('lang_v1.location_custom_field2');
                $location_custom_field3 = !empty($custom_labels['location']['custom_field_3']) ? $custom_labels['location']['custom_field_3'] : __('lang_v1.location_custom_field3');
                $location_custom_field4 = !empty($custom_labels['location']['custom_field_4']) ? $custom_labels['location']['custom_field_4'] : __('lang_v1.location_custom_field4');
                @endphp
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! html()->label($location_custom_field1 . ':', 'custom_field1')->attributes([]) !!}
                        {!! html()->text('custom_field1', $location->custom_field1)->attributes(['class' => 'form-control',
                        'placeholder' => $location_custom_field1]); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! html()->label($location_custom_field2 . ':', 'custom_field2')->attributes([]) !!}
                        {!! html()->text('custom_field2', $location->custom_field2)->attributes(['class' => 'form-control',
                        'placeholder' => $location_custom_field2]); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! html()->label($location_custom_field3 . ':', 'custom_field3')->attributes([]) !!}
                        {!! html()->text('custom_field3', $location->custom_field3)->attributes(['class' => 'form-control',
                        'placeholder' => $location_custom_field3]); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! html()->label($location_custom_field4 . ':', 'custom_field4')->attributes([]) !!}
                        {!! html()->text('custom_field4', $location->custom_field4)->attributes(['class' => 'form-control',
                        'placeholder' => $location_custom_field4]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr>
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.pos_screen_featured_products') . ':', 'featured_products')->attributes([]) !!} @show_tooltip(__('lang_v1.featured_products_help'))
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('featured_products[]', $featured_products, $location->featured_products, ['class' => 'form-control',
                        'id' => 'featured_products', 'multiple'])); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr>
                <div class="col-sm-12">
                    <strong>@lang('lang_v1.payment_options'): @show_tooltip(__('lang_v1.payment_option_help'))</strong>
                    <div class="form-group">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('lang_v1.payment_method')</th>
                                    <th class="text-center">@lang('lang_v1.enable')</th>
                                    <th class="text-center @if(empty($accounts)) hide @endif">@lang('lang_v1.default_accounts') @show_tooltip(__('lang_v1.default_account_help'))</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $default_payment_accounts = !empty($location->default_payment_accounts) ?
                                json_decode($location->default_payment_accounts, true) : [];
                                @endphp
                                @foreach($payment_types as $key => $value)
                                <tr>
                                    <td class="text-center">{{$value}}</td>
                                    <td class="text-center">{!! html()->checkbox('default_payment_accounts[' . $key . '][is_enabled]', !empty($default_payment_accounts[$key]['is_enabled']), 1)->attributes([]); !!}</td>
                                    <td class="text-center @if(empty($accounts)) hide @endif">
                                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('default_payment_accounts[' . $key . '][account]', $accounts, !empty($default_payment_accounts[$key]['account']) ? $default_payment_accounts[$key]['account'] : null, ['class' => 'form-control input-sm'])); !!}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! html()->form()->close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->