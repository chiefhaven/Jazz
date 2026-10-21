@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.communicator'))

@section('content')
@include('superadmin::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.communicator')</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box box-solid">
				<div class="box-header">
					<i class="fa fa-edit"></i>
					<h3 class="box-title">@lang('superadmin::lang.compose_message')</h3>
				</div>
		        <div class="box-body">
		        	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Superadmin\Http\Controllers\CommunicatorController::class, 'send']), 'method' => 'post', 'id' => 'communication_form']))->open() !!}
		        		<div class="col-md-12 form-group">
		        			{!! html()->label(__('superadmin::lang.recipients').':*', 'recipients')->attributes([]) !!} <button type="button" class="btn btn-primary btn-xs select-all">@lang('lang_v1.select_all')</button> <button type="button" class="btn btn-primary btn-xs deselect-all">@lang('lang_v1.deselect_all')</button>
							{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('recipients[]', $businesses, null, ['class' => 'form-control select2', 'required', 'multiple', 'id' => 'recipients'])); !!}
		        		</div>
		        		<div class="col-md-12 form-group">
		        			{!! html()->label(__('superadmin::lang.subject').':*', 'subject')->attributes([]) !!}
		        			{!! html()->text('subject', null)->attributes(['class' => 'form-control', 'required']); !!}
		        		</div>
		        		<div class="col-md-12 form-group">
		        			{!! html()->label(__('superadmin::lang.message').':*', 'message')->attributes([]) !!}
		        			{!! html()->textarea('message', null)->attributes(['class' => 'form-control', 'required', 'rows' => 6]); !!}
		        		</div>
		        		<div class="col-md-12 form-group">
		        			<button type="submit" class="btn btn-danger pull-right" id="send_message">@lang('superadmin::lang.send')</button>
		        		</div>
		        	{!! html()->form()->close() !!}
		        </div>
		    </div>
		</div>
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header">
					<i class="fa fa-history"></i>
					<h3 class="box-title">@lang('superadmin::lang.message_history')</h3>
				</div>
		        <div class="box-body">
		        	<table class="table" id="message-history">
		        		<thead>
		        			<tr>
		        				<th>@lang('superadmin::lang.subject')</th>
		        				<th>@lang('superadmin::lang.message')</th>
		        				<th>@lang('lang_v1.date')</th>
		        			</tr>
		        		</thead>
		        	</table>
		        </div>
		     </div>
		</div>

	</div>
</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
	$(document).ready( function() {
		$('#send_message').click(function(e){
			e.preventDefault();
			if($('form#communication_form').valid()){
				swal({
	              title: LANG.sure,
	              icon: "warning",
	              buttons: true,
	              dangerMode: false,
	            }).then((sure) => {
	            	if(sure){
	            		$('form#communication_form').submit();
	            	} else {
	            		return false;
	            	}
	            });
	        }
		});

		$('#message-history').DataTable({
			dom:'lfrtip',
			processing: true,
			serverSide: true,
			ajax: '{{action([\Modules\Superadmin\Http\Controllers\CommunicatorController::class, 'getHistory'])}}'
	    });

	    init_tinymce('message');
	});
</script>
@endsection