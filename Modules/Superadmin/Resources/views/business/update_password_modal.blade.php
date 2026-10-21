<div class="modal fade" id="update_password_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
	  	<div class="modal-content">
	  	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Superadmin\Http\Controllers\BusinessController::class, 'updatePassword']), 'method' => 'post', 'id' => 'password_update_form' ]))->open() !!}
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title"><span id="user_name"></span> - @lang( 'superadmin::lang.update_password' )</h4>
	    </div>

	    <div class="modal-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
					    {!! html()->label(__( 'business.password' ) . ':', 'password')->attributes([]) !!}
					    {!! html()->password('password')->attributes(['class' => 'form-control', 'placeholder' => __( 'business.password' ), 'required' ]); !!}
					    {!! html()->hidden('user_id', null)->attributes(['id' => 'user_id' ]); !!}
					</div>
				</div>
				<div class="col-md-6">
				    <div class="form-group">
					    {!! html()->label(__( 'business.confirm_password' ) . ':', 'confirm_password')->attributes([]) !!}
					    {!! html()->password('confirm_password')->attributes(['class' => 'form-control', 'placeholder' => __( 'business.confirm_password' ), 'required', 'data-rule-equalTo' => '#password' ]); !!}
					      
					</div>
				</div>
			</div>
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! html()->form()->close() !!}
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->	
</div>