@php
    $variation_name = !empty($variation_name) ? $variation_name : null;
    $variation_value_id = !empty($variation_value_id) ? $variation_value_id : null;

    $name = (empty($row_type) || $row_type == 'add') ? 'product_variation' : 'product_variation_edit';

    $readonly = !empty($variation_value_id) ? 'readonly' : '';
@endphp

@if(!session('business.enable_price_tax')) 
    @php
        $default = 0;
        $class = 'hide';
    @endphp
@else
    @php
        $default = null;
        $class = '';
    @endphp
@endif

@php
    $is_variation_value_hidden = !empty($variation_value_id) ? 1 : 0;
@endphp

<tr @if(!empty($variation_value_id)) 
        data-variation_value_id="{{$variation_value_id}}" 
        class="variation_value_row hide" 
    @endif>
    <td>
        {!! html()->text($name . '[' . $variation_index . '][variations][' . $value_index . '][sub_sku]', null)->attributes(['class' => 'form-control input-sm input_sub_sku']); !!}
        {!! html()->hidden($name . '[' . $variation_index . '][variations][' . $value_index . '][is_hidden]', $is_variation_value_hidden)->attributes(['class' => 'is_variation_value_hidden']) !!}

        {!! html()->hidden($name . '[' . $variation_index . '][variations][' . $value_index . '][variation_value_id]', $variation_value_id)->attributes([]) !!}
    </td>
    <td>
        {!! html()->text($name . '[' . $variation_index . '][variations][' . $value_index . '][value]', $variation_name)->attributes(['class' => 'form-control input-sm variation_value_name', 'required', $readonly]); !!}
    </td>
    <td class="{{$class}}">
        <div class="width-50 f-left">
            {!! html()->text($name . '[' . $variation_index . '][variations][' . $value_index . '][default_purchase_price]', $default)->attributes(['class' => 'form-control input-sm variable_dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}
        </div>

        <div class="width-50 f-left">
            <div class="input-group">
                {!! html()->text($name . '[' . $variation_index . '][variations][' . $value_index . '][dpp_inc_tax]', $default)->attributes(['class' => 'form-control input-sm variable_dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
                @if($value_index == 0)
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat apply-all btn-sm p-5-5" data-toggle="tooltip" title="@lang('lang_v1.apply_all')" data-target-class=".variable_dpp_inc_tax"><i class="fas fa-check-double"></i></button>
                    </span>
                @endif
            </div>
        </div>
    </td>
    <td class="{{$class}}">
        <div class="input-group">
            {!! html()->text($name . '[' . $variation_index . '][variations][' . $value_index . '][profit_percent]', $profit_percent)->attributes(['class' => 'form-control input-sm variable_profit_percent input_number', 'required']); !!}
            @if($value_index == 0)
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat apply-all btn-sm p-5-5" data-toggle="tooltip" title="@lang('lang_v1.apply_all')" data-target-class=".variable_profit_percent"><i class="fas fa-check-double"></i></button>
                </span>
            @endif
        </div>
    </td>
    <td class="{{$class}}">
        {!! html()->text($name . '[' . $variation_index . '][variations][' . $value_index . '][default_sell_price]', $default)->attributes(['class' => 'form-control input-sm variable_dsp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}

        {!! html()->text($name . '[' . $variation_index . '][variations][' . $value_index . '][sell_price_inc_tax]', $default)->attributes(['class' => 'form-control input-sm variable_dsp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
    </td>
    <td>{!! html()->file('variation_images_' . $variation_index . '_' . $value_index . '[]')->attributes(['class' => 
        'variation_images', 'accept' => 'image/*', 'multiple']); !!}</td>
    <td>
        <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error remove_variation_value_row"><i class="fa fa-trash"></i></button>
        <input type="hidden" class="variation_row_index" value="{{$value_index}}">
    </td>
</tr>