<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\InvoiceSchemeController::class, 'update'], [$invoice->id]), 'method' => 'put', 'id' => 'invoice_scheme_add_form' ]))->open() !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'invoice.edit_invoice' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="option-div-group">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="option-div @if($invoice->scheme_type == 'blank') {{ 'active'}} @endif">
                                <h4>FORMAT: <br>XXXX <i class="fa fa-check-circle pull-right icon"></i></h4>
                                <input type="radio" name="scheme_type" value="blank" @if($invoice->scheme_type == 'blank') {{ 'checked'}} @endif>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="option-div  @if($invoice->scheme_type == 'year') {{ 'active'}} @endif">
                                <h4>FORMAT: <br>{{ date('Y') }}{{config('constants.invoice_scheme_separator')}}XXXX <i class="fa fa-check-circle pull-right icon"></i></h4>
                                <input type="radio" name="scheme_type" value="year" @if($invoice->scheme_type == 'year') {{ 'checked'}} @endif>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>@lang('invoice.preview'):</label>
                        <div id="preview_format">@lang('invoice.not_selected')</div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! html()->label(__( 'invoice.name' ) . ':*', 'name')->attributes([]) !!}
                        {!! html()->text('name', $invoice->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'invoice.name' ) ]); !!}
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                        {!! html()->label(__( 'invoice.number_type' ) . ':*', 'invoice_number_type')->attributes([]) !!} @show_tooltip(__('invoice.number_type_tooltip'))
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('number_type', $number_types, $invoice->number_type, ['class' => 'form-control select2', 'id' => 'invoice_number_type'])); !!}
                    </div>
                </div>


                <div id="invoice_format_settings">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! html()->label(__( 'invoice.prefix' ) . ':', 'prefix')->attributes([]) !!}
                            <div class="input-group col-md-12 col-sm-12">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                {!! html()->text('prefix', $invoice->prefix)->attributes(['class' => 'form-control', 'placeholder' => '']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 sequential_field @if($invoice->number_type == 'random') hide @endif">
                        <div class="form-group">
                            {!! html()->label(__( 'invoice.start_number' ) . ':', 'start_number')->attributes([]) !!}
                            <div class="input-group col-md-12 col-sm-12">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                {!! html()->input('number', 'start_number', $invoice->start_number)->attributes(['class' => 'form-control', 'required', 'min' => 0 ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! html()->label(__( 'invoice.total_digits' ) . ':', 'total_digits')->attributes([]) !!}
                                <div class="input-group col-md-12 col-sm-12">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('total_digits', ['4' => '4', '5' => '5', '6' => '6', '7' => '7',
                                    '8' => '8', '9'=>'9', '10' => '10'], $invoice->total_digits, ['class' => 'form-control', 'required'])); !!}
                                </div>
                            </div>
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