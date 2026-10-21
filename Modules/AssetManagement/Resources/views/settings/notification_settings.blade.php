<div class="pos-tab-content">
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<h4>@lang('assetmanagement::lang.asset_send_for_maintenance_notification'):</h4>
					<p class="help-block">@lang('lang_v1.available_tags'): {asset_code}, {created_by}, {maintenance_id}, {status}, {priority}, {maintenance_note}</p>
				</div>
				<div class="col-md-12">
	                <div class="form-group">
	                    {!! html()->label(__('assetmanagement::lang.recipients') . ':', 'send_for_maintenence_recipients')->attributes([])!!}
	                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('send_for_maintenence_recipients[]', $users, !empty($asset_settings['send_for_maintenence_recipients'])? $asset_settings['send_for_maintenence_recipients'] : null, ['class' => 'form-control select2', 'multiple', 'style' => 'width:100%'])); !!}
	                </div>
	            </div>
				<div class="col-md-12">
					<div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="enable_asset_send_for_maintenance_email" id="enable_asset_send_for_maintenance_email" class="input-icheck" value="1" @if(!empty($asset_settings['enable_asset_send_for_maintenance_email'])) checked @endif>
                                @lang('assetmanagement::lang.enable_email')
                            </label>
                        </div>
                    </div>
				</div>
			</div>
			<div class="row @if(empty($asset_settings['enable_asset_send_for_maintenance_email'])) hide @endif" id="asset_send_for_maintenance_email_div">
				<div class="col-md-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.email_subject').':', 'send_for_maintenance_subject')->attributes([]) !!}
                        {!! html()->text('send_for_maintenance[subject]', $send_for_maintenance_template['subject'])->attributes(['class' => 'form-control'
                        , 'placeholder' => __('lang_v1.email_subject'), 'id' =>'send_for_maintenance_subject']); !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.email_body').':', 'send_for_maintenance_email_body')->attributes([]) !!}
                        {!! html()->textarea('send_for_maintenance[email_body]', $send_for_maintenance_template['email_body'])->attributes(['class' => 'form-control ckeditor'
                        , 'placeholder' => __('lang_v1.email_body'), 'id' => 'send_for_maintenance_email_body', 'rows' => 6]); !!}
                    </div>
                </div>
			</div>
		</div>
	</div>

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<h4>@lang('assetmanagement::lang.asset_assigned_for_maintenance_notification'):</h4>
					<p class="help-block">@lang('lang_v1.available_tags'): {asset_code}, {created_by}, {maintenance_id}, {status}, {priority}, {send_for_maintenance_details}</p>
				</div>
				<div class="col-md-12">
					<div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="enable_asset_assigned_for_maintenance_email" id="enable_asset_assigned_for_maintenance_email" class="input-icheck" value="1" @if(!empty($asset_settings['enable_asset_assigned_for_maintenance_email'])) checked @endif>
                                @lang('assetmanagement::lang.enable_email')
                            </label>
                        </div>
                    </div>
				</div>
			</div>
			<div class="row @if(empty($asset_settings['enable_asset_assigned_for_maintenance_email'])) hide @endif" id="asset_assigned_for_maintenance_email_div">
				<div class="col-md-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.email_subject').':', 'assigned_for_maintenance_subject')->attributes([]) !!}
                        {!! html()->text('assigned_for_maintenance[subject]', $assigned_for_maintenance_template['subject'])->attributes(['class' => 'form-control'
                        , 'placeholder' => __('lang_v1.email_subject'), 'id' =>'assigned_for_maintenance_subject']); !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.email_body').':', 'assigned_for_maintenance_email_body')->attributes([]) !!}
                        {!! html()->textarea('assigned_for_maintenance[email_body]', $assigned_for_maintenance_template['email_body'])->attributes(['class' => 'form-control ckeditor'
                        , 'placeholder' => __('lang_v1.email_body'), 'id' => 'assigned_for_maintenance_email_body', 'rows' => 6]); !!}
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>