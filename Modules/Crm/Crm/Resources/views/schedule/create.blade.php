<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'store']), 'method' => 'post', 'id' => 'add_schedule' ]))->open() !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    @lang('crm::lang.add_schedule')
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="schedule_for" value="{{$schedule_for}}" id="schedule_for">
                    <div class="col-md-8">
                       <div class="form-group">
                            {!! html()->label(__('crm::lang.title') . ':*', 'title')->attributes([])!!}
                            {!! html()->text('title', null)->attributes(['class' => 'form-control', 'required' ]) !!}
                       </div>
                    </div>
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! html()->label(__('contact.customer') . '/' . __('crm::lang.lead') .':*', 'contact_id')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('contact_id', $customers, $contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                       </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! html()->label(__('sale.status') .':', 'status')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('status', $statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'follow_up_create_status'])); !!}
                       </div>
                    </div>
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! html()->label(__('crm::lang.start_datetime') . ':*', 'start_datetime')->attributes([])!!}
                            {!! html()->text('start_datetime', null)->attributes(['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
                       </div>
                    </div>
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! html()->label(__('crm::lang.end_datetime') . ':*', 'end_datetime')->attributes([])!!}
                            {!! html()->text('end_datetime', null)->attributes(['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
                       </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.description') . ':', 'description')->attributes([]) !!}
                            {!! html()->textarea('description', null)->attributes(['class' => 'form-control ', 'id' => 'description']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.schedule_type') .':*', 'schedule_type')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('schedule_type', $follow_up_types, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.followup_category') .':*', 'followup_category_id')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('followup_category_id', $followup_category, null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')])); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.assgined') .':*', 'user_id')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('user_id[]', $users, null, ['class' => 'form-control select2', 'multiple', 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>
                                {!! html()->checkbox('allow_notification', false, 1)->attributes(['class' => 'input-icheck', 'id' => 'allow_notification']); !!}
                                @lang('crm::lang.send_notification')
                            </label>
                            @show_tooltip(__('crm::lang.send_schedule_notificatoion'))
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="allow_notification_elements hide">
                        <div class="col-md-6">
                            {!! html()->label(__('crm::lang.notify_via') .':*', 'notify_via')->attributes([]) !!} 
                            <div class="form-group checkbox-inline">
                                <label>
                                    {!! html()->checkbox('notify_via[sms]', false, 1)->attributes(['class' => 'input-icheck']); !!}
                                    @lang('crm::lang.sms')
                                </label>
                            </div>
                            <div class="form-group checkbox-inline">
                                <label>
                                    {!! html()->checkbox('notify_via[mail]', true, 1)->attributes(['class' => 'input-icheck']); !!}
                                    @lang('business.email')
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="multi-input">
                                    {!! html()->label(__('crm::lang.notify_before') . ':*', 'notify_before')->attributes([]) !!}
                                    <br/>
                                    {!! html()->input('number', 'notify_before', null)->attributes(['class' => 'form-control width-40 pull-left', 'placeholder' => __('crm::lang.notify_before'), 'required']); !!}

                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('notify_type', $notify_type, '', ['class' => 'form-control width-60 pull-left'])); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                @lang('messages.close')
                </button>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.save')
                </button>
            </div>
        {!! html()->form()->close() !!}
    </div>
</div>