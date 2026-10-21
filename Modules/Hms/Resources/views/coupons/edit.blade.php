<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'update'],[$coupon->id]), 'method' => 'put', 'id' => 'edit_coupon' ]))->open() !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'hms::lang.edit_coupon' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            {!! html()->label(__('hms::lang.type') . '*', 'hms_room_type_id')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('hms_room_type_id', $types, $coupon->hms_room_type_id, [
                'class' => 'form-control',
                'required',
                'placeholder' => __('hms::lang.type'),
            ])) !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.date_from') . '*', 'date_from')->attributes([]) !!}
            {!! html()->text('start_date', !empty($coupon->start_date) ? @format_date($coupon->start_date) : null)->attributes([
                'class' => 'form-control date_picker',
                'required',
                'readonly',
            ]) !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.date_to') . '*', 'date_to')->attributes([]) !!}
            {!! html()->text('end_date', !empty($coupon->end_date) ? @format_date($coupon->end_date) : null)->attributes([
                'class' => 'form-control date_picker',
                'required',
                'readonly',
            ]) !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.coupon_code') . '*', 'coupon_code')->attributes([]) !!}
            {!! html()->text('coupon_code', $coupon->coupon_code)->attributes([
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.discount') . '*', 'discount')->attributes([]) !!}
            {!! html()->input('number', 'discount', $coupon->discount)->attributes([
                'class' => 'form-control',
                'required',
                'step' => '0.01',
            ]) !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.discount_type'). '*', 'discount_type')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('discount_type', $discount_type, $coupon->discount_type, [
                'class' => 'form-control',
                'required',
            ])) !!}
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! html()->form()->close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->