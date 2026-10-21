<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\DocumentShareController::class, 'update'], [$id]), 'id' => 'share_document_form', 'method' => 'put']))->open() !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title text-center" id="exampleModalLabel">
        @if(!empty($type))
          @lang('essentials::lang.share_memos')
        @else
          @lang('essentials::lang.share_document')
        @endif
      </h4>
    </div>
    <div class="modal-body">
      
        <input type="hidden" name="document_id" id="document_id" value="{{$id}}">
        <div class="form-group">
            {!! html()->label(__('essentials::lang.user').':', 'user')->attributes([]) !!} <br>
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('user[]', $users, $shared_user, ['class' => 'form-control select2', 'multiple' => 'multiple', 'style'=>"width: 50%; height:50%"])); !!}
        </div>
        <div class="form-group">
            {!! html()->label(__('essentials::lang.role').':', 'role')->attributes([]) !!} <br>
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('role[]', $roles, $shared_role, ['class' => 'form-control select2', 'multiple' => 'multiple', 'style'=>"width: 50%; height:50%"])); !!}
        </div>

    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary pull-right ladda-button doc-share-btn" data-style="expand-right">
          <span class="ladda-label">@lang('messages.update')</span>
      </button>
    </div>
  </div>
  {!! html()->form()->close() !!}
</div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    __select2($('.select2'));
  })
</script>