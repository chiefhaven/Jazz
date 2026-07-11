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

        // EIS Terminal Activation - Using the button from settings_mra_eis
        // Terminal Activation
        $('#activate_eis_terminal_btn').click(function() {
            var activationCode = $('#terminal_activation_code_input').val();
            
            if (!activationCode) {
                swal({
                    text: 'Please enter terminal activation code',
                    icon: 'warning'
                });
                return;
            }

            // Build the correct payload structure
            var data = {
                terminal_activation_code: activationCode,
                business_id: $('#business_id').val(),
                token: $('#eis_token').val(),
                environment: {
                    platform: {
                        osName: getOSName(),
                        osVersion: getOSVersion(),
                        osBuild: '',
                        macAddress: getMacAddress()
                    },
                    pos: {
                        productID: '{{ config('app.name') }}',
                        productVersion: '{{ config('app.version') }}'
                    }
                }
            };

            // Show loading
            var $btn = $(this);
            $btn.prop('disabled', true);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Activating...');

            $.ajax({
                method: 'post',
                data: data,
                url: "{{ route('eis.terminal.activate') }}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.message || 'Terminal activated successfully!',
                            icon: 'success'
                        });
                        
                        if (result.data) {
                            updateTerminalUI(result.data);
                        }
                        
                        // Display activation code if returned
                        if (result.activation_code) {
                            $('#activation_code_display').text(result.activation_code);
                        }
                        
                        // Display credentials if returned
                        if (result.terminal_credentials) {
                            $('#jwt_token_display').text(result.terminal_credentials.jwtToken || 'N/A');
                            $('#secret_key_display').text(result.terminal_credentials.secretKey || 'N/A');
                        }
                    } else {
                        console.error('Activation failed:', result);
                        swal({
                            text: result || 'Failed to activate terminal',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    swal({
                        text: errorMsg,
                        icon: 'error'
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $btn.html('@lang('lang_v1.activate_eis_terminal')');
                }
            });
        });

        // Utility functions for environment detection
        function getOSName() {
            var userAgent = navigator.userAgent;
            if (userAgent.indexOf('Windows') !== -1) return 'Windows';
            if (userAgent.indexOf('Mac') !== -1) return 'MacOS';
            if (userAgent.indexOf('Linux') !== -1) return 'Linux';
            if (userAgent.indexOf('Android') !== -1) return 'Android';
            if (userAgent.indexOf('iOS') !== -1) return 'iOS';
            return 'Unknown';
        }

        function getOSVersion() {
            var userAgent = navigator.userAgent;
            var match;
            if ((match = userAgent.match(/Windows NT (\d+\.\d+)/))) return match[1];
            if ((match = userAgent.match(/Mac OS X (\d+[._]\d+[._]\d+)/))) return match[1].replace(/_/g, '.');
            if ((match = userAgent.match(/Android (\d+\.\d+)/))) return match[1];
            return 'Unknown';
        }

        function getMacAddress() {
            // For web, we generate a unique identifier
            return 'web-' + navigator.platform + '-' + navigator.userAgent.length;
        }

        // Deactivate Terminal
        $(document).on('click', '#deactivate_eis_terminal_btn', function() {
            var data = {
                business_id: $('#business_id').val(),
                reason: $('#deactivation_reason').val()
            };

            swal({
                title: 'Are you sure?',
                text: 'Do you want to deactivate this terminal?',
                icon: 'warning',
                buttons: true,
                dangerMode: true
            }).then((willDeactivate) => {
                if (willDeactivate) {
                    $.ajax({
                        method: 'post',
                        data: data,
                        url: "{{ route('eis.terminal.deactivate') }}",
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(result) {
                            if (result.success == true) {
                                swal({
                                    text: result.msg || 'Terminal deactivated successfully',
                                    icon: 'success'
                                });
                                updateTerminalUI(result.data);
                            } else {
                                swal({
                                    text: result.msg || 'Failed to deactivate terminal',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            swal({
                                text: 'An error occurred',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

        // Toggle Terminal Status
        $(document).on('click', '#toggle_eis_terminal_btn', function() {
            var data = {
                business_id: $('#business_id').val(),
                token: $('#eis_token').val()
            };

            $.ajax({
                method: 'post',
                data: data,
                url: "{{ route('eis.terminal.toggle') }}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg || 'Terminal status toggled successfully',
                            icon: 'success'
                        });
                        updateTerminalUI(result.data);
                    } else {
                        swal({
                            text: result.msg || 'Failed to toggle terminal',
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    swal({
                        text: 'An error occurred',
                        icon: 'error'
                    });
                }
            });
        });

        // Regenerate Credentials
        $(document).on('click', '#regenerate_credentials_btn', function() {
            var data = {
                business_id: $('#business_id').val(),
                token: $('#eis_token').val()
            };

            swal({
                title: 'Are you sure?',
                text: 'This will regenerate terminal credentials. Old credentials will be invalid.',
                icon: 'warning',
                buttons: true,
                dangerMode: true
            }).then((willRegenerate) => {
                if (willRegenerate) {
                    $.ajax({
                        method: 'post',
                        data: data,
                        url: "{{ route('eis.terminal.regenerate-credentials') }}",
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(result) {
                            if (result.success == true) {
                                swal({
                                    text: result.msg || 'Credentials regenerated successfully',
                                    icon: 'success'
                                });
                                if (result.data) {
                                    $('#jwt_token_display').text(result.data.jwt_token || 'N/A');
                                    $('#secret_key_display').text(result.data.secret_key || 'N/A');
                                }
                            } else {
                                swal({
                                    text: result.msg || 'Failed to regenerate credentials',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            swal({
                                text: 'An error occurred',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

        // Get Terminal Status
        function getTerminalStatus() {
            var businessId = $('#business_id').val();
            
            $.ajax({
                method: 'get',
                url: "{{ url('api/v1/terminal/status') }}/" + businessId,
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        updateTerminalUI(result.data);
                    }
                },
                error: function() {
                    $('#terminal_status_display').html('<span class="badge badge-danger">Error loading status</span>');
                }
            });
        }

        // Update Terminal UI
        function updateTerminalUI(data) {
            if (data) {
                // Update status
                if (data.is_active) {
                    $('#terminal_status_display').html('<span class="badge badge-success">Active</span>');
                    $('#terminal_active_actions').show();
                    $('#terminal_inactive_actions').hide();
                    $('#activation_code_section').hide();
                } else {
                    $('#terminal_status_display').html('<span class="badge badge-danger">Inactive</span>');
                    $('#terminal_active_actions').hide();
                    $('#terminal_inactive_actions').show();
                    $('#activation_code_section').show();
                }
                
                // Update details
                $('#terminal_label_display').text(data.terminal_label || 'N/A');
                $('#terminal_id_display').text(data.terminal_id || 'N/A');
                $('#trading_name_display').text(data.trading_name || 'N/A');
                $('#email_address_display').text(data.email_address || 'N/A');
                $('#phone_number_display').text(data.phone_number || 'N/A');
                $('#activation_date_display').text(data.activation_date ? new Date(data.activation_date).toLocaleString() : 'N/A');
                
                // Update credentials
                if (data.terminal_credentials) {
                    $('#jwt_token_display').text(data.terminal_credentials.jwt_token || 'N/A');
                    $('#secret_key_display').text(data.terminal_credentials.secret_key || 'N/A');
                }
            }
        }

        // Utility functions for environment detection
        function getOSName() {
            var userAgent = navigator.userAgent;
            if (userAgent.indexOf('Windows') !== -1) return 'Windows';
            if (userAgent.indexOf('Mac') !== -1) return 'MacOS';
            if (userAgent.indexOf('Linux') !== -1) return 'Linux';
            if (userAgent.indexOf('Android') !== -1) return 'Android';
            if (userAgent.indexOf('iOS') !== -1) return 'iOS';
            return 'Unknown';
        }

        function getOSVersion() {
            var userAgent = navigator.userAgent;
            var match;
            if ((match = userAgent.match(/Windows NT (\d+\.\d+)/))) return match[1];
            if ((match = userAgent.match(/Mac OS X (\d+[._]\d+[._]\d+)/))) return match[1].replace(/_/g, '.');
            if ((match = userAgent.match(/Android (\d+\.\d+)/))) return match[1];
            return 'Unknown';
        }

        function getBrowser() {
            var userAgent = navigator.userAgent;
            if (userAgent.indexOf('Chrome') !== -1 && userAgent.indexOf('Edge') === -1) return 'Chrome';
            if (userAgent.indexOf('Firefox') !== -1) return 'Firefox';
            if (userAgent.indexOf('Safari') !== -1 && userAgent.indexOf('Chrome') === -1) return 'Safari';
            if (userAgent.indexOf('Edge') !== -1) return 'Edge';
            return 'Unknown';
        }

        function getBrowserVersion() {
            var userAgent = navigator.userAgent;
            var match;
            if ((match = userAgent.match(/(Chrome|Firefox|Safari|Edge|Opera)\/(\d+\.\d+)/))) return match[2];
            return 'Unknown';
        }

        function getMacAddress() {
            return 'web-' + navigator.platform + '-' + navigator.userAgent.length;
        }

        // Load terminal status on page load
        setTimeout(getTerminalStatus, 500);

        // Refresh status every 60 seconds
        setInterval(getTerminalStatus, 60000);

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