<div class="modal fade" id="change_status_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'changeStatus']), 'method' => 'post', 'id' => 'change_status_form' ]))->open() !!}

	    <div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">@lang( 'essentials::lang.change_status' )</h4>
	    </div>

	    <div class="modal-body">
	      	<div class="form-group">
	      		<input type="hidden" name="leave_id" id="leave_id">
	      		<label for="status">@lang( 'sale.status' ):*</label>
	      		<select class="form-control select2" name="status" required id="status_dropdown" style="width: 100%;">
	      			@foreach($leave_statuses as $key => $value)
	      				<option value="{{$key}}">{{$value['name']}}</option>
	      			@endforeach
	      		</select>
	      	</div>
	      	<div class="form-group">
	      		<label for="status_note">@lang( 'brand.note' ):</label>
	      		<textarea class="form-control" name="status_note" rows="3" id="status_note"></textarea>
	      	</div>
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary ladda-button update-leave-status" data-style="expand-right">
	      	<span class="ladda-label">@lang( 'messages.update' )</span>
	      </button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>

	    {!! html()->form()->close() !!}

	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>