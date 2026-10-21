@extends('layouts.app')
@section('title', __('expense.edit_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('expense.edit_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
  {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ExpenseController::class, 'update'], [$expense->id]), 'method' => 'PUT', 'id' => 'add_expense_form', 'files' => true ]))->open() !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('purchase.business_location').':*', 'location_id')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $expense->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'])); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('expense.expense_category').':', 'expense_category_id')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('expense_category_id', $expense_categories, $expense->expense_category_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
          </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('product.sub_category')  . ':', 'expense_sub_category_id')->attributes([]) !!}
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('expense_sub_category_id', $sub_categories, $expense->expense_sub_category_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'])); !!}
            </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('purchase.ref_no').':*', 'ref_no')->attributes([]) !!}
            {!! html()->text('ref_no', $expense->ref_no)->attributes(['class' => 'form-control', 'required']); !!}
            <p class="help-block">
                @lang('lang_v1.leave_empty_to_autogenerate')
            </p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('messages.date') . ':*', 'transaction_date')->attributes([]) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! html()->text('transaction_date', @format_datetime($expense->transaction_date))->attributes(['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']); !!}
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('expense.expense_for').':', 'expense_for')->attributes([]) !!} @show_tooltip(__('tooltip.expense_for'))
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('expense_for', $users, $expense->expense_for, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('lang_v1.expense_for_contact').':', 'contact_id')->attributes([]) !!} 
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('contact_id', $contacts, $expense->contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('purchase.attach_document') . ':', 'document')->attributes([]) !!}
                {!! html()->file('document')->attributes(['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                @includeIf('components.document_help_text')</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! html()->label(__('product.applicable_tax') . ':', 'tax_id')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('tax_id', $taxes['tax_rates'], $expense->tax_id, ['class' => 'form-control'])); !!}

            <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
            value="0">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('sale.total_amount') . ':*', 'final_total')->attributes([]) !!}
            {!! html()->text('final_total', @num_format($expense->final_total))->attributes(['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('expense.expense_note') . ':', 'additional_notes')->attributes([]) !!}
                {!! html()->textarea('additional_notes', $expense->additional_notes)->attributes(['class' => 'form-control', 'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>
  </div> <!--box end-->
  @include('expense.recur_expense_form_part')
  <div class="col-sm-12 text-center">
    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg">@lang('messages.update')</button>
  </div>

{!! html()->form()->close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
  __page_leave_confirmation('#add_expense_form');
</script>
@endsection