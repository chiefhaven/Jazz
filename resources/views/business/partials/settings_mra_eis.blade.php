<div class="pos-tab-content">
    <div class="row">
        <div id="toggle_visibility" @if(!empty($eis_settings['use_superadmin_settings'])) class="hide" @endif>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('terminal_activation_code', __('lang_v1.terminal_activation_code') . ':') !!}
                    {!! Form::text('eis_settings[terminal_activation_code]', $eis_settings['terminal_activation_code'] ?? '', ['class' => 'form-control','placeholder' => __('lang_v1.terminal_activation_code'), 'id' => 'terminal_activation_code_input']); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-12 activate_eis_terminal_btn @if(!empty($eis_settings['use_superadmin_settings'])) hide @endif">
                <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white pull-right" id="activate_eis_terminal_btn">
                    @lang('lang_v1.activate_eis_terminal')
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" id="business_id" value="{{ $businessId ?? auth()->user()->business_id ?? 1 }}">
    <input type="hidden" id="eis_token" value="{{ $eisToken ?? session('eis_token') }}">

    <!-- Activation Code Display -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>Activation Code:</strong>
                <span id="activation_code_display">
                    {{ $eis_settings['terminal_activation_code'] ?? 'Not yet activated' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Terminal Status & Details -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Terminal Status</h4>
                </div>
                <div class="card-body">
                    <!-- Status Display -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <span id="terminal_status_display">
                                <span class="badge badge-secondary">Checking...</span>
                            </span>
                        </div>
                        <div class="col-md-6 text-right">
                            <button id="refresh_status_btn" class="btn btn-sm btn-info">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Active Actions -->
                    <div id="terminal_active_actions" class="row mb-3" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> Terminal is active
                            </div>
                            <button id="deactivate_eis_terminal_btn" class="btn btn-danger">
                                <i class="fa fa-stop"></i> Deactivate Terminal
                            </button>
                            <button id="toggle_eis_terminal_btn" class="btn btn-warning">
                                <i class="fa fa-exchange"></i> Toggle Status
                            </button>
                        </div>
                    </div>

                    <!-- Inactive Actions -->
                    <div id="terminal_inactive_actions" class="row mb-3" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> Terminal is inactive
                            </div>
                            <div class="form-group">
                                <label for="deactivation_reason">Deactivation Reason</label>
                                <input type="text" id="deactivation_reason" 
                                       class="form-control" 
                                       placeholder="Reason for deactivation (optional)">
                            </div>
                        </div>
                    </div>

                    <!-- Terminal Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Terminal Details</h5>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th width="40%">Terminal Label</th>
                                    <td id="terminal_label_display">N/A</td>
                                </tr>
                                <tr>
                                    <th>Terminal ID</th>
                                    <td id="terminal_id_display">N/A</td>
                                </tr>
                                <tr>
                                    <th>Trading Name</th>
                                    <td id="trading_name_display">N/A</td>
                                </tr>
                                <tr>
                                    <th>Email Address</th>
                                    <td id="email_address_display">N/A</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td id="phone_number_display">N/A</td>
                                </tr>
                                <tr>
                                    <th>Activation Date</th>
                                    <td id="activation_date_display">N/A</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Terminal Credentials</h5>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th width="40%">JWT Token</th>
                                    <td id="jwt_token_display">
                                        <span class="text-muted">N/A</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Secret Key</th>
                                    <td id="secret_key_display">
                                        <span class="text-muted">N/A</span>
                                    </td>
                                </tr>
                            </table>
                            <button id="regenerate_credentials_btn" class="btn btn-sm btn-warning mt-2">
                                <i class="fa fa-key"></i> Regenerate Credentials
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>