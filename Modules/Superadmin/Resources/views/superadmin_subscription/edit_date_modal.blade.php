<!-- Modal -->
<div class="modal-dialog" role="document">
    <div class="modal-content edit-subscription-modal">
     {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController::class, 'updateSubscription']), 'method' => 'POST', 'id' => 'edit_subscription_form']))->open() !!}
      {!! html()->hidden('subscription_id', $subscription->id)->attributes([]); !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">@lang( "superadmin::lang.edit_subscription")</h4>
      </div>
      <div class="modal-body">
             <div class="form-group">
                {!! html()->label(__( "superadmin::lang.start_date"), 'start_date')->attributes([]) !!}

                {!! html()->text('start_date', !empty($subscription->start_date) ? @format_date($subscription->start_date) : null)->attributes(['class' => 'form-control datepicker', 'readonly']); !!}
              </div>

              <div class="form-group">
                {!! html()->label(__("superadmin::lang.end_date"), 'end_date')->attributes([])!!}

                {!! html()->text('end_date', !empty($subscription->end_date) ? @format_date($subscription->end_date) : null)->attributes(['class' => 'form-control datepicker', 'readonly']);!!}
              </div>

              <div class="form-group">
                {!! html()->label(__("superadmin::lang.trial_end_date"), 'trial_end_date')->attributes([])!!}

                {!! html()->text('trial_end_date', !empty($subscription->trial_end_date) ? @format_date($subscription->trial_end_date) : null)->attributes(['class' => 'form-control datepicker', 'readonly']);!!}
              </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( "superadmin::lang.update")</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( "superadmin::lang.close")</button>
      </div>
      {!! html()->form()->close() !!}
    </div>
</div>
