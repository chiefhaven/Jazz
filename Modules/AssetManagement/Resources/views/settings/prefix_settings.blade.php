<div class="pos-tab-content active">
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! html()->label(__('assetmanagement::lang.asset_code_prefix') . ':', 'asset_code_prefix')->attributes([]) !!}
						{!! html()->text('asset_code_prefix', !empty($asset_settings['asset_code_prefix'])? $asset_settings['asset_code_prefix'] : '')->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_code_prefix')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! html()->label(__('assetmanagement::lang.allocation_code_prefix') . ':', 'allocation_code_prefix')->attributes([]) !!}
						{!! html()->text('allocation_code_prefix', !empty($asset_settings['allocation_code_prefix'])? $asset_settings['allocation_code_prefix'] : '')->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.allocation_code_prefix')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! html()->label(__('assetmanagement::lang.revoke_code_prefix') . ':', 'revoke_code_prefix')->attributes([]) !!}
						{!! html()->text('revoke_code_prefix', !empty($asset_settings['revoke_code_prefix'])? $asset_settings['revoke_code_prefix'] : '')->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.revoke_code_prefix')]); !!}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! html()->label(__('assetmanagement::lang.asset_maintenance_prefix') . ':', 'asset_maintenance_prefix')->attributes([]) !!}
						{!! html()->text('asset_maintenance_prefix', !empty($asset_settings['asset_maintenance_prefix'])? $asset_settings['asset_maintenance_prefix'] : '')->attributes(['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_maintenance_prefix')]); !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>