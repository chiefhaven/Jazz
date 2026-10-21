@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.edit_page'))

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.edit_page')</h1>
</section>

<!-- Main content -->
<section class="content">

	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Superadmin\Http\Controllers\PageController::class, 'update'], $page->id), 'method' => 'put', 'id' => 'add_page_form']))->open() !!}

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.page_title').':', 'title')->attributes([]) !!}
						{!! html()->text('title', $page->title)->attributes(['class' => 'form-control', 'placeholder' => __('superadmin::lang.page_title')]); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.slug').':', 'slug')->attributes([]) !!}
						{!! html()->text('slug', $page->slug)->attributes(['class' => 'form-control', 'placeholder' => __('superadmin::lang.slug'), 'required']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.menu_order').':', 'menu_order')->attributes([]) !!} @show_tooltip(__('superadmin::lang.menu_order_tooltip'))
						{!! html()->input('number', 'menu_order', $page->menu_order)->attributes(['class' => 'form-control', 'placeholder' => __('superadmin::lang.menu_order')]); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="checkbox">
					<label>
						{!! html()->checkbox('is_shown', !empty($page->is_shown), 1)->attributes(['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.is_visible')}}
					</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						{!! html()->label(__('superadmin::lang.page_content').':', 'content')->attributes([]) !!}
						{!! html()->textarea('content', $page->content)->attributes(['class' => 'form-control', 'placeholder' => __('superadmin::lang.page_content')]); !!}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
		</div>
	</div>

	{!! html()->form()->close() !!}
</section>

@endsection

@section('javascript')
	@include('superadmin::pages.form_script')
@endsection