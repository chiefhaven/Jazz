@extends('layouts.app')
@section('title', __( 'connector::lang.clients' ))

@section('vue')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'connector::lang.clients' )</h1>
</section>

@if(empty($is_demo))
<section class="content">
	@component('components.widget', ['class' => 'box-solid', 'title' => __( 'connector::lang.clients' )])
        @slot('tool')
            <div class="box-tools">
                @if($can_manage_clients)
                    <a href="{{action([\Modules\Connector\Http\Controllers\ClientController::class, 'regenerate'])}}" class="btn btn-block btn-default" >
                    <i class="fas fa-plus"></i> @lang( 'connector::lang.regenerate_doc' )</a>
                @endif
                
                @if($can_manage_clients)
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-toggle="modal" 
                        data-target="#create_client_modal">
                        <i class="fas fa-plus"></i> @lang( 'connector::lang.create_client' )</button>
                @endif
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="clients_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>@lang( 'user.name' )</th>
                        <th>@lang( 'connector::lang.client_secret' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
                <tbody>
                	@foreach($clients as $client)
                		<tr>
                			<td>{{$client->id}}</td>
                			<td>{{$client->name}}</td>
                			<td>{{$client->secret}}</td>
                			<td>{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Connector\Http\Controllers\ClientController::class, 'destroy'], [$client->id]), 'method' => 'delete', 'id' => 'create_client_form' ]))->open() !!}<button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> @lang( 'messages.delete' )</button>{!! html()->form()->close() !!}</td>
                		</tr>
                	@endforeach
                </tbody>
            </table>
        </div>
    @endcomponent
</section>
@else
<section>
    <div class="col-md-12 text-danger">
        <br/>
        @lang('lang_v1.disabled_in_demo')
    </div>
</section>
@endif



<!-- Create Client Modal -->
<div class="modal fade" id="create_client_modal" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Connector\Http\Controllers\ClientController::class, 'store']), 'method' => 'post', 'id' => 'create_client_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'connector::lang.create_client' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! html()->label(__( 'user.name' ) . ':*', 'name')->attributes([]) !!}
          {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'user.name' ) ]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){
		clients_table = $('#clients_table').DataTable();
	});
</script>
@endsection