<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action('\Modules\Accounting\Http\Controllers\CoaController@update', $account->id), 
        'method' => 'put', 'id' => 'create_client_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'accounting::lang.edit_account' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.account_type' ) . ':*', 'account_primary_type')->attributes([]) !!}
                    <select class="form-control" name="account_primary_type" id="account_primary_type" required>
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($account_types as $account_type => $account_details)
                            <option value="{{$account_type}}"
                            @if($account->account_primary_type == $account_type) selected @endif
                            >{{__('accounting::lang.' .$account_type)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.account_sub_type' ) . ':*', 'account_sub_type')->attributes([]) !!}
                    <select class="form-control" name="account_sub_type_id" id="account_sub_type" required>
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($account_sub_types as $account_type)
                            <option value="{{$account_type->id}}" 
                            data-show_balance="{{$account_type->show_balance}}"
                             @if($account->account_sub_type_id == $account_type->id) selected @endif>
                            {{$account_type->account_type_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.detail_type' ) . ':*', 'detail_type')->attributes([]) !!}

                    <select class="form-control" name="detail_type_id" id="detail_type" required>
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($account_detail_types as $detail_type)
                            <option value="{{$detail_type->id}}" 
                            @if($account->detail_type_id == $detail_type->id) selected @endif >
                            {{$detail_type->account_type_name}}</option>
                        @endforeach
                    </select>
                    <p class="help-block" id="detail_type_desc">{{$account->detail_type->account_type_description ?? ''}}</p>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'user.name' ) . ':*', 'name')->attributes([]) !!}
                    {!! html()->text('name', $account->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'user.name' ) ]); !!}
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.gl_code' ) . ':', 'gl_code')->attributes([]) !!}
                    {!! html()->text('gl_code', $account->gl_code)->attributes(['class' => 'form-control', 'placeholder' => __( 'accounting::lang.gl_code' ) ]); !!}
                    <p class="help-block">@lang( 'accounting::lang.gl_code_help' )</p>
                </div>
                <div class="form-group">
                    {!! html()->label(__( 'accounting::lang.parent_account' ) . ':', 'parent_account')->attributes([]) !!}
                    <select class="form-control" name="parent_account_id" id="parent_account">
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($parent_accounts as $parent_account)
                            <option value="{{$parent_account->id}}" 
                            @if($account->parent_account_id == $parent_account->id) selected @endif >
                            {{$parent_account->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row"">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'lang_v1.description' ) . ':', 'description')->attributes([]) !!}
                    {!! html()->textarea('description', $account->description)->attributes(['class' => 'form-control', 
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