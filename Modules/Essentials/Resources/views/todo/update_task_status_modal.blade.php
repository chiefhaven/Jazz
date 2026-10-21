<div class="modal fade" id="update_task_status_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
  		<div class="modal-content">
  			<div class="modal-header">
		      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      	<h4 class="modal-title">@lang( 'essentials::lang.change_status' )</h4>
		    </div>
		    <div class="modal-body">
	  			<div class="form-group">
					{!! html()->label(__('sale.status') . ':', 'updated_status')->attributes([]) !!}
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('status', $task_statuses, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'updated_status'])); !!}
					{!! html()->hidden('task_id', null)->attributes(['id' => 'task_id']); !!}
				</div>
  			</div>
  			<div class="modal-footer">
		      	<button type="button" class="btn btn-primary" id="update_status_btn">@lang( 'messages.update' )</button>
		      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
		    </div>
  		</div>
  	</div>
</div>