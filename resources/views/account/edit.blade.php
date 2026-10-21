<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\AccountController::class, 'update'],$account->id), 'method' => 'PUT', 'id' => 'edit_payment_account_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.edit_account' )</h4>
    </div>

    <div class="modal-body">
            <div class="form-group">
                {!! html()->label(__( 'lang_v1.name' ) .":*", 'name')->attributes([]) !!}
                {!! html()->text('name', $account->name)->attributes(['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
            </div>

             <div class="form-group">
                {!! html()->label(__( 'account.account_number' ) .":*", 'account_number')->attributes([]) !!}
                {!! html()->text('account_number', $account->account_number)->attributes(['class' => 'form-control', 'required','placeholder' => __( 'account.account_number' ) ]); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__( 'account.account_type' ) .":", 'account_type_id')->attributes([]) !!}
                <select name="account_type_id" class="form-control select2">
                    <option>@lang('messages.please_select')</option>
                    @foreach($account_types as $account_type)
                        <optgroup label="{{$account_type->name}}">
                            <option value="{{$account_type->id}}" @if($account->account_type_id == $account_type->id) selected @endif >{{$account_type->name}}</option>
                            @foreach($account_type->sub_types as $sub_type)
                                <option value="{{$sub_type->id}}" @if($account->account_type_id == $sub_type->id) selected @endif >{{$sub_type->name}}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
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
                @if(!empty($account->account_details))
                    @foreach($account->account_details as $key => $account_detail)
                        <tr>
                            <td>
                                {!! html()->text('account_details['.$key.'][label]', !empty($account->account_details[$key]['label'])? $account->account_details[$key]['label'] : null)->attributes(['class' => 'form-control']); !!}
                            </td>
                            <td>
                                {!! html()->text('account_details['.$key.'][value]', !empty($account->account_details[$key]['value'])?$account->account_details[$key]['value']:null)->attributes(['class' => 'form-control']); !!}      
                            </td>
                        </tr>
                    @endforeach
                @else
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
                @endif
            </table>
            
            <div class="form-group">
                {!! html()->label(__( 'brand.note' ), 'note')->attributes([]) !!}
                {!! html()->textarea('note', $account->note)->attributes(['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->