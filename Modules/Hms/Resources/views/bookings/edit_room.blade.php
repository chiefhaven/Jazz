<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['id' => 'edit_booking_room' ]))->open() !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"> @lang('hms::lang.edit_room')</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            {!! html()->label(__('hms::lang.type') . '*', 'name')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('type', $types, $type->id, [
                'class' => 'form-control',
                'required',
                'id' => 'type',
                'disabled' => true,
                'placeholder' => __('hms::lang.type'),
            ])) !!}
        </div>
       <div class="modify_field"> 
        <div class="form-group">
            {!! html()->label(__('hms::lang.no_of_adult') . '*', 'no_of_adult')->attributes([]) !!}
            <select class="form-control" id="no_of_adult" required name="no_of_child">
                @for ($i = 1; $i <= $type->no_of_adult; $i++)
                    <option {{ $i == $no_of_adult ? 'selected' : ''}} value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.no_of_child') . '*', 'no_of_child')->attributes([]) !!}
            <select class="form-control" id="no_of_child" required name="no_of_child">
                @for ($i = 0; $i <= $type->no_of_child; $i++)
                    <option {{ $i == $no_of_child ? 'selected' : ''}} value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
          <div class="form-group">
            {!! html()->label(__('hms::lang.room_no') . '*', 'room_no')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('room_no', $rooms, $room_id, [
                'class' => 'form-control',
                'required',
                'id' => 'room_no',
                'placeholder' => __('hms::lang.room_no'),
            ])) !!}
          </div>
       </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! html()->form()->close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->