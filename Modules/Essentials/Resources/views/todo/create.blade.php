<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'store']), 'id' => 'task_form', 'method' => 'post']))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.add_to_do' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="col-md-12">
		        <div class="form-group">
		            {!! html()->label(__('essentials::lang.task') . ":*", 'task')->attributes([])!!}
		            {!! html()->text('task', null)->attributes(['class' => 'form-control', 'required']) !!}
		         </div>
		    </div>
		    @if(!empty($users))
		    @can('essentials.assign_todos')
			<div class="col-md-12">
		        <div class="form-group">
					{!! html()->label(__('essentials::lang.assigned_to') . ':*', 'users')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('users[]', $users, null, ['class' => 'form-control select2', 'multiple', 'required', 'style' => 'width: 100%;'])); !!}
					</div>
				</div>
			</div>
			@endcan
			@endif
			<div class="clearfix"></div>
			<div class="col-md-6">
		        <div class="form-group">
					{!! html()->label(__('essentials::lang.priority') . ':', 'priority')->attributes([]) !!}
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('priority', $priorities, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;'])); !!}
				</div>
			</div>
			<div class="col-md-6">
		        <div class="form-group">
					{!! html()->label(__('sale.status') . ':', 'status')->attributes([]) !!}
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('status', $task_statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;'])); !!}
				</div>
			</div>
			<div class="clearfix"></div>
		    <div class="col-md-6">
		        <div class="form-group">
					{!! html()->label(__('business.start_date') . ':*', 'date')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! html()->text('date', @format_datetime('now'))->attributes(['class' => 'form-control datepicker text-center', 'required', 'readonly']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-6">
		        <div class="form-group">
					{!! html()->label(__('essentials::lang.end_date') . ':', 'end_date')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! html()->text('end_date', '')->attributes(['class' => 'form-control datepicker text-center', 'readonly']); !!}
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-6">
		        <div class="form-group">
					{!! html()->label(__('essentials::lang.estimated_hours') . ':', 'estimated_hours')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fas fa-clock"></i>
						</span>
						{!! html()->text('estimated_hours', null)->attributes(['class' => 'form-control']); !!}
					</div>
				</div>
			</div>
		    <div class="clearfix"></div>
	    	<div class="col-md-12">
	    		<div class="form-group">
					{!! html()->label(__('lang_v1.description') . ':', 'to_do_description')->attributes([]) !!}
					{!! html()->textarea('description', null)->attributes(['id' => 'to_do_description']); !!}
				</div>
	    	</div>
	    	<div class="col-md-12">
	        	<div class="form-group">
                    <label for="media_upload">
                        @lang('lang_v1.upload_documents'):
                    </label>
                    <div class="dropzone" id="media_upload"></div>
                    {{-- params for media upload --}}
				    <input type="hidden" id="media_upload_url" value="{{route('attach.medias.to.model')}}">
				    <input type="hidden" id="model_id" value="">
				    <input type="hidden" id="model_type" value="Modules\Essentials\Entities\ToDo">
				    <input type="hidden" id="model_media_type" value="">
                </div>
	        </div>
    	</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right">
      	<span class="ladda-label">@lang( 'messages.save' )</span>
      </button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->