<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\SalesCommissionAgentController::class, 'store']), 'method' => 'post', 'id' => 'sale_commission_agent_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_sales_commission_agent' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-2">
        <div class="form-group">
          {!! html()->label(__( 'business.prefix' ) . ':', 'surname')->attributes([]) !!}
            {!! html()->text('surname', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! html()->label(__( 'business.first_name' ) . ':*', 'first_name')->attributes([]) !!}
            {!! html()->text('first_name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! html()->label(__( 'business.last_name' ) . ':', 'last_name')->attributes([]) !!}
            {!! html()->text('last_name', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-6">
        <div class="form-group">
          {!! html()->label(__( 'business.email' ) . ':', 'email')->attributes([]) !!}
            {!! html()->text('email', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.email' ) ]); !!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          {!! html()->label(__( 'lang_v1.contact_no' ) . ':', 'contact_no')->attributes([]) !!}
            {!! html()->text('contact_no', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.contact_no' ) ]); !!}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          {!! html()->label(__( 'business.address' ) . ':', 'address')->attributes([]) !!}
            {!! html()->textarea('address', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.address'), 'rows' => 3 ]); !!}
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! html()->label(__( 'lang_v1.cmmsn_percent' ) . ':', 'cmmsn_percent')->attributes([]) !!}
            {!! html()->text('cmmsn_percent', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ), 'required' ]); !!}
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