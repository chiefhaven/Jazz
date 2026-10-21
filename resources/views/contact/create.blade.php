<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  @php
    $form_id = 'contact_add_form';
    if(isset($quick_add)){
      $form_id = 'quick_add_contact';
    }

    if(isset($store_action)) {
      $url = $store_action;
      $type = 'lead';
      $customer_groups = [];
    } else {
      $url = action([\App\Http\Controllers\ContactController::class, 'store']);
      $type = isset($selected_type) ? $selected_type : '';
      $sources = [];
      $life_stages = [];
    }
  @endphp
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => $url, 'method' => 'post', 'id' => $form_id ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_contact')</h4>
    </div>

    <div class="modal-body">
        <div class="row">            
            <div class="col-md-4 contact_type_div">
                <div class="form-group">
                    {!! html()->label(__('contact.contact_type') . ':*', 'type')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('type', $types, $type, ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required'])); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-15">
                <label class="radio-inline">
                    <input type="radio" name="contact_type_radio" id="inlineRadio1" value="individual">
                    @lang('lang_v1.individual')
                </label>
                <label class="radio-inline">
                    <input type="radio" name="contact_type_radio" id="inlineRadio2" value="business">
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
                        {!! html()->text('contact_id', null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.contact_id')]); !!}
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
                      {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('customer_group_id', $customer_groups, '', ['class' => 'form-control'])); !!}
                  </div>
                </div>
            </div>
            <div class="clearfix customer_fields"></div>
            <div class="col-md-4 business" style="display: none;">
                <div class="form-group">
                    {!! html()->label(__('business.business_name') . ':', 'supplier_business_name')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-briefcase"></i>
                        </span>
                        {!! html()->text('supplier_business_name', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.business_name')]); !!}
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! html()->label(__( 'business.prefix' ) . ':', 'prefix')->attributes([]) !!}
                    {!! html()->text('prefix', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! html()->label(__( 'business.first_name' ) . ':*', 'first_name')->attributes([]) !!}
                    {!! html()->text('first_name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! html()->label(__( 'lang_v1.middle_name' ) . ':', 'middle_name')->attributes([]) !!}
                    {!! html()->text('middle_name', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.middle_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! html()->label(__( 'business.last_name' ) . ':', 'last_name')->attributes([]) !!}
                    {!! html()->text('last_name', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
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
                        {!! html()->text('mobile', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
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
                        {!! html()->text('alternate_number', null)->attributes(['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
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
                        {!! html()->text('landline', null)->attributes(['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
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
                        {!! html()->email('email', null)->attributes(['class' => 'form-control','placeholder' => __('business.email')]); !!}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-4 individual" style="display: none;">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.dob') . ':', 'dob')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        
                        {!! html()->text('dob', null)->attributes(['class' => 'form-control dob-date-picker','placeholder' => __('lang_v1.dob'), 'readonly']); !!}
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
                      {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('crm_source', $sources, null, ['class' => 'form-control', 'id' => 'crm_source','placeholder' => __('messages.please_select')])); !!}
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
                      {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('crm_life_stage', $life_stages, null, ['class' => 'form-control', 'id' => 'crm_life_stage','placeholder' => __('messages.please_select')])); !!}
                  </div>
              </div>
            </div>

            <!-- User in create leads -->
            <div class="col-md-6 lead_additional_div">
                  <div class="form-group">
                      {!! html()->label(__('lang_v1.assigned_to') . ':*', 'user_id')->attributes([]) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-user"></i>
                          </span>
                          {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('user_id[]', $users ?? [], null, ['class' => 'form-control select2', 'id' => 'user_id', 'multiple', 'required', 'style' => 'width: 100%;'])); !!}
                      </div>
                  </div>
            </div>

            <!-- User in create customer & supplier -->
            @if(config('constants.enable_contact_assign') && $type !== 'lead')
                <div class="col-md-6">
                      <div class="form-group">
                          {!! html()->label(__('lang_v1.assigned_to') . ':', 'assigned_to_users')->attributes([]) !!}
                          <div class="input-group">
                              <span class="input-group-addon">
                                  <i class="fa fa-user"></i>
                              </span>
                              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('assigned_to_users[]', $users ?? [], null, ['class' => 'form-control select2', 'id' => 'assigned_to_users', 'multiple', 'style' => 'width: 100%;'])); !!}
                          </div>
                      </div>
                </div>
            @endif

            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm center-block more_btn" data-target="#more_div">@lang('lang_v1.more_info') <i class="fa fa-chevron-down"></i></button>
            </div>

            <div id="more_div" class="hide">
                {!! html()->hidden('position', null)->attributes(['id' => 'position']); !!}
                <div class="col-md-12"><hr/></div>

                <div class="col-md-4">
                    <div class="form-group">
                      {!! html()->label(__('contact.tax_no') . ':', 'tax_number')->attributes([]) !!}
                        <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-info"></i>
                          </span>
                          {!! html()->text('tax_number', null)->attributes(['class' => 'form-control', 'placeholder' => __('contact.tax_no')]); !!}
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
                            {!! html()->text('authorization_code', null)->attributes(['class' => 'form-control', 'placeholder' => __('contact.eis_authorization_code'), 'maxlength' => 255]); !!}
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
                          {!! html()->text('opening_balance', 0)->attributes(['class' => 'form-control input_number']); !!}
                      </div>
                  </div>
                </div>

                <div class="clearfix"></div>
                <div class="col-md-4 pay_term">
                  <div class="form-group">
                    <div class="multi-input">
                      {!! html()->label(__('contact.pay_term') . ':', 'pay_term_number')->attributes([]) !!} @show_tooltip(__('tooltip.pay_term'))
                      <br/>
                      {!! html()->input('number', 'pay_term_number', null)->attributes(['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

                      {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], '', ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')])); !!}
                    </div>
                  </div>
                </div>
                @php
                  $common_settings = session()->get('business.common_settings');
                  $default_credit_limit = !empty($common_settings['default_credit_limit']) ? $common_settings['default_credit_limit'] : null;
                @endphp
                <div class="col-md-4 customer_fields">
                  <div class="form-group">
                      {!! html()->label(__('lang_v1.credit_limit') . ':', 'credit_limit')->attributes([]) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fas fa-money-bill-alt"></i>
                          </span>
                          {!! html()->text('credit_limit', $default_credit_limit ?? null)->attributes(['class' => 'form-control input_number']); !!}
                      </div>
                      <p class="help-block">@lang('lang_v1.credit_limit_help')</p>
                  </div>
                </div>
                

                <div class="col-md-12"><hr/></div>
                <div class="clearfix"></div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.address_line_1') . ':', 'address_line_1')->attributes([]) !!}
                        {!! html()->text('address_line_1', null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.address_line_1'), 'rows' => 3]); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.address_line_2') . ':', 'address_line_2')->attributes([]) !!}
                        {!! html()->text('address_line_2', null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.address_line_2'), 'rows' => 3]); !!}
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
                        {!! html()->text('city', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.city')]); !!}
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
                    {!! html()->text('state', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.state')]); !!}
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
                    {!! html()->text('country', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.country')]); !!}
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
                    {!! html()->text('zip_code', null)->attributes(['class' => 'form-control', 
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
                    {!! html()->text('land_mark', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.land_mark')]); !!}
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
                    {!! html()->text('street_name', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.street_name')]); !!}
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
                    {!! html()->text('building_number', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.building_number')]); !!}
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
                    {!! html()->text('additional_number', null)->attributes(['class' => 'form-control', 'placeholder' => __('business.additional_number')]); !!}
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
                {!! html()->text('custom_field1', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field1]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field2 . ':', 'custom_field2')->attributes([]) !!}
                {!! html()->text('custom_field2', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field2]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field3 . ':', 'custom_field3')->attributes([]) !!}
                {!! html()->text('custom_field3', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field3]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field4 . ':', 'custom_field4')->attributes([]) !!}
                {!! html()->text('custom_field4', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field4]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field5 . ':', 'custom_field5')->attributes([]) !!}
                {!! html()->text('custom_field5', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field5]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field6 . ':', 'custom_field6')->attributes([]) !!}
                {!! html()->text('custom_field6', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field6]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field7 . ':', 'custom_field7')->attributes([]) !!}
                {!! html()->text('custom_field7', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field7]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field8 . ':', 'custom_field8')->attributes([]) !!}
                {!! html()->text('custom_field8', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field8]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field9 . ':', 'custom_field9')->attributes([]) !!}
                {!! html()->text('custom_field9', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field9]); !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! html()->label($contact_custom_field10 . ':', 'custom_field10')->attributes([]) !!}
                {!! html()->text('custom_field10', null)->attributes(['class' => 'form-control', 
                    'placeholder' => $contact_custom_field10]); !!}
            </div>
          </div>
          <div class="col-md-12 shipping_addr_div"><hr></div>
          <div class="col-md-8 col-md-offset-2 shipping_addr_div mb-10" >
              <strong>{{__('lang_v1.shipping_address')}}</strong><br>
              {!! html()->text('shipping_address', null)->attributes(['class' => 'form-control', 
                    'placeholder' => __('lang_v1.search_address'), 'id' => 'shipping_address']); !!}
            <div class="mb-10" id="map"></div>
          </div>
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
                        {!! html()->text('shipping_custom_field_details[shipping_custom_field_1]', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_1]); !!}
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
                        {!! html()->text('shipping_custom_field_details[shipping_custom_field_2]', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_2]); !!}
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
                        {!! html()->text('shipping_custom_field_details[shipping_custom_field_3]', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_3]); !!}
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
                        {!! html()->text('shipping_custom_field_details[shipping_custom_field_4]', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_4]); !!}
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
                        {!! html()->text('shipping_custom_field_details[shipping_custom_field_5]', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_5]); !!}
                    </div>
                </div>
            @endif
            @if(!empty($common_settings['is_enabled_export']))
                <div class="col-md-12 mb-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_export" class="form-check-input" id="is_customer_export">
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
                            {!! html()->text('export_custom_field_'.$i, null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.export_custom_field'.$i)]); !!}
                        </div>
                    </div>
                @endfor
            @endif
            </div>
        </div>
        @include('layouts.partials.module_form_part')
    </div>
    
    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}
  
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->