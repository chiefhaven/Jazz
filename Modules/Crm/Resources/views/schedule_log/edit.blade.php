<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Crm\Http\Controllers\ScheduleLogController::class, 'update'], ['id' => $schedule_log->id]), 'method' => 'put', 'id' => 'schedule_log_form' ]))->open() !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    @lang('crm::lang.edit_schedule_log')
                </h4>
            </div>
            <div class="modal-body">
                <!-- schedule id -->
                <input type="hidden" name="schedule_id" value="{{$schedule->id}}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.subject') . ':*', 'subject')->attributes([]) !!}
                            {!! html()->text('subject', $schedule_log->subject)->attributes(['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.log_type') .':*', 'log_type')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('log_type', ['call' => __('crm::lang.call'), 'sms' => __('crm::lang.sms'), 'meeting' => __('crm::lang.meeting'), 'email' => __('business.email')], $schedule_log->log_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! html()->label(__('crm::lang.start_datetime') . ':*', 'start_datetime')->attributes([])!!}
                            {!! html()->text('start_datetime', !empty($schedule_log->start_datetime) ? @format_datetime($schedule_log->start_datetime) : '')->attributes(['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
                       </div>
                    </div>
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! html()->label(__('crm::lang.end_datetime') . ':*', 'end_datetime')->attributes([])!!}
                            {!! html()->text('end_datetime', !empty($schedule_log->end_datetime) ? @format_datetime($schedule_log->end_datetime) : '')->attributes(['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
                       </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.description') . ':', 'description')->attributes([]) !!}
                            {!! html()->textarea('description', $schedule_log->description)->attributes(['class' => 'form-control ', 'id' => 'description']); !!}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! html()->label(__('crm::lang.schedule_status') .':', 'status')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('status', $statuses, $schedule->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;'])); !!}
                       </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                @lang('messages.close')
                </button>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.update')
                </button>
            </div>
        {!! html()->form()->close() !!}
    </div>
</div>