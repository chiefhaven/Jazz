<div class="modal fade" id ="woocommerce_sync_modal" tabindex="-1" role="dialog">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ProductController::class, 'toggleWooCommerceSync']), 'method' => 'post', 'id' => 'toggle_woocommerce_sync_form' ]))->open() !!}
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    @lang('lang_v1.woocommerce_sync')
                </h4>
              </div>
              <div class="modal-body">
                <input type="hidden" id="woocommerce_products_sync" name="woocommerce_products_sync" value="">
                <div class="row">
                    <div class="col-md-12">
                        <label for="woocommerce_disable_sync">
                            @lang('lang_v1.woocommerce_sync')
                        </label>
                        <select name="woocommerce_disable_sync" class="form-control" id="woocommerce_disable_sync">
                            <option value="0">
                                @lang('lang_v1.enable')
                            </option>
                            <option value="1">
                                @lang('lang_v1.disable')
                            </option>
                        </select>
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white ladda-button">
                    @lang('messages.save')
                </button>
              </div>
            </div>
        </div>
    {!! html()->form()->close() !!}
</div>