<div class="modal-dialog" role="document">
	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Crm\Http\Controllers\ContactLoginController::class, 'update'], [$user->id]), 'method' => 'put', 'id' => 'contact_login_edit' ]))->open() !!}
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title" id="myModalLabel">
				@lang('crm::lang.edit_login')
			</h4>
		</div>
		<div class="modal-body">
			<input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
			<div class="row">
				<div class="col-md-2">
			        <div class="form-group">
			         	{!! html()->label(__( 'business.prefix' ) . ':', 'surname')->attributes([]) !!}
			         	{!! html()->text('surname', $user->surname)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
			        </div>
			    </div>
			    <div class="col-md-5">
			        <div class="form-group">
						{!! html()->label(__( 'business.first_name' ) . ':*', 'first_name')->attributes([]) !!}
						{!! html()->text('first_name', $user->first_name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
			        </div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						{!! html()->label(__( 'business.last_name' ) . ':', 'last_name')->attributes([]) !!}
						{!! html()->text('last_name', $user->last_name)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
					</div>
				</div>
		      	<div class="clearfix"></div>
		      	@if(!empty($contacts))
		      		<div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('contact.contact') .':*', 'crm_contact_id')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('crm_contact_id', $contacts, $user->crm_contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                @else
				<input type="hidden" name="crm_contact_id" value="{{$user->crm_contact_id}}">
		      	@endif
		      	<div class="col-md-6">
					<div class="form-group">
						{!! html()->label(__( 'business.email' ) . ':*', 'email')->attributes([]) !!}
						{!! html()->text('email', $user->email)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.email' ) ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					    {!! html()->label(__( 'lang_v1.mobile_number' ) . ':', "contact_no")->attributes([]) !!}
					    {!! html()->text('contact_no', $user->contact_no)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.mobile_number')]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
				    	{!! html()->label(__( 'business.alternate_number' ) . ':', 'alt_number')->attributes([]) !!}
				    	{!! html()->text('alt_number', !empty($user->alt_number) ? $user->alt_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.alternate_number') ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
				    	{!! html()->label(__( 'lang_v1.family_contact_number' ) . ':', 'family_number')->attributes([]) !!}
				    	{!! html()->text('family_number', !empty($user->family_number) ? $user->family_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.family_contact_number') ]); !!}
				    </div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					    {!! html()->label(__( 'lang_v1.department' ) . ':', "crm_department")->attributes([]) !!}
					    {!! html()->text("crm_department", !empty($user->crm_department) ? $user->crm_department : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.department'), 'id'=>"crm_department" ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					    {!! html()->label(__( 'lang_v1.designation' ) . ':', "crm_designation")->attributes([]) !!}
					    {!! html()->text("crm_designation", !empty($user->crm_designation) ? $user->crm_designation : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.designation'), 'id'=>"crm_designation" ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! html()->label(__( 'business.password' ) . ':', 'password')->attributes([]) !!}
						{!! html()->password('password')->attributes(['class' => 'form-control', 'placeholder' => __( 'business.password' ) ]); !!}
						<p class="help-block">@lang('user.leave_password_blank')</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! html()->label(__( 'business.confirm_password' ) . ':', 'confirm_password')->attributes([]) !!}
						{!! html()->password('confirm_password')->attributes(['class' => 'form-control', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
					</div>
				</div>
		      	<div class="clearfix"></div>
		      	<div class="col-md-6">
			        <div class="form-group">
			          {!! html()->label(__( 'lang_v1.cmmsn_percent' ) . ':', 'cmmsn_percent')->attributes([]) !!}
			            {!! html()->text('cmmsn_percent', $user->cmmsn_percent)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ) ]); !!}
			        </div>
			    </div>
				<div class="col-md-4">
					@php
						$status = $user->status;
						$is_active = !empty($status) && $status == 'active' ? true : false;
					@endphp
					<div class="form-group">
						<br>
						<label>
							{!! html()->checkbox('is_active', $is_active, 'active')->attributes(['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
						</label>
						@show_tooltip(__('lang_v1.tooltip_enable_user_active'))
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">
				@lang( 'messages.close' )
			</button>
			<button type="submit" class="btn btn-primary">
				@lang( 'messages.update' )
			</button>
		</div>
	</div>
	{!! html()->form()->close() !!}
</div>