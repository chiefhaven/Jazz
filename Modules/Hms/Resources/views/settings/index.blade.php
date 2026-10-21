@extends('layouts.app')
@section('title', __('messages.settings'))

@section('content')
    @include('hms::layouts.nav')
    <!-- Main content -->
    <section class="content">
        <!-- Custom Tabs -->
        @component('components.widget', ['class' => 'box-primary', 'title' => __('messages.settings') . ':'])
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#cn_1" data-toggle="tab" aria-expanded="true">
                            @lang('hms::lang.booking_prefix')
                        </a>
                    </li>
                    <li class="">
                        <a href="#cn_2" data-toggle="tab" aria-expanded="true">
                            @lang('lang_v1.customer_notifications')
                        </a>
                    </li>
                    <li class="">
                        <a href="#cn_3" data-toggle="tab" aria-expanded="true">
                            @lang('hms::lang.print_pdf')
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="cn_1">
                        <div class="row">
                            <div class="box-body">
                                {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })([
                                    'url' => action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'store']),
                                    'method' => 'post',
                                    'id' => 'hms_setting',
                                    'files' => true,
                                ]))->open() !!}
                                @php
                                    $settings = json_decode($busines->hms_settings);
                                @endphp
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! html()->label(__('hms::lang.booking_prefix') . '*', 'booking_prefix')->attributes([]) !!}
                                        {!! html()->text('booking_prefix', $settings->prefix ?? null)->attributes([
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => __('hms::lang.booking_prefix'),
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    {!! html()->input('submit', null, __('messages.submit'))->attributes(['class' => 'btn btn-success btn-big']) !!}
                                </div>
                
                                {!! html()->form()->close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="cn_2">
                        <h3>@lang('hms::lang.new_booking')</h3>
                        <div class="row">
                            {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })([
                                'url' => action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'store_email_template']),
                                'method' => 'post',
                            ]))->open() !!}
                            <div class="col-md-12">
                                <strong>@lang('lang_v1.available_tags'):</strong>
                                <p class="help-block">
                                    {{ implode(', ', $tags) }}
                                </p>
                            </div>
                            <div class="col-md-12 mt-10">
                                <div class="form-group">
                                    {!! html()->label(__('lang_v1.email_subject') . ':', 'subject')->attributes([]) !!}
                                    {!! html()->text('subject', empty($template->subject) ? null : $template->subject)->attributes([
                                        'class' => 'form-control',
                                        'placeholder' => __('lang_v1.email_subject'),
                                        'id' => 'subject',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! html()->label('CC:', 'cc')->attributes([]) !!}
                                    {!! html()->email('cc', empty($template->cc) ? null : $template->cc)->attributes([
                                        'class' => 'form-control',
                                        'placeholder' => 'CC',
                                        'id' => 'cc',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! html()->label('BCC:', 'bcc')->attributes([]) !!}
                                    {!! html()->email('bcc', empty($template->bcc) ? null : $template->bcc)->attributes([
                                        'class' => 'form-control',
                                        'placeholder' => 'BCC',
                                        'id' => 'bcc',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! html()->label(__('lang_v1.email_body') . ':', 'email_body')->attributes([]) !!}
                                    {!! html()->textarea('email_body', empty($template->email_body) ? null : $template->email_body)->attributes([
                                        'class' => 'form-control ckeditor',
                                        'placeholder' => __('lang_v1.email_body'),
                                        'id' => 'email_body',
                                        'rows' => 6,
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12 mt-15">
                                <label class="checkbox-inline">
                                    {!! html()->checkbox('auto_send', empty($template->auto_send) ? null : $template->auto_send, 1)->attributes([
                                        'class' => 'input-icheck',
                                    ]) !!} @lang('lang_v1.autosend_email')
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-danger btn-big">@lang('messages.save')</button>
                                </div>
                            </div>
                            {!! html()->form()->close() !!}
                        </div>
                    </div>
                    <div class="tab-pane" id="cn_3">
                            <div class="row">
                                <div class="box-body">
                                    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })([
                                        'url' => action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'post_pdf']),
                                        'method' => 'post',
                                        'id' => 'post_pdf',
                                        'files' => true,
                                    ]))->open() !!}
                                    @php
                                        $settings = json_decode($busines->hms_settings);
                                    @endphp
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! html()->label(__('hms::lang.footer_text'), 'footer_text')->attributes([]) !!}
                                            {!! html()->textarea('footer_text', $settings->booking_pdf->footer_text ?? null)->attributes([
                                                'class' => 'form-control',
                                                'placeholder' => __('hms::lang.footer_text'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        {!! html()->input('submit', null, __('messages.submit'))->attributes(['class' => 'btn btn-success btn-big']) !!}
                                    </div>
                    
                                    {!! html()->form()->close() !!}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        @endcomponent
    </section>
    <!-- /.content -->
@endsection

@section('javascript')
    <script type="text/javascript">
        tinymce.init({
            selector: 'textarea#email_body',
        });
        tinymce.init({
            selector: 'textarea#footer_text',
        });
    </script>
@endsection
