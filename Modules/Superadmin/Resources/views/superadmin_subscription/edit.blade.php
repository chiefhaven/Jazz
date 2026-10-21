<!-- Modal -->
<div class="modal-dialog" role="document">
    <div class="modal-content">
     {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController::class, 'update'],$subscription->id), 'method' => 'PUT', 'id' => 'status_change_form']))->open() !!}

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">@lang( "superadmin::lang.subscription_status")</h4>
      </div>

      <div class="modal-body">
             <div class="form-group">
                {!! html()->label(__( "superadmin::lang.status"), 'status')->attributes([]) !!}

                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('status', $status, $subscription->status, ['class' => 'form-control'])); !!}
              </div>

              <div class="form-group">
                {!! html()->label(__("superadmin::lang.payment_transaction_id"), 'payment_transaction_id')->attributes([])!!}

                {!! html()->text('payment_transaction_id', $subscription->payment_transaction_id)->attributes(['class' => 'form-control']);!!}
              </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( "superadmin::lang.update")</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( "superadmin::lang.close")</button>
      </div>
      {!! html()->form()->close() !!}
    </div>
</div>
