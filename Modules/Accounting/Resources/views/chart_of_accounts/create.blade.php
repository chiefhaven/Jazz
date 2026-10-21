<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action('\Modules\Accounting\Http\Controllers\CoaController@store'), 'method' => 'post', 'id' => 'create_client_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'accounting::lang.add_account' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.account_type' ) . ':*', 'account_primary_type')->attributes([]) !!}
                    <select class="form-control" name="account_primary_type" id="account_primary_type" required>
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($account_types as $account_type => $account_details)
                            <option value="{{$account_type}}">{{__('accounting::lang.' .$account_type)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.account_sub_type' ) . ':*', 'account_sub_type')->attributes([]) !!}
                    <select class="form-control" name="account_sub_type_id" id="account_sub_type" required>
                        <option value="">@lang('messages.please_select')</option>
                    </select>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.detail_type' ) . ':*', 'detail_type')->attributes([]) !!}
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('detail_type_id', [], null, ['class' => 'form-control', 
                        'required', 'placeholder' => __('messages.please_select'), 'id' => 'detail_type' ])); !!}
                    <p class="help-block" id="detail_type_desc"></p>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'user.name' ) . ':*', 'name')->attributes([]) !!}
                    {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'user.name' ) ]); !!}
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.gl_code' ) . ':', 'gl_code')->attributes([]) !!}
                    {!! html()->text('gl_code', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'accounting::lang.gl_code' ) ]); !!}
                    <p class="help-block">@lang( 'accounting::lang.gl_code_help' )</p>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.parent_account' ) . ':', 'parent_account')->attributes([]) !!}
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('parent_account_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 
                        'id' => 'parent_account' ])); !!}
                </div>
            </div>
        </div>
        <div class="row" id="bal_div">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label(__( 'lang_v1.balance' ) . ':', 'balance')->attributes([]) !!}
                    {!! html()->text('balance', null)->attributes(['class' => 'form-control input_number', 
                        'placeholder' => __( 'lang_v1.balance' ) ]); !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.as_of' ) . ':', 'as_of')->attributes([]) !!}
                    <div class="input-group">
                        {!! html()->text('balance_as_of', null)->attributes(['class' => 'form-control', 'id' => 'as_of' ]); !!}
                        <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                    </div>
                </div>
            </div>
        </div>   
        <div class="row"">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'lang_v1.description' ) . ':', 'description')->attributes([]) !!}
                    {!! html()->textarea('description', null)->attributes(['class' => 'form-control', 
                        'placeholder' => __( 'lang_v1.description' ) ]); !!}
                </div>
            </div>
        </div> 
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->