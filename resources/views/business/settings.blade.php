@extends('layouts.app')
@section('title', __('business.business_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('business.business_settings')</h1>
    <br>
    @include('layouts.partials.search_settings')
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\BusinessController::class, 'postBusinessSettings']), 'method' => 'post', 'id' => 'bussiness_edit_form',
           'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
        @component('components.widget', ['class' =>  'pos-tab-container'])
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu tw-rounded-lg">
                <div class="list-group">
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base  active">@lang('business.business')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.tax') @show_tooltip(__('tooltip.business_tax'))</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.product')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('contact.contact')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.sale')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('sale.pos_sale')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.display_screen')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('purchase.purchases')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.payment')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.dashboard')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.system')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.prefixes')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.email_settings')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.sms_settings')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.reward_point_settings')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.mra_eis')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.modules')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.custom_labels')</a>
                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                <!-- tab 1 start -->
                @include('business.partials.settings_business')
                <!-- tab 1 end -->
                <!-- tab 2 start -->
                @include('business.partials.settings_tax')
                <!-- tab 2 end -->
                <!-- tab 3 start -->
                @include('business.partials.settings_product')
                @include('business.partials.settings_contact')
                <!-- tab 3 end -->
                <!-- tab 4 start -->
                @include('business.partials.settings_sales')
                @include('business.partials.settings_pos')
                @include('business.partials.settings_display_pos')
                <!-- tab 4 end -->
                <!-- tab 5 start -->
                @include('business.partials.settings_purchase')
                @include('business.partials.settings_payment')
                <!-- tab 5 end -->
                <!-- tab 6 start -->
                @include('business.partials.settings_dashboard')
                <!-- tab 6 end -->
                <!-- tab 7 start -->
                @include('business.partials.settings_system')
                <!-- tab 7 end -->
                <!-- tab 8 start -->
                @include('business.partials.settings_prefixes')
                <!-- tab 8 end -->
                <!-- tab 9 start -->
                @include('business.partials.settings_email')
                <!-- tab 9 end -->
                <!-- tab 10 start -->
                @include('business.partials.settings_sms')
                <!-- tab 10 end -->
                <!-- tab 11 start -->
                @include('business.partials.settings_reward_point')
                <!-- tab 11 end -->
                <!-- tab 12 start -->
                <!-- MRA EIS Settings Tab -->
                @include('business.partials.settings_mra_eis')
                <!-- tab 12 end -->
                <!-- tab 13 start -->
                @include('business.partials.settings_modules')
                <!-- tab 13 end -->
                @include('business.partials.settings_custom_labels')
            </div>
        @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-center">
            <button class="tw-dw-btn tw-dw-btn-error tw-dw-btn-lg tw-text-white" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->
@stop

