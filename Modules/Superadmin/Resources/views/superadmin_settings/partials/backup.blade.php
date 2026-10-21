<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.backup_disk') . ':', 'BACKUP_DISK')->attributes([]) !!}
            	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('BACKUP_DISK', $backup_disk, $default_values['BACKUP_DISK'], ['class' => 'form-control'])); !!}
            </div>
        </div>
        <div class="col-xs-8 @if(env('BACKUP_DISK') != 'dropbox') hide @endif" id="dropbox_access_token_div">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.dropbox_access_token') . ':', 'DROPBOX_ACCESS_TOKEN')->attributes([]) !!}
            	{!! html()->text('DROPBOX_ACCESS_TOKEN', $default_values['DROPBOX_ACCESS_TOKEN'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.dropbox_access_token')]); !!}
            </div>
            <p class="help-block">{!! __('superadmin::lang.dropbox_help', ['link' => 'https://www.dropbox.com/developers/apps/create']) !!}</p>
        </div>
    </div>
</div>