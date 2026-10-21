<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action('\Modules\Accounting\Http\Controllers\AccountTypeController@update', 
        $account_type->id), 'method' => 'put', 'id' => 'edit_account_type_form' ]))->open() !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@if($account_type->account_type=='sub_type') 
        @lang('accounting::lang.edit_account_type') @else @lang('accounting::lang.edit_detail_type') @endif</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'user.name' ) . ':*', 'edit_name')->attributes([]) !!}
                    {!! html()->text('name', $account_type->name)->attributes(['class' => 'form-control', 
                        'required', 'placeholder' => __( 'user.name' ), 'id' => 'edit_name' ]); !!}
                </div>
            </div>
        </div>
        <div class="row @if($account_type->account_type=='sub_type') hide @endif">
            <div class="col-md-12">
              <div class="form-group">
                {!! html()->label(__( 'accounting::lang.parent_type' ) . ':*', 'edit_parent_id')->attributes([]) !!}
                  <select class="form-control" style="width: 100%;" name="parent_id" id="edit_parent_id">
                   <option value="">@lang('messages.please_select')</option>
                     @foreach($account_sub_types as $at)
                       <option value="{{$at->id}}" @if($at->id==$account_type->parent_id) selected @endif >
                      {{$at->account_type_name}}</option>
                     @endforeach
                  </select>
              </div>
            </div>
        </div>
        <div class="row @if($account_type->account_type=='sub_type') hide @endif"">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'lang_v1.description' ) . ':', 'edit_description')->attributes([]) !!}
                    {!! html()->textarea('description', $account_type->description)->attributes(['class' => 'form-control', 
                        'placeholder' => __( 'lang_v1.description' ), 'rows' => 3, 'id' => 'edit_description' ]); !!}
                </div>
            </div>
        </div> 
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->