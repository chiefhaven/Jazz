<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('business.tax_1_name') . ':', 'tax_label_1')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! html()->text('tax_label_1', $business->tax_label_1)->attributes(['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('business.tax_1_no') . ':', 'tax_number_1')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! html()->text('tax_number_1', $business->tax_number_1)->attributes(['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('business.tax_2_name') . ':', 'tax_label_2')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! html()->text('tax_label_2', $business->tax_label_2)->attributes(['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('business.tax_2_no') . ':', 'tax_number_2')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! html()->text('tax_number_2', $business->tax_number_2)->attributes(['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! html()->checkbox('enable_inline_tax', $business->enable_inline_tax, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_inline_tax' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>
</div>