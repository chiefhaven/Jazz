<div class="modal-dialog" role="document">
  	<div class="modal-content">
  		{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\ShiftController::class, 'postAssignUsers']), 'method' => 'post', 'id' => 'add_user_shift_form' ]))->open() !!}
  		<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">
	      		@lang( 'essentials::lang.assign_users' )
	      		(
	      			{{$shift->name}}
	      			@if($shift->type == 'fixed_shift')
	      			: {{@format_time($shift->start_time)}} - {{@format_time($shift->end_time)}}
	      			@endif
	      		)
	      	</h4>
	    </div>
	    <div class="modal-body">
	    	{!! html()->hidden('shift_id', $shift->id)->attributes([]); !!}
	    	<table class="table table-condensed">
	    		<thead>
	    			<tr>
	    				<th>&nbsp;</th>
	    				<th>@lang('report.user')</th>
	    				<th>@lang('business.start_date')</th>
	    				<th>@lang('essentials::lang.end_date')</th>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			@foreach($users as $key => $value)
	    				<tr>
	    					<td>{!! html()->checkbox('user_shift[' . $key . '][is_added]', array_key_exists($key, $user_shifts), 1)->attributes(['id' => 'user_check_' . $key ]); !!}</td>
	    					<td>{{$value}}</td>
	    					<td>
					        	<div class="input-group date">
					        		{!! html()->text('user_shift[' . $key . '][start_date]', !empty($user_shifts[$key]['start_date']) ? $user_shifts[$key]['start_date'] : null)->attributes(['class' => 'form-control date_picker', 'placeholder' => __( 'business.start_date' ), 'readonly', 'id' => 'user_shift[' . $key . '][start_date]']); !!}
					        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
					        	</div>
					        </td>
					        <td>
					        	<div class="input-group date">
					        		{!! html()->text('user_shift[' . $key . '][end_date]', !empty($user_shifts[$key]['end_date']) ? $user_shifts[$key]['end_date'] : null)->attributes(['class' => 'form-control date_picker', 'placeholder' => __( 'essentials::lang.end_date' ), 'readonly' ]); !!}
					        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
					        	</div>
					        </td>
	    				</tr>
	    			@endforeach
	    		</tbody>
	    	</table>
	    </div>
	    <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! html()->form()->close() !!}
  	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#add_user_shift_form').validate({
			rules: {
				@foreach($users as $key => $value)
				'user_shift[{{$key}}][start_date]': {
					required: function(element){
		            	return $('#user_check_{{$key}}').is(":checked");
		        	}
		    	},
				@endforeach
			}
		});
	});
</script>