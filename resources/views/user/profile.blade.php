@extends('layouts.app')
@section('title', __('lang_v1.my_profile'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.my_profile')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\UserController::class, 'updatePassword']), 'method' => 'post', 'id' => 'edit_password_form',
            'class' => 'form-horizontal' ]))->open() !!}
<div class="row">
    <div class="col-sm-12">
        <div class="box box-solid"> <!--business info box start-->
            <div class="box-header">
                <div class="box-header">
                    <h3 class="box-title"> @lang('user.change_password')</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    {!! html()->label(__('user.current_password') . ':', 'current_password')->attributes(['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! html()->password('current_password')->attributes(['class' => 'form-control','placeholder' => __('user.current_password'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! html()->label(__('user.new_password') . ':', 'new_password')->attributes(['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! html()->password('new_password')->attributes(['class' => 'form-control','placeholder' => __('user.new_password'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! html()->label(__('user.confirm_new_password') . ':', 'confirm_password')->attributes(['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! html()->password('confirm_password')->attributes(['class' => 'form-control','placeholder' =>  __('user.confirm_new_password'), 'required']); !!}
                        </div>
                    </div>
                </div>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right">@lang('messages.update')</button>
            </div>
        </div>
    </div>
</div>
{!! html()->form()->close() !!}
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\UserController::class, 'updateProfile']), 'method' => 'post', 'id' => 'edit_user_profile_form', 'files' => true ]))->open() !!}
<div class="row">
    <div class="col-sm-8">
        <div class="box box-solid"> <!--business info box start-->
            <div class="box-header">
                <div class="box-header">
                    <h3 class="box-title"> @lang('user.edit_profile')</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-md-2">
                    {!! html()->label(__('business.prefix') . ':', 'surname')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! html()->text('surname', $user->surname)->attributes(['class' => 'form-control','placeholder' => __('business.prefix_placeholder')]); !!}
                    </div>
                </div>
                <div class="form-group col-md-5">
                    {!! html()->label(__('business.first_name') . ':', 'first_name')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! html()->text('first_name', $user->first_name)->attributes(['class' => 'form-control','placeholder' => __('business.first_name'), 'required']); !!}
                    </div>
                </div>
                <div class="form-group col-md-5">
                    {!! html()->label(__('business.last_name') . ':', 'last_name')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! html()->text('last_name', $user->last_name)->attributes(['class' => 'form-control','placeholder' => __('business.last_name')]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! html()->label(__('business.email') . ':', 'email')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! html()->email('email', $user->email)->attributes(['class' => 'form-control','placeholder' => __('business.email') ]); !!}
                    </div>
                </div>
                <div class="form-group col-md-6">
                    {!! html()->label(__('business.language') . ':', 'language')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('language', $languages, $user->language, ['class' => 'form-control select2'])); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        @component('components.widget', ['title' => __('lang_v1.profile_photo')])
            @if(!empty($user->media))
                <div class="col-md-12 text-center">
                    {!! $user->media->thumbnail([150, 150], 'img-circle') !!}
                </div>
            @endif
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.upload_image') . ':', 'profile_photo')->attributes([]) !!}
                    {!! html()->file('profile_photo')->attributes(['id' => 'profile_photo', 'accept' => 'image/*']); !!}
                    <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p></small>
                </div>
            </div>
        @endcomponent
    </div>
</div>
@include('user.edit_profile_form_part', ['bank_details' => !empty($user->bank_details) ? json_decode($user->bank_details, true) : null])
<div class="row">
    <div class="col-md-12 text-center">
        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg">@lang('messages.update')</button>
    </div>
</div>
{!! html()->form()->close() !!}

</section>
<!-- /.content -->
@endsection