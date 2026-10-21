<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\AccountController::class, 'store']), 'method' => 'post', 'id' => 'payment_account_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.add_account' )</h4>
    </div>

    <div class="modal-body">
            <div class="form-group">
                {!! html()->label(__( 'lang_v1.name' ) .":*", 'name')->attributes([]) !!}
                {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__( 'account.account_number' ) .":*", 'account_number')->attributes([]) !!}
                {!! html()->text('account_number', null)->attributes(['class' => 'form-control', 'required','placeholder' => __( 'account.account_number' ) ]); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__( 'account.account_type' ) .":", 'account_type_id')->attributes([]) !!}
                <select name="account_type_id" class="form-control select2">\
                    <option>@lang('messages.please_select')</option>
                    @foreach($account_types as $account_type)
                        <optgroup label="{{$account_type->name}}">
                            <option value="{{$account_type->id}}">{{$account_type->name}}</option>
                            @foreach($account_type->sub_types as $sub_type)
                                <option value="{{$sub_type->id}}">{{$sub_type->name}}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                {!! html()->label(__( 'account.opening_balance' ) .":", 'opening_balance')->attributes([]) !!}
                {!! html()->text('opening_balance', 0)->attributes(['class' => 'form-control input_number','placeholder' => __( 'account.opening_balance' ) ]); !!}
            </div>

            <label>@lang('lang_v1.account_details'):</label>
            <table class="table table-striped">
                <tr>
                    <th>
                        @lang('lang_v1.label')
                    </th>
                    <th>
                        @lang('product.value')
                    </th>
                </tr>
                @for ($i = 0; $i < 6; $i++)
                    <tr>
                        <td>
                            {!! html()->text('account_details['.$i.'][label]', null)->attributes(['class' => 'form-control']); !!}
                        </td>
                        <td>
                            {!! html()->text('account_details['.$i.'][value]', null)->attributes(['class' => 'form-control']); !!}      
                        </td>
                    </tr>
                @endfor
            </table>
        
            <div class="form-group">
                {!! html()->label(__( 'brand.note' ), 'note')->attributes([]) !!}
                {!! html()->textarea('note', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->