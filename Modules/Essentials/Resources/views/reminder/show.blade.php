<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalCenterTitle">
            @lang('essentials::lang.reminder_details')
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
                <strong> @lang('essentials::lang.event_name') : </strong> {{ $reminder->name }}
            </div>
            <div class="col-md-6">
                <strong> @lang('essentials::lang.date') : </strong> {{ @format_date($reminder->date )}} <br>

                <strong> @lang('restaurant.start_time') : </strong> {{ $time }} <br>
                <strong> @lang('restaurant.end_time') : </strong> @if(!empty($reminder->end_time)){{ @format_time($reminder->end_time) }} @endif
            </div>
          </div>
          <br>
          <hr>
          <div class="row">
              <div class="col-md-9">
                  {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\ReminderController::class, 'update'], [$reminder->id]), 'method' => 'PUT', 'id' => 'update_reminder_repeat' ]))->open() !!}
                    <div class="input-group">
                      <!-- /btn-group -->
                      {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('repeat', $repeat, $reminder->repeat, ['class' => 'form-control', 'required'])) !!}
                      <div class="input-group-btn">
                        <button type="submit" class="btn btn-primary change_reminder_repeat">@lang('messages.update')</button>
                      </div>
                   </div>
                  {!! html()->form()->close() !!}
              </div>
              <div class="col-md-3">
                <button type="button" class="btn btn-danger" id="delete_reminder" data-href="{{action([\Modules\Essentials\Http\Controllers\ReminderController::class, 'destroy'], [$reminder->id])}}">
                  @lang('essentials::lang.delete_reminder')
                </button>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          @lang( 'messages.close' )
        </button>
      </div>
    </div>
</div>