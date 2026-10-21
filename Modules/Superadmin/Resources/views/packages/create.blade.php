@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))

@section('content')
@include('superadmin::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.packages') <small>@lang('superadmin::lang.add_package')</small></h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency->decimal_separator}}">

	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Superadmin\Http\Controllers\PackagesController::class, 'store']), 'method' => 'post', 'id' => 'add_package_form']))->open() !!}

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				
				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('lang_v1.name').':', 'name')->attributes([]) !!}
						{!! html()->text('name', null)->attributes(['class' => 'form-control', 'required']); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.description').':', 'description')->attributes([]) !!}
						{!! html()->text('description', null)->attributes(['class' => 'form-control', 'required']); !!}
					</div>
				</div>

				<div class="clearfix"></div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.location_count').':', 'location_count')->attributes([]) !!}
						{!! html()->input('number', 'location_count', null)->attributes(['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.user_count').':', 'user_count')->attributes([]) !!}
						{!! html()->input('number', 'user_count', null)->attributes(['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>
				<div class="clearfix"></div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.product_count').':', 'product_count')->attributes([]) !!}
						{!! html()->input('number', 'product_count', null)->attributes(['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.invoice_count').':', 'invoice_count')->attributes([]) !!}
						{!! html()->input('number', 'invoice_count', null)->attributes(['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>
				<div class="clearfix"></div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.interval').':', 'interval')->attributes([]) !!}

						{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('interval', $intervals, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'])); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.interval_count').':', 'interval_count	')->attributes([]) !!}
						{!! html()->input('number', 'interval_count', null)->attributes(['class' => 'form-control', 'required', 'min' => 1]); !!}
					</div>
				</div>
				<div class="clearfix"></div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.trial_days').':', 'trial_days	')->attributes([]) !!}
						{!! html()->input('number', 'trial_days', null)->attributes(['class' => 'form-control', 'required', 'min' => 0]); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.price').':', 'price')->attributes([]) !!}
						@show_tooltip(__('superadmin::lang.tooltip_pkg_price'))

						<div class="input-group">
							<span class="input-group-addon" id="basic-addon3"><b>{{$currency->code}} {{$currency->symbol}}</b></span>
							{!! html()->text('price', null)->attributes(['class' => 'form-control input_number', 'required']); !!}
						</div>
						<span class="help-block">
							0 = @lang('superadmin::lang.free_package')
						</span>
					</div>
				</div>
				<div class="clearfix"></div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.sort_order').':', 'sort_order	')->attributes([]) !!}
						{!! html()->input('number', 'sort_order', 1)->attributes(['class' => 'form-control', 'required']); !!}
					</div>
				</div>

				<div class="clearfix"></div>
				<div class="col-sm-6">
					<div class="checkbox">
					<label>
						{!! html()->checkbox('is_private', false, 1)->attributes(['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.private_superadmin_only')}}
					</label>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="checkbox">
					<label>
						{!! html()->checkbox('is_one_time', false, 1)->attributes(['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.one_time_only_subscription')}}
					</label>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-4">
					<div class="checkbox">
					<label>
						{!! html()->checkbox('enable_custom_link', false, 1)->attributes(['class' => 'input-icheck', 'id' => 'enable_custom_link']); !!}
                        {{__('superadmin::lang.enable_custom_subscription_link')}}
					</label>
					</div>
				</div>
				<div id="custom_link_div" class="hide">
					<div class="col-sm-4">
						<div class="form-group">
							{!! html()->label(__('superadmin::lang.custom_link').':', 'custom_link')->attributes([]) !!}
							{!! html()->text('custom_link', null)->attributes(['class' => 'form-control']); !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							{!! html()->label(__('superadmin::lang.custom_link_text').':', 'custom_link_text')->attributes([]) !!}
							{!! html()->text('custom_link_text', null)->attributes(['class' => 'form-control']); !!}
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				@foreach($permissions as $module => $module_permissions)
					@foreach($module_permissions as $permission)
					<div class="col-sm-3">
                        @if(isset($permission['field_type']) && in_array($permission['field_type'], ['number', 'input']))
                        <div class="form-group">
							{!! html()->label($permission['label'].':', "custom_permissions[$permission[name]]")->attributes([]) !!} 
                            @if(isset($permission['tooltip']))
                                @show_tooltip($permission['tooltip'])
                            @endif
                            
							{!! html()->text("custom_permissions[$permission[name]]", null)->attributes(['class' => 'form-control', 'type' => $permission['field_type']]); !!} 
						</div>
                        @else
                            <div class="checkbox">
                            <label>
                                {!! html()->checkbox("custom_permissions[$permission[name]]", $permission['default'], 1)->attributes(['class' => 'input-icheck']); !!}
                                {{$permission['label']}}
                            </label>
                            </div>
                        @endif
					</div>
					@endforeach
				@endforeach

				<div class="col-sm-3">
					<div class="checkbox">
					<label>
						{!! html()->checkbox('is_active', true, 1)->attributes(['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.is_active')}}
					</label>
					</div>
				</div>

				
			</div>

			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
				</div>
			</div>

		</div>
	</div>

	{!! html()->form()->close() !!}
</section>

@endsection

@section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){
			$('form#add_package_form').validate();
		});
		$('#enable_custom_link').on('ifChecked', function(event){
		   $("div#custom_link_div").removeClass('hide');
		});
		$('#enable_custom_link').on('ifUnchecked', function(event){
		   $("div#custom_link_div").addClass('hide');
		});
	</script>
@endsection