<div class="modal fade" id="create_account_type_modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action('\Modules\Accounting\Http\Controllers\AccountTypeController@store'), 
        'method' => 'post', 'id' => 'create_account_type_form' ]))->open() !!}
    {!! html()->hidden('account_type', null)->attributes(['id' => 'account_type']); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="account_type_title"></h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'user.name' ) . ':*', 'name')->attributes([]) !!}
                    {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'user.name' ) ]); !!}
                </div>
            </div>
        </div>

        <div class="row" id="account_type_div">
            <div class="col-md-12">
              <div class="form-group">
                {!! html()->label(__( 'accounting::lang.account_type' ) . ':*', 'parent_id')->attributes([]) !!}
                  <select class="form-control" style="width: 100%;" name="account_primary_type" id="account_primary_type">
                   <option value="">@lang('messages.please_select')</option>
                        @foreach($account_types as $k => $v)
                            <option value="{{$k}}">{{$v['label']}}</option>
                        @endforeach
                  </select>
              </div>
            </div>
        </div>

        <div class="row" id="parent_id_div">
            <div class="col-md-12">
              <div class="form-group">
                {!! html()->label(__( 'accounting::lang.parent_type' ) . ':*', 'parent_id')->attributes([]) !!}
                  <select class="form-control" style="width: 100%;" name="parent_id" id="parent_id">
                   <option value="">@lang('messages.please_select')</option>
                     @foreach($account_sub_types as $account_type)
                       <option value="{{$account_type->id}}">
                      {{$account_type->account_type_name}}</option>
                     @endforeach
                  </select>
              </div>
            </div>
        </div>
        <div class="row" id="description_div">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'lang_v1.description' ) . ':', 'description')->attributes([]) !!}
                    {!! html()->textarea('description', null)->attributes(['class' => 'form-control', 
                        'placeholder' => __( 'lang_v1.description' ), 'rows' => 3 ]); !!}
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
</div>