@section('javascript')
<script type="text/javascript">
    __page_leave_confirmation('#bussiness_edit_form');
    
    $(document).on('ifToggled', '#use_superadmin_settings', function() {
        if ($('#use_superadmin_settings').is(':checked')) {
            $('#toggle_visibility').addClass('hide');
            $('.test_email_btn').addClass('hide');
        } else {
            $('#toggle_visibility').removeClass('hide');
            $('.test_email_btn').removeClass('hide');
        }
    });

    $(document).ready(function(){
    
        $('#test_email_btn').click( function() {
            var data = {
                mail_driver: $('#mail_driver').val(),
                mail_host: $('#mail_host').val(),
                mail_port: $('#mail_port').val(),
                mail_username: $('#mail_username').val(),
                mail_password: $('#mail_password').val(),
                mail_encryption: $('#mail_encryption').val(),
                mail_from_address: $('#mail_from_address').val(),
                mail_from_name: $('#mail_from_name').val(),
            };
            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testEmailConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });
        });

        var eisBusy = false;
        var eisUrls = {
            index: @json(route('eis.terminals.index')),
            activate: @json(route('eis.terminals.activate')),
            'latest-config': @json(route('eis.terminals.latest-config', ['terminalId' => 0])),
            deactivate: @json(route('eis.terminals.deactivate', ['terminalId' => 0])),
            'renew-tokens': @json(route('eis.terminals.renew-tokens', ['terminalId' => 0])),
            'default': @json(route('eis.terminals.default', ['terminalId' => 0]))
        };

        function eisMessage(message, error) {
            $('#eis-management-message')
                .attr('class', 'alert ' + (error ? 'alert-danger' : 'alert-success'))
                .text(message);
        }

        function eisError(xhr) {
            var result = xhr.responseJSON || {};
            var errors = result.errors || {};
            var field = Object.keys(errors)[0];
            eisMessage(field ? errors[field][0] : (result.msg || result.message || 'Unable to complete terminal action.'), true);
        }

        function eisControls() {
            $('#eis-terminal-management button').prop('disabled', eisBusy);
            $('#eis-activation-location').prop('disabled', eisBusy || !$('#eis-activation-location option[value!=""]').length);
            $('#eis-activate-terminal').prop('disabled', eisBusy || !$('#eis-activation-location').val());
            $('#eis-terminal-rows button[data-is-default="true"]').prop('disabled', true);
        }

        function loadEisTerminals() {
            eisBusy = true;
            eisControls();
            return $.ajax({
                url: eisUrls.index,
                dataType: 'json'
            }).done(function(result) {
                if (!result.success) {
                    eisMessage(result.msg || result.message || 'Unable to load terminals.', true);
                    return;
                }
                var selected = $('#eis-activation-location').val();
                var locations = $('#eis-activation-location').empty();
                $('<option>').val('').text('Select a location').appendTo(locations);
                $.each(result.data.locations, function(_, location) {
                    $('<option>').val(location.id).text(location.name).appendTo(locations);
                });
                if (selected) locations.val(selected);
                var rows = $('#eis-terminal-rows').empty();
                $.each(result.data.terminals, function(_, terminal) {
                    var row = $('<tr>').appendTo(rows);
                    $('<td>').text((terminal.terminal_label || terminal.device_id || 'Terminal') + ' (#' + terminal.id + ')').appendTo(row);
                    $('<td>').text(terminal.location_name).appendTo(row);
                    $('<td>').text(terminal.is_default ? 'Default' : '—').appendTo(row);
                    $('<td>').text(terminal.is_active ? 'Active' : 'Inactive').appendTo(row);
                    var actions = $('<td>').appendTo(row);
                    $.each({'latest-config': 'Latest config', 'deactivate': 'Deactivate', 'renew-tokens': 'Renew tokens', 'default': 'Set default'}, function(action, label) {
                        if ((action === 'deactivate' || action === 'default') && !terminal.is_active) return;
                        $('<button>', {type: 'button', 'class': 'btn btn-default btn-xs eis-terminal-action'})
                            .attr('data-terminal-id', terminal.id)
                            .attr('data-action', action)
                            .attr('data-is-default', action === 'default' && terminal.is_default ? 'true' : 'false')
                            .text(label).appendTo(actions);
                        actions.append(' ');
                    });
                });
                if (!result.data.terminals.length) {
                    $('<tr>').append($('<td>', {colspan: 5}).text('No terminals in your permitted locations.')).appendTo(rows);
                }
            }).fail(function(xhr) {
                $('#eis-terminal-rows').empty().append($('<tr>').append($('<td>', {colspan: 5}).text('Unable to load terminals. Use Refresh to try again.')));
                eisError(xhr);
            }).always(function() {
                eisBusy = false;
                eisControls();
            });
        }

        function eisAction(url, data, activated) {
            var reload = false;
            eisBusy = true;
            eisControls();
            $.ajax({
                method: 'post',
                url: url,
                data: data,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).done(function(result) {
                eisMessage(result.msg || result.message || 'Terminal updated.', !result.success);
                if (result.success) {
                    if (activated) $('#eis-activation-code').val('');
                    reload = true;
                }
            }).fail(eisError).always(function() {
                if (reload) {
                    loadEisTerminals();
                } else {
                    eisBusy = false;
                    eisControls();
                }
            });
        }

        $('#eis-activation-location').change(eisControls);
        $('#eis-refresh-terminals').click(function() {
            if (!eisBusy) loadEisTerminals();
        });
        $('#eis-activate-terminal').click(function() {
            if (eisBusy) return;
            var location = $('#eis-activation-location').val();
            var code = $('#eis-activation-code').val().trim();
            if (!location || !code) {
                eisMessage('Select a location and enter an activation code.', true);
                return;
            }
            eisAction(eisUrls.activate, {
                location_id: location,
                terminal_activation_code: code,
                environment: {
                    platform: {osName: navigator.platform || 'Web', osVersion: 'Unknown', osBuild: ''},
                    pos: {productID: @json(config('app.name')), productVersion: @json(config('app.version', '1.0.0'))}
                }
            }, true);
        });
        $('#eis-terminal-rows').on('click', '.eis-terminal-action', function() {
            if (eisBusy) return;
            var action = $(this).attr('data-action');
            var terminalId = $(this).attr('data-terminal-id');
            var data = {};
            if (action === 'deactivate') {
                var reason = window.prompt('Deactivate terminal #' + terminalId + '? Reason (optional):', '');
                if (reason === null) return;
                data.reason = reason;
            } else if (action === 'renew-tokens' && !window.confirm('Renew tokens for terminal #' + terminalId + '? Existing credentials will become invalid.')) {
                return;
            } else if (action === 'default' && !window.confirm('Use terminal #' + terminalId + ' as the default for its location?')) {
                return;
            }
            eisAction(eisUrls[action].replace('/0/', '/' + terminalId + '/'), data, false);
        });
        loadEisTerminals();

        // SMS Test
        $('#test_sms_btn').click( function() {
            var test_number = $('#test_number').val();
            if (test_number.trim() == '') {
                toastr.error('{{__("lang_v1.test_number_is_required")}}');
                $('#test_number').focus();
                return false;
            }

            var data = {
                url: $('#sms_settings_url').val(),
                send_to_param_name: $('#send_to_param_name').val(),
                msg_param_name: $('#msg_param_name').val(),
                request_method: $('#request_method').val(),
                param_1: $('#sms_settings_param_key1').val(),
                param_2: $('#sms_settings_param_key2').val(),
                param_3: $('#sms_settings_param_key3').val(),
                param_4: $('#sms_settings_param_key4').val(),
                param_5: $('#sms_settings_param_key5').val(),
                param_6: $('#sms_settings_param_key6').val(),
                param_7: $('#sms_settings_param_key7').val(),
                param_8: $('#sms_settings_param_key8').val(),
                param_9: $('#sms_settings_param_key9').val(),
                param_10: $('#sms_settings_param_key10').val(),
                param_val_1: $('#sms_settings_param_val1').val(),
                param_val_2: $('#sms_settings_param_val2').val(),
                param_val_3: $('#sms_settings_param_val3').val(),
                param_val_4: $('#sms_settings_param_val4').val(),
                param_val_5: $('#sms_settings_param_val5').val(),
                param_val_6: $('#sms_settings_param_val6').val(),
                param_val_7: $('#sms_settings_param_val7').val(),
                param_val_8: $('#sms_settings_param_val8').val(),
                param_val_9: $('#sms_settings_param_val9').val(),
                param_val_10: $('#sms_settings_param_val10').val(),
                test_number: test_number,
                header_1: $('#sms_settings_header_key1').val(),
                header_val_1: $('#sms_settings_header_val1').val(),
                header_2: $('#sms_settings_header_key2').val(),
                header_val_2: $('#sms_settings_header_val2').val(),
                header_3: $('#sms_settings_header_key3').val(),
                header_val_3: $('#sms_settings_header_val3').val(),
                data_parameter_type: $('#data_parameter_type').val(),
            };

            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testSmsConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });
        });

        $('select.custom_labels_products').change(function(){
            value = $(this).val();
            textarea = $(this).parents('div.custom_label_product_div').find('div.custom_label_product_dropdown');
            if(value == 'dropdown'){
                textarea.removeClass('hide');
            } else{
                textarea.addClass('hide');
            }
        });

        tinymce.init({
            selector: 'textarea#display_screen_heading',
            height: 250
        });

        $('.carousel_image').fileinput({
            showUpload: true,
            showPreview: true,
            browseLabel: LANG.file_browse_label,
            removeLabel: LANG.remove,
        });
    });
</script>
@endsection