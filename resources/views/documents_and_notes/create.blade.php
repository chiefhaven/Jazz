<div class="modal-dialog modal-lg" role="document">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['action' => '\App\Http\Controllers\DocumentAndNoteController@store', 'id' => 'docus_notes_form', 'method' => 'post']))->open() !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('lang_v1.add_note')
            </h4>
        </div>
        <!-- model id like project_id, user_id -->
        {!! html()->hidden('notable_id', $notable_id)->attributes(['class' => 'form-control']) !!}
        <!-- model name like App\User -->
        {!! html()->hidden('notable_type', $notable_type)->attributes(['class' => 'form-control']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                        {!! html()->label(__('lang_v1.heading') . ':*', 'heading')->attributes([])!!}
                        {!! html()->text('heading', null)->attributes(['class' => 'form-control', 'required' ]) !!}
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.description') . ':', 'description')->attributes([]) !!}
                        {!! html()->textarea('description', null)->attributes(['class' => 'form-control ', 'id' => 'docs_note_description']); !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="fileupload">
                            @lang('lang_v1.documents'):
                        </label>
                        <div class="dropzone" id="docusUpload"></div>
                    </div>
                    <input type="hidden" id="docus_notes_media" name="file_name[]" value="">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_private" value="1"> @lang('lang_v1.is_private')
                                <i class="fa fa-info-circle" data-toggle="tooltip" title="@lang('lang_v1.note_will_be_visible_to_u_only')"></i>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                @lang('messages.save')
            </button>
             <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div><!-- /.modal-content -->
    {!! html()->form()->close() !!}
</div><!-- /.modal-dialog -->