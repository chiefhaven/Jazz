<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Hms\Http\Controllers\ExtraController::class, 'update'], ['extra'=> $extra->id]), 'method' => 'put', 'id' => 'add_extra' ]))->open() !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'hms::lang.edit_extra' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            {!! html()->label(__('hms::lang.name') . '*', 'name')->attributes([]) !!}
            {!! html()->text('name', $extra->name)->attributes([
                'class' => 'form-control',
                'required',
                'placeholder' => __('hms::lang.name'),
            ]) !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.price') . '*', 'price')->attributes([]) !!}
            {!! html()->input('number', 'price', $extra->price)->attributes([
                'class' => 'form-control',
                'required',
                'step' => '0.01',
                'placeholder' => __('hms::lang.price'),
            ]) !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('hms::lang.per') . '*', 'price_per')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('price_per', $price_per, $extra->price_per, [
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