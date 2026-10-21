<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! html()->label('CC:', 'cc')->attributes([])!!}
            {!! html()->text('cc', !empty($proposal_template) ? $proposal_template->cc : '')->attributes(['class' => 'form-control' ]) !!}
            <p class="help-block">@lang('crm::lang.comma_separated_email')</p>
       </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! html()->label('BCC:', 'bcc')->attributes([])!!}
            {!! html()->text('bcc', !empty($proposal_template) ? $proposal_template->bcc : '')->attributes(['class' => 'form-control' ]) !!}
            <p class="help-block">@lang('crm::lang.comma_separated_email')</p>
       </div> 
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! html()->label(__('crm::lang.subject') . ':*', 'subject')->attributes([])!!}
            {!! html()->text('subject', !empty($proposal_template) ? $proposal_template->subject : '')->attributes(['class' => 'form-control', 'required' ]) !!}
       </div>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
        <div class="form-group">
            {!! html()->label(__('crm::lang.email_body') . ':*', 'body')->attributes([]) !!}
            {!! html()->textarea('body', !empty($proposal_template) ? $proposal_template->body : '')->attributes(['class' => 'form-control', 'id' => 'proposal_email_body','required']); !!}
        </div>
        @error('body')
        	<label id="proposal_email_body-error" class="error" for="body">
        		{{ $message }}
        	</label>
		@enderror
    </div>
</div>
@if($attachments)
	<div class="row">
		<div class="col-sm-12">
	      <div class="form-group">
	        {!! html()->label(__('crm::lang.attachments') . ':', 'attachments')->attributes([]) !!}
	        {!! html()->file('attachments[]')->attributes(['id' => 'attachments', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
	        <small>
	        	<p class="help-block">
	        		<p class="help-block">
	                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
	                    @includeIf('components.document_help_text')
	                </p>
	        	</p>
	        </small>
	      </div>
	    </div>
	</div>
@endif