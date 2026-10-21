<div class="modal fade" tabindex="-1" role="dialog" id="confirmSuspendModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang('lang_v1.suspend_sale')</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
				        <div class="form-group">
				            {!! html()->label(__('lang_v1.suspend_note') . ':', 'additional_notes')->attributes([]) !!}
				            {!! html()->textarea('additional_notes', !empty($transaction->additional_notes) ? $transaction->additional_notes : null)->attributes(['class' => 'form-control','rows' => '4']); !!}
				            {!! html()->hidden('is_suspend', 0)->attributes(['id' => 'is_suspend']); !!}
				        </div>
				    </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="pos-suspend">@lang('messages.save')</button>
			    <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->