@extends('layouts.auth')

@section('content')

<div class="row">

    <h1 class="page-header text-center">{{ config('app.name', 'ultimatePOS') }}</h2>
    
    <div class="col-md-8 col-md-offset-2">
        
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title text-center">Register and Get Started in minutes</h3>
            </div>

            {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => {{ route('business.postRegister') }}]))->open() !!}
            {!! html()->token(); !!}

                <!-- /.box-header -->
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label('Business Name:', 'name')->attributes([]) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-suitcase"></i>
                                </span>
                                {!! html()->text('name', null)->attributes(['class' => 'form-control','placeholder' => 'Business name']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Start Date:', 'start_date')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! html()->text('start_date', null)->attributes(['class' => 'form-control start-date-picker','placeholder' => 'Start Date', 'readonly']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Currency:', 'currency')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-money-bill-alt"></i>
                            </span>
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('currency', $currencies, '', ['class' => 'form-control','placeholder' => 'Select Currency'])); !!}
                        </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                        {!! html()->label('Country:', 'country')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-globe"></i>
                            </span>
                            {!! html()->text('country', null)->attributes(['class' => 'form-control','placeholder' => 'Country']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                        {!! html()->label('State:', 'state')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! html()->text('state', null)->attributes(['class' => 'form-control','placeholder' => 'State']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                        {!! html()->label('City:', 'city')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! html()->text('city', null)->attributes(['class' => 'form-control','placeholder' => 'City']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Zip Code:', 'zip_code')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! html()->text('zip_code', null)->attributes(['class' => 'form-control','placeholder' => 'Zip/Postal Code']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Landmark:', 'landmark')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! html()->text('landmark', null)->attributes(['class' => 'form-control','placeholder' => 'Landmark']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Tax 1 Name:', 'tax_label_1')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! html()->text('tax_label_1', null)->attributes(['class' => 'form-control','placeholder' => 'GST / VAT / Other']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Tax 1 No.:', 'tax_number_1')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! html()->text('tax_number_1', null)->attributes(['class' => 'form-control',]); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Tax 2 Name:', 'tax_label_2')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! html()->text('tax_label_2', null)->attributes(['class' => 'form-control','placeholder' => 'GST / VAT / Other']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Tax 2 No.:', 'tax_number_2')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! html()->text('tax_number_2', null)->attributes(['class' => 'form-control',]); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                    </div>

                    <!-- Owner Information -->
                    <div class="col-md-4">
                        <div class="form-group">
                        {!! html()->label('Surname:', 'surname')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! html()->text('surname', null)->attributes(['class' => 'form-control','placeholder' => 'GST / VAT / Other']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                        {!! html()->label('First Name:', 'first_name')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! html()->text('first_name', null)->attributes(['class' => 'form-control','placeholder' => 'Owner Name']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                        {!! html()->label('Last Name:', 'last_name')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! html()->text('last_name', null)->attributes(['class' => 'form-control','placeholder' => 'Owner Name']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Username:', 'username')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! html()->text('username', null)->attributes(['class' => 'form-control','placeholder' => 'Username used for login']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Email:', 'email')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                            {!! html()->text('email', null)->attributes(['class' => 'form-control','placeholder' => '']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Password:', 'password')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! html()->password('password')->attributes(['class' => 'form-control','placeholder' => 'Login Password']); !!}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        {!! html()->label('Confirm Password:', 'confirm_password')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                            {!! html()->password('confirm_password')->attributes(['class' => 'form-control','placeholder' => 'Same as Login Password']); !!}
                        </div>
                        </div>
                    </div>

                </div>
                <!-- /.box-body -->
                
                <div class="box-footer">
                    <button type="button" class="btn btn-success pull-right">Register</button>
                </div>

            {!! html()->form()->close() !!}
            
        </div>
          <!-- /.box -->
    </div>

</div>


@endsection