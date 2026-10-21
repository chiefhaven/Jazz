<div class="modal fade reminder" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
  {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\ReminderController::class, 'store']), 'id' => 'reminder_form']))->open() !!}
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalCenterTitle">
            @lang('essentials::lang.add_reminder')
          </h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @php
            $repeat = [
                      'one_time' => __('essentials::lang.one_time'),
                      'every_day' => __('essentials::lang.every_day'),
                      'every_week' => __('essentials::lang.every_week'),
                      'every_month' => __('essentials::lang.every_month'),
                        ];
            @endphp
          <div class="row">
            <div class="col-md-12">
              {!! html()->label(__('essentials::lang.event_name') . ":*", 'name')->attributes([]) !!}

                      {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required']) !!}
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
              {!! html()->label(__('essentials::lang.repeat') . ':*', 'repeat')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('repeat', $repeat, null, ['class' => 'form-control','required'])) !!}
            </div>
            <div class="col-md-6">
              <div class="form-group">
                {!! html()->label(__('essentials::lang.date') . ':*', 'date')->attributes([]) !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!! html()->text('date', @format_date('today'))->attributes(['class' => 'form-control datepicker', 'required', 'readonly']); !!}
                </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
              <div class="form-group">
                {!! html()->label(__('restaurant.start_time') . ':*', 'time')->attributes([]) !!}
                      <div class='input-group'>
                        <span class="input-group-addon">
                              <span class="glyphicon glyphicon-time"></span>
                          </span>
                {!! html()->text('time', @format_time('now'))->attributes(['class' => 'form-control', 'required', 'id' => 'time', 'readonly']); !!}
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                {!! html()->label(__('restaurant.end_time') . ':', 'time')->attributes([]) !!}
                      <div class='input-group'>
                        <span class="input-group-addon">
                              <span class="glyphicon glyphicon-time"></span>
                          </span>
                {!! html()->text('end_time', @format_time('now'))->attributes(['class' => 'form-control', 'id' => 'end_time', 'readonly']); !!}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            @lang('essentials::lang.cancel')
          </button>
          <button type="submit" class="btn btn-primary save_reminder">
            @lang('essentials::lang.submit')
          </button>
        </div>
      </div>
      {!! html()->form()->close() !!}
  </div>
</div>