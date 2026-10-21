<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

  @php

    if(isset($update_action)) {
        $url = $update_action;
        $customer_groups = [];
        $opening_balance = 0;
        $lead_users = $contact->leadUsers->pluck('id');
    } else {
      $url = action([\App\Http\Controllers\ContactController::class, 'update'], [$contact->id]);
      $sources = [];
      $life_stages = [];
      $lead_users = [];
      $assigned_to_users = $contact->userHavingAccess->pluck('id');
    }
  @endphp

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => $url, 'method' => 'PUT', 'id' => 'contact_edit_form']))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.edit_contact')</h4>
    </div>

    <div class="modal-body">

      <div class="row">

        <div class="col-md-4">
          <div class="form-group">
              {!! html()->label(__('contact.contact_type') . ':*', 'type')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                  </span>
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('type', $types, $contact->type, ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required'])); !!}
              </div>
          </div>
        </div>
        <div class="col-md-4 mt-15">
            <label class="radio-inline">
                <input type="radio" name="contact_type_radio" @if($contact->contact_type == 'individual') checked @endif id="inlineRadio1" value="individual">
                @lang('lang_v1.individual')
            </label>
            <label class="radio-inline">
                <input type="radio" name="contact_type_radio" @if($contact->contact_type == 'business') checked @endif id="inlineRadio2" value="business">
                @lang('business.business')
            </label>
        </div>
        <div class="col-md-4">
          <div class="form-group">
              {!! html()->label(__('lang_v1.contact_id') . ':', 'contact_id')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-id-badge"></i>
                  </span>
                  <input type="hidden" id="hidden_id" value="{{$contact->id}}">
                  {!! html()->text('contact_id', $contact->contact_id)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.contact_id')]); !!}
              </div>
              <p class="help-block">
                @lang('lang_v1.leave_empty_to_autogenerate')
            </p>
          </div>
        </div>
        <div class="col-md-4 customer_fields">
          <div class="form-group">
              {!! html()->label(__('lang_v1.customer_group') . ':', 'customer_group_id')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-users"></i>
                  </span>
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('customer_group_id', $customer_groups, $contact->customer_group_id, ['class' => 'form-control'])); !!}
              </div>
          </div>
        </div>
        <div class="clearfix customer_fields"></div>
        <div class="col-md-4 business" @if($contact->contact_type == 'individual' || empty($contact->contact_type)) style="display: none;"  @endif>
          <div class="form-group">
              {!! html()->label(__('business.business_name') . ':', 'supplier_business_name')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-briefcase"></i>
                  </span>
                  {!! html()->text('supplier_business_name', $contact->supplier_business_name)->attributes(['class' => 'form-control', 'placeholder' => __('business.business_name')]); !!}
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3 individual"  @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! html()->label(__( 'business.prefix' ) . ':', 'prefix')->attributes([]) !!}
                    {!! html()->text('prefix', $contact->prefix)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! html()->label(__( 'business.first_name' ) . ':*', 'first_name')->attributes([]) !!}
                    {!! html()->text('first_name', $contact->first_name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! html()->label(__( 'lang_v1.middle_name' ) . ':', 'middle_name')->attributes([]) !!}
                    {!! html()->text('middle_name', $contact->middle_name)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.middle_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! html()->label(__( 'business.last_name' ) . ':', 'last_name')->attributes([]) !!}
                    {!! html()->text('last_name', $contact->last_name)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>

      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('contact.mobile') . ':*', 'mobile')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-mobile"></i>
                </span>
                {!! html()->text('mobile', $contact->mobile)->attributes(['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('contact.alternate_contact_number') . ':', 'alternate_number')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! html()->text('alternate_number', $contact->alternate_number)->attributes(['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('contact.landline') . ':', 'landline')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! html()->text('landline', $contact->landline)->attributes(['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
            <div class="form-group">
                {!! html()->label(__('business.email') . ':', 'email')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-envelope"></i>
                    </span>
                    {!! html()->email('email', $contact->email)->attributes(['class' => 'form-control','placeholder' => __('business.email')]); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group individual" @if($contact->contact_type == 'business') style="display: none;"  @endif>
                {!! html()->label(__('lang_v1.dob') . ':', 'dob')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    
                    {!! html()->text('dob', !empty($contact->dob) ? @format_date($contact->dob) : null)->attributes(['class' => 'form-control dob-date-picker','placeholder' => __('lang_v1.dob'), 'readonly']); !!}
                </div>
            </div>
        </div>
        
        <!-- lead additional field -->
        <div class="col-md-4 lead_additional_div">
          <div class="form-group">
              {!! html()->label(__('lang_v1.source') . ':', 'crm_source')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fas fa fa-search"></i>
                  </span>
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('crm_source', $sources, $contact->crm_source, ['class' => 'form-control', 'id' => 'crm_source','placeholder' => __('messages.please_select')])); !!}
              </div>
          </div>
        </div>
        <div class="col-md-4 lead_additional_div">
          <div class="form-group">
              {!! html()->label(__('lang_v1.life_stage') . ':', 'crm_life_stage')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fas fa fa-life-ring"></i>
                  </span>
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('crm_life_stage', $life_stages, $contact->crm_life_stage, ['class' => 'form-control', 'id' => 'crm_life_stage','placeholder' => __('messages.please_select')])); !!}
              </div>
          </div>
        </div>
        <div class="col-md-6 lead_additional_div">
          <div class="form-group">
              {!! html()->label(__('lang_v1.assigned_to') . ':*', 'user_id')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                  </span>
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('user_id[]', $users, $lead_users, ['class' => 'form-control select2', 'id' => 'user_id', 'multiple', 'required', 'style' => 'width: 100%;'])); !!}
              </div>
          </div>
        </div>

        @if(config('constants.enable_contact_assign') && $contact->type !== 'lead')
          <!-- User in create customer & supplier -->
          <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.assigned_to') . ':', 'assigned_to_users')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('assigned_to_users[]', $users, $assigned_to_users ?? [], ['class' => 'form-control select2', 'id' => 'assigned_to_users', 'multiple', 'style' => 'width: 100%;'])); !!}
                    </div>
                </div>
          </div>
        @endif

        <div class="col-md-12">
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white center-block more_btn" data-target="#more_div">@lang('lang_v1.more_info') <i class="fa fa-chevron-down"></i></button>
        </div>
        
        <div id="more_div" class="hide">

            <div class="col-md-12"><hr/></div>
        
        <div class="col-md-4">
          <div class="form-group">
              {!! html()->label(__('contact.tax_no') . ':', 'tax_number')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-info"></i>
                  </span>
                  {!! html()->text('tax_number', $contact->tax_number)->attributes(['class' => 'form-control', 'placeholder' => __('contact.tax_no')]); !!}
              </div>
          </div>
        </div>

        
        <div class="col-md-4">
          <div class="form-group">
              {!! html()->label(__('contact.eis_authorization_code') . ':', 'authorization_code')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-key"></i>
                  </span>
                  {!! html()->text('authorization_code', $contact->authorization_code)->attributes(['class' => 'form-control', 'placeholder' => __('contact.eis_authorization_code'), 'maxlength' => 255]); !!}
              </div>
          </div>
        </div>

        <div class="col-md-4 opening_balance">
          <div class="form-group">
              {!! html()->label(__('lang_v1.opening_balance') . ':', 'opening_balance')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fas fa-money-bill-alt"></i>
                  </span>
                  {!! html()->text('opening_balance', $opening_balance)->attributes(['class' => 'form-control input_number']); !!}
              </div>
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-md-4 pay_term">
          <div class="form-group">
            <div class="multi-input">
              {!! html()->label(__('contact.pay_term') . ':', 'pay_term_number')->attributes([]) !!} @show_tooltip(__('tooltip.pay_term'))
              <br/>
              {!! html()->input('number', 'pay_term_number', $contact->pay_term_number)->attributes(['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], $contact->pay_term_type, ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')])); !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-4 customer_fields">
          <div class="form-group">
              {!! html()->label(__('lang_v1.credit_limit') . ':', 'credit_limit')->attributes([]) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fas fa-money-bill-alt"></i>
                  </span>
                  {!! html()->text('credit_limit', $contact->credit_limit != null ? @num_format($contact->credit_limit) : null)->attributes(['class' => 'form-control input_number']); !!}
              </div>
              <p class="help-block">@lang('lang_v1.credit_limit_help')</p>
          </div>
        </div>
          
      <div class="col-md-12">
        <hr/>
      </div>
      
      <div class="col-md-6">
        <div class="form-group">
            {!! html()->label(__('lang_v1.address_line_1') . ':', 'address_line_1')->attributes([]) !!}
            {!! html()->text('address_line_1', $contact->address_line_1)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.address_line_1'), 'rows' => 3]); !!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
            {!! html()->label(__('lang_v1.address_line_2') . ':', 'address_line_2')->attributes([]) !!}
            {!! html()->text('address_line_2', $contact->address_line_2)->attributes(['class' => 'form-control', 
                'placeholder' => __('lang_v1.address_line_2'), 'rows' => 3]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('business.city') . ':', 'city')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('city', $contact->city)->attributes(['class' => 'form-control', 'placeholder' => __('business.city')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('business.state') . ':', 'state')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('state', $contact->state)->attributes(['class' => 'form-control', 'placeholder' => __('business.state')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('business.country') . ':', 'country')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-globe"></i>
                </span>
                {!! html()->text('country', $contact->country)->attributes(['class' => 'form-control', 'placeholder' => __('business.country')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('business.zip_code') . ':', 'zip_code')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('zip_code', $contact->zip_code)->attributes(['class' => 'form-control', 
                'placeholder' => __('business.zip_code_placeholder')]); !!}
            </div>
        </div>
      </div>

      
      <div class="col-md-4">
        <div class="form-group">
            {!! html()->label(__('business.land_mark') . ':', 'land_mark')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('land_mark', $contact->land_mark)->attributes(['class' => 'form-control', 'placeholder' => __('business.land_mark')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
            {!! html()->label(__('business.street_name') . ':', 'street_name')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('street_name', $contact->street_name)->attributes(['class' => 'form-control', 'placeholder' => __('business.street_name')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
            {!! html()->label(__('business.building_number') . ':', 'building_number')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('building_number', $contact->building_number)->attributes(['class' => 'form-control', 'placeholder' => __('business.building_number')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
            {!! html()->label(__('business.additional_number_secondary') . ':', 'additional_number')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('additional_number', $contact->additional_number)->attributes(['class' => 'form-control', 'placeholder' => __('business.additional_number')]); !!}
            </div>
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="col-md-12">
        <hr/>
      </div>
      @php
        $custom_labels = json_decode(session('business.custom_labels'), true);
        $contact_custom_field1 = !empty($custom_labels['contact']['custom_field_1']) ? $custom_labels['contact']['custom_field_1'] : __('lang_v1.contact_custom_field1');
        $contact_custom_field2 = !empty($custom_labels['contact']['custom_field_2']) ? $custom_labels['contact']['custom_field_2'] : __('lang_v1.contact_custom_field2');
        $contact_custom_field3 = !empty($custom_labels['contact']['custom_field_3']) ? $custom_labels['contact']['custom_field_3'] : __('lang_v1.contact_custom_field3');
        $contact_custom_field4 = !empty($custom_labels['contact']['custom_field_4']) ? $custom_labels['contact']['custom_field_4'] : __('lang_v1.contact_custom_field4');
        $contact_custom_field5 = !empty($custom_labels['contact']['custom_field_5']) ? $custom_labels['contact']['custom_field_5'] : __('lang_v1.custom_field', ['number' => 5]);
        $contact_custom_field6 = !empty($custom_labels['contact']['custom_field_6']) ? $custom_labels['contact']['custom_field_6'] : __('lang_v1.custom_field', ['number' => 6]);
        $contact_custom_field7 = !empty($custom_labels['contact']['custom_field_7']) ? $custom_labels['contact']['custom_field_7'] : __('lang_v1.custom_field', ['number' => 7]);
        $contact_custom_field8 = !empty($custom_labels['contact']['custom_field_8']) ? $custom_labels['contact']['custom_field_8'] : __('lang_v1.custom_field', ['number' => 8]);
        $contact_custom_field9 = !empty($custom_labels['contact']['custom_field_9']) ? $custom_labels['contact']['custom_field_9'] : __('lang_v1.custom_field', ['number' => 9]);
        $contact_custom_field10 = !empty($custom_labels['contact']['custom_field_10']) ? $custom_labels['contact']['custom_field_10'] : __('lang_v1.custom_field', ['number' => 10]);
      @endphp
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field1 . ':', 'custom_field1')->attributes([]) !!}
            {!! html()->text('custom_field1', $contact->custom_field1)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field1]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field2 . ':', 'custom_field2')->attributes([]) !!}
            {!! html()->text('custom_field2', $contact->custom_field2)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field2]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field3 . ':', 'custom_field3')->attributes([]) !!}
            {!! html()->text('custom_field3', $contact->custom_field3)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field3]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field4 . ':', 'custom_field4')->attributes([]) !!}
            {!! html()->text('custom_field4', $contact->custom_field4)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field4]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field5 . ':', 'custom_field5')->attributes([]) !!}
            {!! html()->text('custom_field5', $contact->custom_field5)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field5]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field6 . ':', 'custom_field6')->attributes([]) !!}
            {!! html()->text('custom_field6', $contact->custom_field6)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field6]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field7 . ':', 'custom_field7')->attributes([]) !!}
            {!! html()->text('custom_field7', $contact->custom_field7)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field7]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field8 . ':', 'custom_field8')->attributes([]) !!}
            {!! html()->text('custom_field8', $contact->custom_field8)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field8]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field9 . ':', 'custom_field9')->attributes([]) !!}
            {!! html()->text('custom_field9', $contact->custom_field9)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field9]); !!}
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! html()->label($contact_custom_field10 . ':', 'custom_field10')->attributes([]) !!}
            {!! html()->text('custom_field10', $contact->custom_field10)->attributes(['class' => 'form-control', 
                'placeholder' => $contact_custom_field10]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12 shipping_addr_div"><hr></div>
      <div class="col-md-8 col-md-offset-2 shipping_addr_div mb-10" >
          <strong>{{__('lang_v1.shipping_address')}}</strong><br>
          {!! html()->text('shipping_address', $contact->shipping_address)->attributes(['class' => 'form-control', 
                'placeholder' => __('lang_v1.search_address'), 'id' => 'shipping_address']); !!}
        <div class="mb-10" id="map"></div>
      </div>
      {!! html()->hidden('position', $contact->position)->attributes(['id' => 'position']); !!}
        @php
            $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

            $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

            $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';

            $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';

            $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
        @endphp

        @if(!empty($custom_labels['shipping']['is_custom_field_1_contact_default']) && !empty($shipping_custom_label_1))
            @php
                $label_1 = $shipping_custom_label_1 . ':';
            @endphp

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_1, 'shipping_custom_field_1')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_details[shipping_custom_field_1]', !empty($contact->shipping_custom_field_details['shipping_custom_field_1']) ? $contact->shipping_custom_field_details['shipping_custom_field_1'] : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_1]); !!}
                </div>
            </div>
        @endif
        @if(!empty($custom_labels['shipping']['is_custom_field_2_contact_default']) && !empty($shipping_custom_label_2))
            @php
                $label_2 = $shipping_custom_label_2 . ':';
            @endphp

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_2, 'shipping_custom_field_2')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_details[shipping_custom_field_2]', !empty($contact->shipping_custom_field_details['shipping_custom_field_2']) ? $contact->shipping_custom_field_details['shipping_custom_field_2'] : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_2]); !!}
                </div>
            </div>
        @endif
        @if(!empty($custom_labels['shipping']['is_custom_field_3_contact_default']) && !empty($shipping_custom_label_3))
            @php
                $label_3 = $shipping_custom_label_3 . ':';
            @endphp

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_3, 'shipping_custom_field_3')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_details[shipping_custom_field_3]', !empty($contact->shipping_custom_field_details['shipping_custom_field_3']) ? $contact->shipping_custom_field_details['shipping_custom_field_3'] : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_3]); !!}
                </div>
            </div>
        @endif
        @if(!empty($custom_labels['shipping']['is_custom_field_4_contact_default']) && !empty($shipping_custom_label_4))
            @php
                $label_4 = $shipping_custom_label_4 . ':';
            @endphp

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_4, 'shipping_custom_field_4')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_details[shipping_custom_field_4]', !empty($contact->shipping_custom_field_details['shipping_custom_field_4']) ? $contact->shipping_custom_field_details['shipping_custom_field_4'] : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_4]); !!}
                </div>
            </div>
        @endif
        @if(!empty($custom_labels['shipping']['is_custom_field_5_contact_default']) && !empty($shipping_custom_label_5))
            @php
                $label_5 = $shipping_custom_label_5 . ':';
            @endphp

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_5, 'shipping_custom_field_5')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_details[shipping_custom_field_5]', !empty($contact->shipping_custom_field_details['shipping_custom_field_5']) ? $contact->shipping_custom_field_details['shipping_custom_field_5'] : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_5]); !!}
                </div>
            </div>
        @endif
        @php
          $common_settings = session()->get('business.common_settings');
        @endphp
        @if(!empty($common_settings['is_enabled_export']))
            <div class="col-md-12 mb-12">
                <div class="form-check">
                    <input type="checkbox" name="is_export" class="form-check-input" id="is_customer_export" @if(!empty($contact->is_export)) checked @endif>
                    <label class="form-check-label" for="is_customer_export">@lang('lang_v1.is_export')</label>
                </div>
            </div>
            @php
                $i = 1;
            @endphp
            @for($i; $i <= 6 ; $i++)
                <div class="col-md-4 export_div" style="display: none;">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.export_custom_field'.$i).':', 'export_custom_field_'.$i)->attributes([]) !!}
                        {!! html()->text('export_custom_field_'.$i, !empty($contact['export_custom_field_'.$i]) ? $contact['export_custom_field_'.$i] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.export_custom_field'.$i)]); !!}
                    </div>
                </div>
            @endfor
        @endif
    </div>
</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->