<div class="form-group">
    {!! html()->label(__('hms::lang.no_of_adult') . '*', 'no_of_adult')->attributes([]) !!}
    <select class="form-control" id="no_of_adult" required name="no_of_child">
        @for ($i = 1; $i <= $type->no_of_adult; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>
<div class="form-group">
    {!! html()->label(__('hms::lang.no_of_child') . '*', 'no_of_child')->attributes([]) !!}
    <select class="form-control" id="no_of_child" required name="no_of_child">
        @for ($i = 0; $i <= $type->no_of_child; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>
<div class="form-group">
    {!! html()->label(__('hms::lang.room_no') . '*', 'room_no')->attributes([]) !!}
    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('room_no', $rooms, '', [
        'class' => 'form-control',
        'required',
        'id' => 'room_no',
        'placeholder' => __('hms::lang.room_no'),
    ])) !!}
</div>
