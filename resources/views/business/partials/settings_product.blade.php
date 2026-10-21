<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('business.sku_prefix') . ':', 'sku_prefix')->attributes([]) !!}
                 {!! html()->text('sku_prefix', $business->sku_prefix)->attributes(['class' => 'form-control text-uppercase']); !!}
            </div>
        </div>
        
        <div class="col-sm-4">
            {!! html()->label(__( 'product.enable_product_expiry' ) . ':', 'enable_product_expiry')->attributes([]) !!}
            @show_tooltip(__('lang_v1.tooltip_enable_expiry'))

            <div class="input-group">
                <span class="input-group-addon">
                    {!! html()->checkbox('enable_product_expiry', $business->enable_product_expiry, 1)->attributes([]); !!} 
                </span>

                <select class="form-control" id="expiry_type"
                    name="expiry_type" 
                    @if(!$business->enable_product_expiry) disabled @endif>
                    <option value="add_expiry" @if($business->expiry_type == 'add_expiry') selected @endif>
                        {{__('lang_v1.add_expiry')}}
                    </option>
                  <option value="add_manufacturing" @if($business->expiry_type == 'add_manufacturing') selected @endif>{{__('lang_v1.add_manufacturing_auto_expiry')}}</option>
                </select>
            </div>
        </div>

        <div class="col-sm-4 @if(!$business->enable_product_expiry) hide @endif" id="on_expiry_div">
            <div class="form-group">
                <div class="multi-input">
                    {!! html()->label(__('lang_v1.on_product_expiry') . ':', 'on_product_expiry')->attributes([]) !!}
                    @show_tooltip(__('lang_v1.tooltip_on_product_expiry'))
                    <br>

                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('on_product_expiry', ['keep_selling'=>__('lang_v1.keep_selling'), 'stop_selling'=>__('lang_v1.stop_selling') ], $business->on_product_expiry, ['class' => 'form-control pull-left', 'style' => 'width:60%;'])); !!}

                    @php
                        $disabled = '';
                        if($business->on_product_expiry == 'keep_selling'){
                            $disabled = 'disabled';
                        }
                    @endphp

                    {!! html()->input('number', 'stop_selling_before', $business->stop_selling_before)->attributes(['class' => 'form-control pull-left', 'placeholder' => 'stop n days before', 'style' => 'width:40%;', $disabled, 'required', 'id' => 'stop_selling_before']); !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_brand', $business->enable_brand, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_brand' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_category', $business->enable_category, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'enable_category']); !!} {{ __( 'lang_v1.enable_category' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4 enable_sub_category @if($business->enable_category != 1) hide @endif">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_sub_category', $business->enable_sub_category, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'enable_sub_category']); !!} {{ __( 'lang_v1.enable_sub_category' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_price_tax', $business->enable_price_tax, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_price_tax' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.default_unit') . ':', 'default_unit')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-balance-scale"></i>
                    </span>
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('default_unit', $units_dropdown, $business->default_unit, ['class' => 'form-control select2', 'style' => 'width: 100%;' ])); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_sub_units', $business->enable_sub_units, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_sub_units' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.sub_units_tooltip'))
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_racks', $business->enable_racks, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_racks' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.tooltip_enable_racks'))
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_row', $business->enable_row, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_row' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('enable_position', $business->enable_position, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_position' ) }}
                  </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('common_settings[enable_product_warranty]', !empty($common_settings['enable_product_warranty']) ? true : false, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_product_warranty' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4 @if(config('constants.enable_secondary_unit') == false) hide @endif">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('common_settings[enable_secondary_unit]', !empty($common_settings['enable_secondary_unit']) ? true : false, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_secondary_unit' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('common_settings[is_product_image_required]', !empty($common_settings['is_product_image_required']) ? true : false, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.is_product_image_required' ) }}
                  </label>
                </div>
            </div>
        </div>

    </div>
</div>