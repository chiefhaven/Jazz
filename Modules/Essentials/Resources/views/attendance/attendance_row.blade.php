<tr data-user_id="{{$user->id}}">
	<td>
		{{$user->user_full_name}}
	</td>
	<td>
		@if(empty($attendance->clock_in_time))
			<div class="input-group date">
				{!! html()->text('attendance[' . $user->id . '][clock_in_time]', null)->attributes(['class' => 'form-control date_time_picker', 'placeholder' => __( 'essentials::lang.clock_in_time' ), 'readonly', 'required' ]); !!}
				<span class="input-group-addon"><i class="fas fa-clock"></i></span>
			</div>
		@else
			{{@format_datetime($attendance->clock_in_time)}} <br>
			<small class="text-muted">(@lang('essentials::lang.clocked_in') - {{\Carbon::parse($attendance->clock_in_time)->diffForHumans(\Carbon::now())}})</small>

			{!! html()->hidden('attendance[' . $user->id . '][id]', $attendance->id)->attributes([]); !!}
		@endif
	</td>
	<td>
		<div class="input-group date">
			{!! html()->text('attendance[' . $user->id . '][clock_out_time]', null)->attributes(['class' => 'form-control date_time_picker', 'placeholder' => __( 'essentials::lang.clock_out_time' ), 'readonly' ]); !!}
			<span class="input-group-addon"><i class="fas fa-clock"></i></span>
		</div>
	</td>
	<td>
		{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('attendance[' . $user->id . '][essentials_shift_id]', $shifts, !empty($attendance->essentials_shift_id) ? $attendance->essentials_shift_id : null, ['class' => 'form-control', 'placeholder' => __( 'messages.please_select' ) ])); !!}
	</td>
	<td>
		{!! html()->text('attendance[' . $user->id . '][ip_address]', !empty($attendance->ip_address) ? $attendance->ip_address : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.ip_address') ]); !!}
	</td>
	<td>
		{!! html()->textarea('attendance[' . $user->id . '][clock_in_note]', !empty($attendance->clock_in_note) ? $attendance->clock_in_note : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_note'), 'rows' => 3 ]); !!}
	</td>
	<td>
		{!! html()->textarea('attendance[' . $user->id . '][clock_out_note]', !empty($attendance->clock_out_note) ? $attendance->clock_out_note : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_note'), 'rows' => 3 ]); !!}
	</td>
	<td><button type="button" class="btn btn-xs btn-danger remove_attendance_row"><i class="fa fa-times"></i></button></td>
</tr>