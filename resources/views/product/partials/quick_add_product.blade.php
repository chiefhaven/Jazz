<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ProductController::class, 'saveQuickProduct']), 'method' => 'post', 'id' => 'quick_add_product_form' ]))->open() !!}

    <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title" id="modalTitle">@lang( 'product.add_new_product' )</h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! html()->label(__('product.product_name') . ':*', 'name')->attributes([]) !!}
              {!! html()->text('name', $product_name)->attributes(['class' => 'form-control', 'required',
              'placeholder' => __('product.product_name')]); !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('type', ['single' => 'Single', 'variable' => 'Variable'], 'single', ['class' => 'hide', 'id' => 'type'])); !!}
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('product.sku') . ':', 'sku')->attributes([]) !!} @show_tooltip(__('tooltip.sku'))
            {!! html()->text('sku', null)->attributes(['class' => 'form-control',
              'placeholder' => __('product.sku')]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('product.barcode_type') . ':*', 'barcode_type')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('barcode_type', $barcode_types, 'C128', ['class' => 'form-control select2', 'required'])); !!}
          </div>
        </div>
        <div class="clearfix"></div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('product.unit') . ':*', 'unit_id')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('unit_id', $units, null, ['class' => 'form-control select2', 'required'])); !!}
          </div>
        </div>

        <div class="col-sm-4 @if(!session('business.enable_sub_units')) hide @endif">
          <div class="form-group">
            {!! html()->label(__('lang_v1.related_sub_units') . ':', 'sub_unit_ids')->attributes([]) !!} @show_tooltip(__('lang_v1.sub_units_tooltip'))

            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sub_unit_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'sub_unit_ids'])); !!}
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('product.brand') . ':', 'brand_id')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('brand_id', $brands, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'])); !!}
          </div>
        </div>
        
        <div class="clearfix"></div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('product.category') . ':', 'category_id')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('category_id', $categories, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'])); !!}
          </div>
        </div>

        <div class="col-sm-4 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
          <div class="form-group">
            {!! html()->label(__('product.sub_category') . ':', 'sub_category_id')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sub_category_id', [], null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'])); !!}
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
          <br>
            <label>
              {!! html()->checkbox('enable_stock', true, 1)->attributes(['class' => 'input-icheck', 'id' => 'enable_stock']); !!} <strong>@lang('product.manage_stock')</strong>
            </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4" id="alert_quantity_div">
          <div class="form-group">
            {!! html()->label(__('product.alert_quantity') . ':', 'alert_quantity')->attributes([]) !!}
            {!! html()->text('alert_quantity', null)->attributes(['class' => 'form-control input_number',
            'placeholder' => __('product.alert_quantity'), 'min' => '0']); !!}
          </div>
        </div>
        @if(!empty($common_settings['enable_product_warranty']))
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('lang_v1.warranty') . ':', 'warranty_id')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('warranty_id', $warranties, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
          </div>
        </div>
        @endif
        @if(session('business.enable_product_expiry'))
          @if(session('business.expiry_type') == 'add_expiry')
              @php
                $expiry_period = 12;
                $hide = true;
              @endphp
          @else
              @php
                $expiry_period = null;
                $hide = false;
              @endphp
          @endif
        <div class="col-sm-4 @if($hide) hide @endif">
          <div class="form-group">
            <div class="multi-input">
              {!! html()->label(__('product.expires_in') . ':', 'expiry_period')->attributes([]) !!}<br>
              {!! html()->text('expiry_period', $expiry_period)->attributes(['class' => 'form-control pull-left input_number',
                'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;']); !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ], 'months', ['class' => 'form-control select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type'])); !!}
            </div>
          </div>
        </div>
        @endif
        @php
          $default_location = null;
          if(count($business_locations) == 1){
            $default_location = array_key_first($business_locations->toArray());
          }
        @endphp
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('business.business_locations') . ':', 'product_locations')->attributes([]) !!} @show_tooltip(__('lang_v1.product_location_help'))
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('product_locations[]', $business_locations, $default_location, ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations'])); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('lang_v1.weight') . ':', 'weight')->attributes([]) !!}
            {!! html()->text('weight', null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-8">
          <div class="form-group">
            {!! html()->label(__('lang_v1.product_description') . ':', 'product_description')->attributes([]) !!}
              {!! html()->textarea('product_description', null)->attributes(['class' => 'form-control']); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('product.applicable_tax') . ':', 'tax')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('tax', $taxes, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'])); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('product.selling_price_tax_type') . ':*', 'tax_type')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], 'exclusive', ['class' => 'form-control select2', 'required'])); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="checkbox">
          <br>
            <label>
              {!! html()->checkbox('enable_sr_no', false, 1)->attributes(['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
            </label>@show_tooltip(__('lang_v1.tooltip_sr_no'))
          </div>
        </div>
        <div class="clearfix"></div>
        @php
        $custom_labels = json_decode(session('business.custom_labels'), true);
        $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
        $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
        $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
        $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
      @endphp
        <div class="col-sm-4">
          <div class="form-group">
            <br>
            <label>
              {!! html()->checkbox('not_for_selling', false, 1)->attributes(['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
            </label> @show_tooltip(__('lang_v1.tooltip_not_for_selling'))
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label($product_custom_field1 . ':', 'product_custom_field1')->attributes([]) !!}
            {!! html()->text('product_custom_field1', null)->attributes(['class' => 'form-control', 'placeholder' => $product_custom_field1]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label($product_custom_field2 . ':', 'product_custom_field2')->attributes([]) !!}
            {!! html()->text('product_custom_field2', null)->attributes(['class' => 'form-control', 'placeholder' => $product_custom_field2]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label($product_custom_field3 . ':', 'product_custom_field3')->attributes([]) !!}
            {!! html()->text('product_custom_field3', null)->attributes(['class' => 'form-control', 'placeholder' => $product_custom_field3]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label($product_custom_field4 . ':', 'product_custom_field4')->attributes([]) !!}
            {!! html()->text('product_custom_field4', null)->attributes(['class' => 'form-control', 'placeholder' => $product_custom_field4]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        @if(!empty($module_form_parts))
          @foreach($module_form_parts as $key => $value)
            @if(!empty($value['template_path']))
              @php
                $template_data = $value['template_data'] ?: [];
              @endphp
              @include($value['template_path'], $template_data)
            @endif
          @endforeach
        @endif
      </div>
      <div class="row">
        <div class="form-group col-sm-11 col-sm-offset-1">
          @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent, 'quick_add' => true ])
        </div>
      </div>
      @if(!empty($product_for) && $product_for == 'pos')
        @include('product.partials.quick_product_opening_stock', ['locations' => $locations])
      @endif
    </div>
    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="submit_quick_product">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready(function(){
    $("form#quick_add_product_form").validate({
      rules: {
          sku: {
              remote: {
                  url: "/products/check_product_sku",
                  type: "post",
                  data: {
                      sku: function() {
                          return $( "#sku" ).val();
                      },
                      product_id: function() {
                          if($('#product_id').length > 0 ){
                              return $('#product_id').val();
                          } else {
                              return '';
                          }
                      },
                  }
              }
          },
          expiry_period:{
              required: {
                  depends: function(element) {
                      return ($('#expiry_period_type').val().trim() != '');
                  }
              }
          }
      },
      messages: {
          sku: {
              remote: LANG.sku_already_exists
          }
      },
      submitHandler: function (form) {
        
        var form = $("form#quick_add_product_form");
        var url = form.attr('action');
        form.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            method: "POST",
            url: url,
            dataType: 'json',
            data: $(form).serialize(),
            success: function(data){
                $('.quick_add_product_modal').modal('hide');
                if( data.success){
                    toastr.success(data.msg);
                    if (typeof get_purchase_entry_row !== 'undefined') {
                      var selected_location = $('#location_id').val();
                      var location_check = true;
                      if (data.locations && selected_location && data.locations.indexOf(selected_location) == -1) {
                        location_check = false;
                      }
                      if (location_check) {
                        get_purchase_entry_row( data.product.id, 0 );
                      }
                      
                    }
                    $(document).trigger({type: "quickProductAdded", 'product': data.product, 'variation': data.variation });
                } else {
                    toastr.error(data.msg);
                }
            }
        });
        return false;
      }
    });
  });
</script>