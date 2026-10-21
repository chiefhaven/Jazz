<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Manufacturing\Http\Controllers\RecipeController::class, 'addIngredients']), 'method' => 'get', 'id' => 'choose_product_form']))->open() !!}

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang( 'manufacturing::lang.choose_product' )</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    {!! html()->label(__('manufacturing::lang.choose_product').':', 'variation_id')->attributes([]) !!}
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('variation_id', [], null, ['class' => 'form-control', 'id' => 'variation_id', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                </div>
                <div class="form-group" id="recipe_selection">
                    {!! html()->label(__('manufacturing::lang.copy_from_recipe').':', 'copy_recipe_id')->attributes([]) !!}
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('copy_recipe_id', $recipes, null, ['class' => 'form-control', 'placeholder' => __('lang_v1.none'), 'style' => 'width: 100%;'])); !!}
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                <button type="submit" class="btn btn-primary">@lang( 'manufacturing::lang.continue' )</button>
            </div>

        {!! html()->form()->close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->