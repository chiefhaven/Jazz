@extends('layouts.app')

@section('title', __( 'user.edit_user' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'user.edit_user' )</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ManageUserController::class, 'update'], [$user->id]), 'method' => 'PUT', 'id' => 'user_edit_form']))->open() !!}
    <div class="row">
        <div class="col-md-12">
        @component('components.widget', ['class' => 'box-primary'])
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
            <div class="col-md-4">
                <div class="form-group">
                  {!! html()->label(__( 'business.email' ) . ':*', 'email')->attributes([]) !!}
                    {!! html()->text('email', $user->email)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.email' ) ]); !!}
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                  <div class="checkbox">
                    <br>
                    <label>
                         {!! html()->checkbox('is_active', $is_checked_checkbox, $user->status)->attributes(['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
                    </label>
                    @show_tooltip(__('lang_v1.tooltip_enable_user_active'))
                  </div>
                </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <div class="checkbox">
                  <br/>
                  <label>
                       {!! html()->checkbox('is_enable_service_staff_pin', $user->is_enable_service_staff_pin, 1)->attributes(['class' => 'input-icheck status', 'id' => 'is_enable_service_staff_pin']); !!} {{ __('lang_v1.enable_service_staff_pin') }}
                  </label>
                  @show_tooltip(__('lang_v1.tooltip_is_enable_service_staff_pin'))
                </div>
              </div>
            </div>
            <div class="col-md-2 service_staff_pin_div {{ $user->is_enable_service_staff_pin == 1 ? '' : 'hide' }}">
              <div class="form-group">
                {!! html()->label(__( 'lang_v1.staff_pin' ) . ':', 'service_staff_pin')->attributes([]) !!}
                  {!! html()->password('service_staff_pin')->attributes(['class' => 'form-control','placeholder' => __( 'lang_v1.staff_pin' ) ]); !!}
              </div>
            </div>
        @endcomponent
        </div>
        <div class="col-md-12">
        @component('components.widget', ['title' => __('lang_v1.roles_and_permissions')])
            <div class="col-md-4">
                <div class="form-group">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('allow_login', !empty($user->allow_login), 1)->attributes([ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
                      </label>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="user_auth_fields @if(empty($user->allow_login)) hide @endif">
            @if(empty($user->allow_login))
                <div class="col-md-4">
                    <div class="form-group">
                      {!! html()->label(__( 'business.username' ) . ':', 'username')->attributes([]) !!}
                      @if(!empty($username_ext))
                        <div class="input-group">
                          {!! html()->text('username', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
                          <span class="input-group-addon">{{$username_ext}}</span>
                        </div>
                        <p class="help-block" id="show_username"></p>
                      @else
                          {!! html()->text('username', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
                      @endif
                      <p class="help-block">@lang('lang_v1.username_help')</p>
                    </div>
                </div>
            @endif
            <div class="col-md-4">
                <div class="form-group">
                  {!! html()->label("PIN" . ':', 'password')->attributes([]) !!}
                    {!! html()->password('password')->attributes(['class' => 'form-control', 'placeholder' => "PIN", 'required' => empty($user->allow_login) ? true : false ]); !!}
                    <p class="help-block">PIN</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                  {!! html()->label("Confirm PIN" . ':', 'confirm_password')->attributes([]) !!}
                    {!! html()->password('confirm_password')->attributes(['class' => 'form-control', 'placeholder' => "Confirm PIN", 'required' => empty($user->allow_login) ? true : false ]); !!}
                  
                </div>
            </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
                <div class="form-group">
                  {!! html()->label(__( 'user.role' ) . ':*', 'role')->attributes([]) !!} @show_tooltip(__('lang_v1.admin_role_location_permission_help'))
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('role', $roles, !empty($user->roles->first()->id) ? $user->roles->first()->id : null, ['class' => 'form-control select2', 'style' => 'width: 100%;'])); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-3">
                <h4>@lang( 'role.access_locations' ) @show_tooltip(__('tooltip.access_locations_permission'))</h4>
            </div>
            <div class="col-md-9">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label>
                          {!! html()->checkbox('access_all_locations', !is_array($permitted_locations) && $permitted_locations == 'all', 'access_all_locations')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.all_locations' ) }} 
                        </label>
                        @show_tooltip(__('tooltip.all_location_permission'))
                    </div>
                  </div>
              @foreach($locations as $location)
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('location_permissions[]', is_array($permitted_locations) && in_array($location->id, $permitted_locations), 'location.' . $location->id)->attributes([ 'class' => 'input-icheck']); !!} {{ $location->name }} @if(!empty($location->location_id))({{ $location->location_id}}) @endif
                      </label>
                    </div>
                </div>
              @endforeach
            </div>
        @endcomponent
        </div>

        <div class="col-md-12">
            @component('components.widget', ['title' => __('sale.sells')])

            <div class="col-md-4">
                <div class="form-group">
                  {!! html()->label(__( 'lang_v1.cmmsn_percent' ) . ':', 'cmmsn_percent')->attributes([]) !!} @show_tooltip(__('lang_v1.commsn_percent_help'))
                    {!! html()->text('cmmsn_percent', !empty($user->cmmsn_percent) ? @num_format($user->cmmsn_percent) : 0)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' )]); !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                  {!! html()->label(__( 'lang_v1.max_sales_discount_percent' ) . ':', 'max_sales_discount_percent')->attributes([]) !!} @show_tooltip(__('lang_v1.max_sales_discount_percent_help'))
                    {!! html()->text('max_sales_discount_percent', !is_null($user->max_sales_discount_percent) ? @num_format($user->max_sales_discount_percent) : null)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.max_sales_discount_percent' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br/>
                      <label>
                        {!! html()->checkbox('selected_contacts', $user->selected_contacts, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'selected_contacts']); !!} {{ __( 'lang_v1.allow_selected_contacts' ) }}
                      </label>
                      @show_tooltip(__('lang_v1.allow_selected_contacts_tooltip'))
                    </div>
                </div>
            </div>
            
            <div class="col-sm-4 selected_contacts_div @if(!$user->selected_contacts) hide @endif">
                <div class="form-group">
                  {!! html()->label(__('lang_v1.selected_contacts') . ':', 'user_allowed_contacts')->attributes([]) !!}
                    <div class="form-group">
                      {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('selected_contact_ids[]', $contact_access, array_keys($contact_access), ['class' => 'form-control select2', 'multiple', 'style' => 'width: 100%;', 'id' => 'user_allowed_contacts' ])); !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    @include('user.edit_profile_form_part', ['bank_details' => !empty($user->bank_details) ? json_decode($user->bank_details, true) : null])

    @if(!empty($form_partials))
      @foreach($form_partials as $partial)
        {!! $partial !!}
      @endforeach
    @endif
    <div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white" id="submit_user_button">@lang( 'messages.update' )</button>
        </div>
    </div>
    {!! html()->form()->close() !!}
  @stop
@section('javascript')
<script type="text/javascript">
  $(document).ready(function(){
    __page_leave_confirmation('#user_edit_form');
    
    $('#selected_contacts').on('ifChecked', function(event){
      $('div.selected_contacts_div').removeClass('hide');
    });
    $('#selected_contacts').on('ifUnchecked', function(event){
      $('div.selected_contacts_div').addClass('hide');
    });

    $('#is_enable_service_staff_pin').on('ifChecked', function(event){
      $('div.service_staff_pin_div').removeClass('hide');
    });

    $('#is_enable_service_staff_pin').on('ifUnchecked', function(event){
      $('div.service_staff_pin_div').addClass('hide');
      $('#service_staff_pin').val('');
    });

    $('#allow_login').on('ifChecked', function(event){
      $('div.user_auth_fields').removeClass('hide');
    });
    $('#allow_login').on('ifUnchecked', function(event){
      $('div.user_auth_fields').addClass('hide');
    });

    $('#user_allowed_contacts').select2({
        ajax: {
            url: '/contacts/customers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    all_contact: true
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
        },
        templateResult: function (data) { 
            var template = '';
            if (data.supplier_business_name) {
                template += data.supplier_business_name + "<br>";
            }
            template += data.text + "<br>" + LANG.mobile + ": " + data.mobile;

            return  template;
        },
        minimumInputLength: 1,
        escapeMarkup: function(markup) {
            return markup;
        },
    });
  });

  $('form#user_edit_form').validate({
                rules: {
                    first_name: {
                        required: true,
                    },
                    email: {
                        email: true,
                        remote: {
                            url: "/business/register/check-email",
                            type: "post",
                            data: {
                                email: function() {
                                    return $( "#email" ).val();
                                },
                                user_id: {{$user->id}}
                            }
                        }
                    },
                    password: {
                        minlength: 5
                    },
                    confirm_password: {
                        equalTo: "#password",
                    },
                    username: {
                        minlength: 5,
                        remote: {
                            url: "/business/register/check-username",
                            type: "post",
                            data: {
                                username: function() {
                                    return $( "#username" ).val();
                                },
                                @if(!empty($username_ext))
                                  username_ext: "{{$username_ext}}"
                                @endif
                            }
                        }
                    }
                },
                messages: {
                    password: {
                        minlength: 'Password should be minimum 5 characters',
                    },
                    confirm_password: {
                        equalTo: 'Should be same as password'
                    },
                    username: {
                        remote: 'Invalid username or User already exist'
                    },
                    email: {
                        remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
                    }
                }
            });
</script>
@endsection