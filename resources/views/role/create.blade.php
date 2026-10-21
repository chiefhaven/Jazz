@extends('layouts.app')
@section('title', __('role.add_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'role.add_role' )</h1>
</section>

<!-- Main content -->
<section class="content">
    @php
      $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
    @endphp
    @component('components.widget', ['class' => 'box-primary'])
        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\RoleController::class, 'store']), 'method' => 'post', 'id' => 'role_add_form' ]))->open() !!}
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! html()->label(__( 'user.role_name' ) . ':*', 'name')->attributes([]) !!}
              {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
          </div>
        </div>
        </div>
        <div class="row">
        <div class="col-md-3">
          <label>@lang( 'user.permissions' ):</label> 
        </div>
        </div>

        @if(in_array('stock_transfers', $enabled_modules))
        <div class="row check_group">
          <div class="col-md-1">
            <h4>@lang( 'lang_v1.others' )</h4>
          </div>
          <div class="col-md-2">
            <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                </label>
              </div>
          </div>
          <div class="col-md-9">
              @if(in_array('service_staff', $enabled_modules))
                <div class="col-md-12">
                  <div class="checkbox">
                    <label>
                      {!! html()->checkbox('is_service_staff', false, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
                    </label>
                    @show_tooltip(__('restaurant.tooltip_service_staff'))
                  </div>
                </div>
              @endif

              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'view_export_buttons')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_export_buttons' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'send_payment_received_notification')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.payment_received' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'send_payment_reminder_notification')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.payment_reminder' ) }}
                  </label>
                </div>
              </div>
          </div>
        </div>
        <hr>
        @endif

        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.user' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'user.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'user.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'user.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'user.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'user.roles' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'roles.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'roles.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'roles.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'roles.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.supplier' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="radio-group">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[supplier_view]', false, ('supplier.view' === null ? null : (string) 'supplier.view'))->attribute('id', 'radio_option[supplier_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_supplier' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[supplier_view]', false, ('supplier.view_own' === null ? null : (string) 'supplier.view_own'))->attribute('id', 'radio_option[supplier_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_supplier' ) }}
              </label>
            </div>
          </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'supplier.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'supplier.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'supplier.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.customer' ) @show_tooltip(__('lang_v1.customer_permissions_tooltip'))</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view]', false, ('customer.view' === null ? null : (string) 'customer.view'))->attribute('id', 'radio_option[customer_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_customer' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view]', false, ('customer.view_own' === null ? null : (string) 'customer.view_own'))->attribute('id', 'radio_option[customer_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_customer' ) }}
              </label>
            </div>
            <hr>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', false, ('customer_with_no_sell_one_month' === null ? null : (string) 'customer_with_no_sell_one_month'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_one_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', false, ('customer_with_no_sell_three_month' === null ? null : (string) 'customer_with_no_sell_three_month'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_three_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', false, ('customer_with_no_sell_six_month' === null ? null : (string) 'customer_with_no_sell_six_month'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_six_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', false, ('customer_with_no_sell_one_year' === null ? null : (string) 'customer_with_no_sell_one_year'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_one_year' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', false, ('customer_irrespective_of_sell' === null ? null : (string) 'customer_irrespective_of_sell'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_irrespective_of_sell' ) }}
              </label>
            </div>
            <hr>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'customer.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'customer.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'customer.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'business.product' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'product.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'product.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'product.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'product.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'product.opening_stock')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'view_purchase_price')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.view_purchase_price') }}
              </label>
              @show_tooltip(__('lang_v1.view_purchase_price_tooltip'))
            </div>
          </div>
        </div>
        </div>
        <hr>
        @if(in_array('purchases', $enabled_modules))
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.purchase' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[purchase_view]', false, ('purchase.view' === null ? null : (string) 'purchase.view'))->attribute('id', 'radio_option[purchase_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[purchase_view]', false, ('view_own_purchase' === null ? null : (string) 'view_own_purchase'))->attribute('id', 'radio_option[purchase_view]')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.view_own_purchase') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'purchase.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'purchase.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'purchase.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.delete' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'purchase.payments')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.add_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'edit_purchase_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'delete_purchase_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.delete_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'purchase.update_status')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.update_status') }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @endif

        @if(in_array('stock_adjustment', $enabled_modules))
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.stock_adjustment' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[stock_adjustment_view]', false, ('stock_adjustment.view' === null ? null : (string) 'stock_adjustment.view'))->attribute('id', 'radio_option[stock_adjustment_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[stock_adjustment_view]', false, ('view_own_stock_adjustment' === null ? null : (string) 'view_own_stock_adjustment'))->attribute('id', 'radio_option[stock_adjustment_view]')->attributes(['class' => 'input-icheck']); !!}
                {{ __('role.stock_adjustment.view_own') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'stock_adjustment.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'stock_adjustment.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'stock_adjustment.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @endif
        @if(in_array('stock_transfers', $enabled_modules))
          <hr>
          <div class="row check_group">
          <div class="col-md-1">
            <h4>@lang( 'role.stock_transfer' )</h4>
          </div>
          <div class="col-md-2">
            <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                </label>
              </div>
          </div>
          <div class="col-md-9">
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->radio('radio_option[stock_transfer_view]', false, ('stock_transfer.view' === null ? null : (string) 'stock_transfer.view'))->attribute('id', 'radio_option[stock_transfer_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.view' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->radio('radio_option[stock_transfer_view]', false, ('stock_transfer.view_own' === null ? null : (string) 'stock_transfer.view_own'))->attribute('id', 'radio_option[stock_transfer_view]')->attributes(['class' => 'input-icheck']); !!}
                  {{ __('role.stock_transfer.view_own') }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', false, 'stock_transfer.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.create' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', false, 'stock_transfer.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.update' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', false, 'stock_transfer.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.delete' ) }}
                </label>
              </div>
            </div>
          </div>
          </div>
          <hr>
        @endif

        @if(!empty($common_settings['enable_purchase_requisition']))
          <div class="row check_group">
            <div class="col-md-1">
              <h4>@lang( 'lang_v1.purchase_requisition' )</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                  <label>
                    <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                  </label>
                </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[purchase_requisition_view]', false, ('purchase_requisition.view_all' === null ? null : (string) 'purchase_requisition.view_all'))->attribute('id', 'radio_option[purchase_requisition_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[purchase_requisition_view]', false, ('purchase_requisition.view_own' === null ? null : (string) 'purchase_requisition.view_own'))->attribute('id', 'radio_option[purchase_requisition_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'purchase_requisition.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'purchase_requisition.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_purchase_requisition' ) }}
                  </label>
                </div>
              </div>

            </div>
          </div>
          <hr>
        @endif

        @if(!empty($common_settings['enable_purchase_order']))
          <div class="row check_group">
            <div class="col-md-1">
              <h4>@lang( 'lang_v1.purchase_order' )</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                  <label>
                    <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                  </label>
                </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[purchase_order_view]', false, ('purchase_order.view_all' === null ? null : (string) 'purchase_order.view_all'))->attribute('id', 'radio_option[purchase_order_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[purchase_order_view]', false, ('purchase_order.view_own' === null ? null : (string) 'purchase_order.view_own'))->attribute('id', 'radio_option[purchase_order_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'purchase_order.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'purchase_order.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'purchase_order.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_purchase_order' ) }}
                  </label>
                </div>
              </div>

            </div>
          </div>
          <hr>
        @endif
        <div class="row check_group">
            <div class="col-md-1">
                <h4>@lang( 'sale.pos_sale' )</h4>
            </div>
            <div class="col-md-2">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                    </label>
                </div>
            </div>
            <div class="col-md-9">
            @if(in_array('pos_sale', $enabled_modules))
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', false, 'sell.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'sell.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
                  </label>
                </div>
              </div>
                @endif
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'sell.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'sell.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'edit_product_price_from_pos_screen')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.edit_product_price_from_pos_screen') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'edit_product_discount_from_pos_screen')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'edit_pos_payment')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.add_edit_payment') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'print_invoice')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.print_invoice') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_pay_checkout')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_pay_checkout') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_draft')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_draft') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_express_checkout')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_express_checkout') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_discount')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_discount') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_suspend_sale')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_suspend_sale') }}
                  </label>
                </div>
              </div>

              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_credit_sale')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_credit_sale_button') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_quotation')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_quotation') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'disable_card')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_card') }}
                  </label>
                </div>
              </div>
            </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'sale.sale' ) @show_tooltip(__('lang_v1.sell_permissions_tooltip'))</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          @if(in_array('add_sale', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[sell_view]', false, ('direct_sell.view' === null ? null : (string) 'direct_sell.view'))->attribute('id', 'radio_option[sell_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_sale' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[sell_view]', false, ('view_own_sell_only' === null ? null : (string) 'view_own_sell_only'))->attribute('id', 'radio_option[sell_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'view_paid_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_paid_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'view_due_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_due_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'view_partial_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_partially_paid_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'view_overdue_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_overdue_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'direct_sell.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_sell' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'direct_sell.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.update_sale' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'direct_sell.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_sell' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'view_commission_agent_sell')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_commission_agent_sell' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'sell.payments')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.add_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'edit_sell_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'delete_sell_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.delete_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'edit_product_price_from_sale_screen')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_product_price_from_sale_screen') }}
              </label>
            </div>
          </div>
          
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'edit_product_discount_from_sale_screen')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'discount.access')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.discount.access') }}
              </label>
            </div>
          </div>
          @if(in_array('types_of_service', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'access_types_of_service')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_types_of_service' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'access_sell_return')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_all_sell_return' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'access_own_sell_return')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_own_sell_return' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'edit_invoice_number')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_edit_invoice_number' ) }}
              </label>
            </div>
          </div>

        </div>
        </div>
        <hr>
      @if(!empty($pos_settings['enable_sales_order']))
        <div class="row check_group">
          <div class="col-md-1">
            <h4>@lang( 'lang_v1.sales_order' )</h4>
          </div>
          <div class="col-md-2">
            <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                </label>
              </div>
          </div>
          <div class="col-md-9">
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->radio('radio_option[so_view]', false, ('so.view_all' === null ? null : (string) 'so.view_all'))->attribute('id', 'radio_option[so_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->radio('radio_option[so_view]', false, ('so.view_own' === null ? null : (string) 'so.view_own'))->attribute('id', 'radio_option[so_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', false, 'so.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', false, 'so.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', false, 'so.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_so' ) }}
                </label>
              </div>
            </div>

          </div>
        </div>
        <hr>
      @endif
      <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'sale.draft' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! html()->radio('radio_option[draft_view]', false, ('draft.view_all' === null ? null : (string) 'draft.view_all'))->attribute('id', 'radio_option[draft_view]')->attributes([ 'class' => 'input-icheck']) !!} {{ __( 'lang_v1.view_all_drafts' ) }}
          </label>
        </div>
      </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[draft_view]', false, ('draft.view_own' === null ? null : (string) 'draft.view_own'))->attribute('id', 'radio_option[draft_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_drafts' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'draft.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_draft' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'draft.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_draft' ) }}
              </label>
            </div>
          </div>

        </div>
      </div>
      <hr>
      <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'lang_v1.quotation' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! html()->radio('radio_option[quotation_view]', false, ('quotation.view_all' === null ? null : (string) 'quotation.view_all'))->attribute('id', 'radio_option[quotation_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_quotations' ) }}
          </label>
        </div>
      </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[quotation_view]', false, ('quotation.view_own' === null ? null : (string) 'quotation.view_own'))->attribute('id', 'radio_option[quotation_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_quotations' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'quotation.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_quotation' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'quotation.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_quotation' ) }}
              </label>
            </div>
          </div>

        </div>
      </div>
      <hr>
      <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'lang_v1.shipments' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[shipping_view]', false, ('access_shipping' === null ? null : (string) 'access_shipping'))->attribute('id', 'radio_option[shipping_view]')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_all_shipments') }}
                  </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[shipping_view]', false, ('access_own_shipping' === null ? null : (string) 'access_own_shipping'))->attribute('id', 'radio_option[shipping_view]')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_own_shipping') }}
                  </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'access_pending_shipments_only')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_pending_shipments_only') }}
                  </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'access_commission_agent_shipping')->attributes(['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_commission_agent_shipping') }}
                  </label>
                </div>
            </div>
        </div>
    </div>
    <hr>
        <div class="row check_group">
      <div class="col-md-1">
        <h4>@lang( 'cash_register.cash_register' )</h4>
      </div>
      <div class="col-md-2">
        <div class="checkbox">
            <label>
              <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
            </label>
          </div>
      </div>
      <div class="col-md-9">
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('permissions[]', false, 'view_cash_register')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_cash_register' ) }}
            </label>
          </div>
        </div>
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('permissions[]', false, 'close_cash_register')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.close_cash_register' ) }}
            </label>
          </div>
        </div>
      </div>
      </div>
        <hr>
        
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.brand' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'brand.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'brand.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'brand.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'brand.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.tax_rate' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'tax_rate.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'tax_rate.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'tax_rate.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'tax_rate.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.unit' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'unit.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'unit.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'unit.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'unit.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'category.category' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'category.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'category.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'category.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'category.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.report' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
            @if(in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules))
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'purchase_n_sell_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_n_sell_report.view' ) }}
                  </label>
                </div>
              </div>
            @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'tax_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'contacts_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.contacts_report.view' ) }}
              </label>
            </div>
          </div>
          @if(in_array('expenses', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'expense_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.expense_report.view' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'profit_loss_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'stock_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'trending_product_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.trending_product_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'register_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.register_report.view' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'sales_representative.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'view_product_stock_value')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_product_stock_value' ) }}
              </label>
            </div>
          </div> 

        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.settings' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'business_settings.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'barcode_settings.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'invoice_settings.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'access_printers')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.access_printers') }}
              </label>
            </div>
          </div>
        </div>
        </div>
        @if(in_array('expenses', $enabled_modules))
            <hr>
            <div class="row check_group">
                <div class="col-md-1">
                  <h4>@lang( 'lang_v1.expense' )</h4>
                </div>
                <div class="col-md-2">
                  <div class="checkbox">
                      <label>
                        <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                      </label>
                    </div>
                </div>
                <div class="col-md-9">
                  <div class="col-md-12">
                        <div class="checkbox">
                          <label>
                            {!! html()->radio('radio_option[expense_view]', false, ('all_expense.access' === null ? null : (string) 'all_expense.access'))->attribute('id', 'radio_option[expense_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_all_expense' ) }}
                          </label>
                        </div>
                      </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                      <label>
                        {!! html()->radio('radio_option[expense_view]', false, ('view_own_expense' === null ? null : (string) 'view_own_expense'))->attribute('id', 'radio_option[expense_view]')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.view_own_expense') }}
                      </label>
                        </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', false, 'expense.add')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'expense.add_expense' ) }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', false, 'expense.edit')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'expense.edit_expense' ) }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', false, 'expense.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_expense' ) }}
                      </label>
                    </div>
                  </div>
                </div>
            </div>
        @endif
        <hr>
        <div class="row check_group">
        <div class="col-md-3">
          <h4>@lang( 'role.dashboard' ) @show_tooltip(__('tooltip.dashboard_permission'))</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', true, 'dashboard.data')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.dashboard.data' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-3">
          <h4>@lang( 'account.account' )</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'account.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'edit_account_transaction')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_account_transaction' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', false, 'delete_account_transaction')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_account_transaction' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @if(in_array('booking', $enabled_modules))
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'restaurant.bookings' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[bookings_view]', false, ('crud_all_bookings' === null ? null : (string) 'crud_all_bookings'))->attribute('id', 'radio_option[bookings_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[bookings_view]', false, ('crud_own_bookings' === null ? null : (string) 'crud_own_bookings'))->attribute('id', 'radio_option[bookings_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @endif
        <div class="row">
        <div class="col-md-3">
          <h4>@lang( 'lang_v1.access_selling_price_groups' )</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', true, 'access_default_selling_price')->attributes([ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
              </label>
            </div>
          </div>
          @if(count($selling_price_groups) > 0)
          @foreach($selling_price_groups as $selling_price_group)
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('spg_permissions[]', false, 'selling_price_group.' . $selling_price_group->id)->attributes([ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
              </label>
            </div>
          </div>
          @endforeach
          @endif
        </div>
        </div>
        @if(in_array('tables', $enabled_modules))
          <div class="row">
            <div class="col-md-3">
              <h4>@lang( 'restaurant.restaurant' )</h4>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', false, 'access_tables')->attributes([ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_tables') }}
                  </label>
                </div>
              </div>
            </div>
          </div>
        @endif
        
        @include('role.partials.module_permissions')
        <div class="row">
        <div class="col-md-12 text-center">
           <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang( 'messages.save' )</button>
        </div>
        </div>

        {!! html()->form()->close() !!}
    @endcomponent
</section>
<!-- /.content -->
@endsection