<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\DiscountController::class, 'update'], [$discount->id]), 'method' => 'put', 'id' => 'discount_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'sale.edit_discount' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            {!! html()->label(__( 'unit.name' ) . ':*', 'name')->attributes([]) !!}
              {!! html()->text('name', $discount->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' ) ]); !!}
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! html()->label(__('report.products') . ':', 'variation_ids')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('variation_ids[]', $variations, array_keys($variations), ['id' => "variation_ids", 'class' => 'form-control', 'multiple'])); !!}
          </div>
        </div>
        <div class="col-md-6 @if(!empty($variations)) hide @endif" id="brand_input">
          <div class="form-group">
            {!! html()->label(__('product.brand') . ':', 'brand_id')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('brand_id', $brands, $discount->brand_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%;'])); !!}
          </div>
        </div>
        <div class="col-sm-6 @if(!empty($variations)) hide @endif" id="category_input">
          <div class="form-group">
            {!! html()->label(__('product.category') . ':', 'category_id')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('category_id', $categories, $discount->category_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%;'])); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! html()->label(__('sale.location') . ':*', 'location_id')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $locations, $discount->location_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required'])); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! html()->label(__( 'lang_v1.priority' ) . ':', 'priority')->attributes([]) !!}
              {!! html()->text('priority', $discount->priority)->attributes(['class' => 'form-control input_number', 'required', 'placeholder' => __( 'lang_v1.priority' ) ]); !!}
          </div>
        </div>
         <div class="col-sm-6">
          <div class="form-group">
            {!! html()->label(__('sale.discount_type') . ':*', 'discount_type')->attributes([]) !!}
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $discount->discount_type, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required'])); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! html()->label(__( 'sale.discount_amount' ) . ':*', 'discount_amount')->attributes([]) !!}
              {!! html()->text('discount_amount', $discount->discount_amount)->attributes(['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.discount_amount' ) ]); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! html()->label(__( 'lang_v1.starts_at' ) . ':', 'starts_at')->attributes([]) !!}
              {!! html()->text('starts_at', $starts_at)->attributes(['class' => 'form-control discount_date', 'required', 'placeholder' => __( 'lang_v1.starts_at' ), 'readonly' ]); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! html()->label(__( 'lang_v1.ends_at' ) . ':', 'ends_at')->attributes([]) !!}
              {!! html()->text('ends_at', $ends_at)->attributes(['class' => 'form-control discount_date', 'required', 'placeholder' => __( 'lang_v1.ends_at' ), 'readonly' ]); !!}
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! html()->label(__('lang_v1.selling_price_group') . ':', 'spg')->attributes([]) !!}
            <select name="spg" class="form-control">
              <option value="" @if(is_null($discount->spg)) selected @endif>@lang('lang_v1.all')</option>
              @foreach($price_groups as $k => $v)
                <option value="{{$k}}" @if($discount->spg === (string)$k) selected @endif>{{$v}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            <br>
            <label>
              {!! html()->checkbox('applicable_in_cg', !empty($discount->applicable_in_cg), 1)->attributes(['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.applicable_in_cg')</strong>
            </label>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="form-group">
            <br>
            <label>
              {!! html()->checkbox('is_active', !empty($discount->is_active), 1)->attributes(['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.is_active')</strong>
            </label>
          </div>
        </div>

      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->