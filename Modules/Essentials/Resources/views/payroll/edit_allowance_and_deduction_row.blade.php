@php
    if($type == 'allowance') {
        $name_col = 'allowance_names';
        $val_col = 'allowance_amounts';
        $val_class = 'allowance';
        $type_col = 'allowance_types';
        $percent_col = 'allowance_percent';
    } elseif($type == 'deduction') {
        $name_col = 'deduction_names';
        $val_col = 'deduction_amounts';
        $val_class = 'deduction';
        $type_col = 'deduction_types';
        $percent_col = 'deduction_percent';
    }

    $amount_type = !empty($amount_type) ? $amount_type : 'fixed';
    $percent = $amount_type == 'percent' && !empty($percent) ?  $percent : 0;
@endphp
<tr>
    <td>
        {!! html()->text($name_col . '[]', !empty($name) ? $name : null)->attributes(['class' => 'form-control input-sm' ]); !!}
    </td>
    <td>
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })($type_col . '[]', ['fixed' => __('lang_v1.fixed'), 'percent' => __('lang_v1.percentage')], $amount_type, ['class' => 'form-control input-sm amount_type' ])); !!}
        <div class="input-group percent_field @if($amount_type != 'percent') hide @endif">
            {!! html()->text($percent_col . '[]', @num_format($percent))->attributes(['class' => 'form-control input-sm input_number percent']); !!}
            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
        </div>
    </td>
    <td>
        @php
            $readonly = $amount_type == 'percent' ? 'readonly' : '';
        @endphp
        {!! html()->text($val_col . '[]', !empty($value) ? @num_format((float) $value) : 0)->attributes(['class' => 'form-control input-sm value_field input_number ' . $val_class, $readonly ]); !!}
    </td>
    <td>
        @if(!empty($add_button))
            <button type="button" class="btn btn-primary btn-xs @if($type == 'allowance') add_allowance @elseif($type == 'deduction') add_deduction @endif">
            <i class="fa fa-plus"></i>
        @else
            <button type="button" class="btn btn-danger btn-xs remove_tr"><i class="fa fa-minus"></i></button>
        @endif
    </button></td>
</tr>