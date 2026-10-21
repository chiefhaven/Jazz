@extends('layouts.app')
@section('title', __('role.edit_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'role.edit_role' )</h1>
</section>

<!-- Main content -->
<section class="content">
    @php
      $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
    @endphp
    @component('components.widget', ['class' => 'box-primary'])
        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\RoleController::class, 'update'], [$role->id]), 'method' => 'PUT', 'id' => 'role_form' ]))->open() !!}
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! html()->label(__( 'user.role_name' ) . ':*', 'name')->attributes([]) !!}
              {!! html()->text('name', str_replace( '#' . auth()->user()->business_id, '', $role->name))->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
          </div>
        </div>
        </div>
        <div class="row">
        <div class="col-md-3">
          <label>@lang( 'user.permissions' ):</label> 
        </div>
        </div>

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
                    {!! html()->checkbox('is_service_staff', $role->is_service_staff, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
                  </label>
                  @show_tooltip(__('restaurant.tooltip_service_staff'))
                  </div>
                </div>
              @endif

              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('view_export_buttons', $role_permissions), 'view_export_buttons')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_export_buttons' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('send_payment_received_notification', $role_permissions), 'send_payment_received_notification')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.payment_received_notification' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('send_payment_reminder_notification', $role_permissions), 'send_payment_reminder_notification')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.payment_reminder_notification' ) }}
                  </label>
                </div>
              </div>
          </div>
        </div>
        <hr>

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
                {!! html()->checkbox('permissions[]', in_array('user.view', $role_permissions), 'user.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('user.create', $role_permissions), 'user.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('user.update', $role_permissions), 'user.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('user.delete', $role_permissions), 'user.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('roles.view', $role_permissions), 'roles.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('roles.create', $role_permissions), 'roles.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('roles.update', $role_permissions), 'roles.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('roles.delete', $role_permissions), 'roles.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
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
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[supplier_view]', in_array('supplier.view', $role_permissions), ('supplier.view' === null ? null : (string) 'supplier.view'))->attribute('id', 'radio_option[supplier_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_supplier' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[supplier_view]', in_array('supplier.view_own', $role_permissions), ('supplier.view_own' === null ? null : (string) 'supplier.view_own'))->attribute('id', 'radio_option[supplier_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_supplier' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('supplier.create', $role_permissions), 'supplier.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('supplier.update', $role_permissions), 'supplier.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('supplier.delete', $role_permissions), 'supplier.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
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
                {!! html()->radio('radio_option[customer_view]', in_array('customer.view', $role_permissions), ('customer.view' === null ? null : (string) 'customer.view'))->attribute('id', 'radio_option[customer_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_customer' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view]', in_array('customer.view_own', $role_permissions), ('customer.view_own' === null ? null : (string) 'customer.view_own'))->attribute('id', 'radio_option[customer_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_customer' ) }}
              </label>
            </div>
            <hr>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', in_array('customer_with_no_sell_one_month', $role_permissions), ('customer_with_no_sell_one_month' === null ? null : (string) 'customer_with_no_sell_one_month'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_one_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', in_array('customer_with_no_sell_three_month', $role_permissions), ('customer_with_no_sell_three_month' === null ? null : (string) 'customer_with_no_sell_three_month'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_three_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', in_array('customer_with_no_sell_six_month', $role_permissions), ('customer_with_no_sell_six_month' === null ? null : (string) 'customer_with_no_sell_six_month'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_six_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', in_array('customer_with_no_sell_one_year', $role_permissions), ('customer_with_no_sell_one_year' === null ? null : (string) 'customer_with_no_sell_one_year'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_one_year' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[customer_view_by_sell]', in_array('customer_irrespective_of_sell', $role_permissions), ('customer_irrespective_of_sell' === null ? null : (string) 'customer_irrespective_of_sell'))->attribute('id', 'radio_option[customer_view_by_sell]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_irrespective_of_sell' ) }}
              </label>
            </div>
            <hr>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('customer.create', $role_permissions), 'customer.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('customer.update', $role_permissions), 'customer.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('customer.delete', $role_permissions), 'customer.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('product.view', $role_permissions), 'product.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('product.create', $role_permissions), 'product.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('product.update', $role_permissions), 'product.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('product.delete', $role_permissions), 'product.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('product.opening_stock', $role_permissions), 'product.opening_stock')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('view_purchase_price', $role_permissions), 'view_purchase_price')->attributes(['class' => 'input-icheck']); !!}
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
                {!! html()->radio('radio_option[purchase_view]', in_array('purchase.view', $role_permissions), ('purchase.view' === null ? null : (string) 'purchase.view'))->attribute('id', 'radio_option[purchase_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[purchase_view]', in_array('view_own_purchase', $role_permissions), ('view_own_purchase' === null ? null : (string) 'view_own_purchase'))->attribute('id', 'radio_option[purchase_view]')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.view_own_purchase') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('purchase.create', $role_permissions), 'purchase.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('purchase.update', $role_permissions), 'purchase.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('purchase.delete', $role_permissions), 'purchase.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.delete' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('purchase.payments', $role_permissions), 'purchase.payments')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.add_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('edit_purchase_payment', $role_permissions), 'edit_purchase_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('delete_purchase_payment', $role_permissions), 'delete_purchase_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.delete_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('purchase.update_status', $role_permissions), 'purchase.update_status')->attributes(['class' => 'input-icheck']); !!}
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
                {!! html()->radio('radio_option[stock_adjustment_view]', in_array('stock_adjustment.view', $role_permissions), ('stock_adjustment.view' === null ? null : (string) 'stock_adjustment.view'))->attribute('id', 'radio_option[stock_adjustment_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[stock_adjustment_view]', in_array('view_own_stock_adjustment', $role_permissions), ('view_own_stock_adjustment' === null ? null : (string) 'view_own_stock_adjustment'))->attribute('id', 'radio_option[stock_adjustment_view]')->attributes(['class' => 'input-icheck']); !!}
                {{ __('role.stock_adjustment.view_own') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('stock_adjustment.create', $role_permissions), 'stock_adjustment.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('stock_adjustment.update', $role_permissions), 'stock_adjustment.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('stock_adjustment.delete', $role_permissions), 'stock_adjustment.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @endif

        @if(in_array('stock_transfers', $enabled_modules))
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
                  {!! html()->radio('radio_option[stock_transfer_view]', in_array('stock_transfer.view', $role_permissions), ('stock_transfer.view' === null ? null : (string) 'stock_transfer.view'))->attribute('id', 'radio_option[stock_transfer_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.view' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->radio('radio_option[stock_transfer_view]', in_array('stock_transfer.view_own', $role_permissions), ('stock_transfer.view_own' === null ? null : (string) 'stock_transfer.view_own'))->attribute('id', 'radio_option[stock_transfer_view]')->attributes(['class' => 'input-icheck']); !!}
                  {{ __('role.stock_transfer.view_own') }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', in_array('stock_transfer.create', $role_permissions), 'stock_transfer.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.create' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', in_array('stock_transfer.update', $role_permissions), 'stock_transfer.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.update' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                    {!! html()->checkbox('permissions[]', in_array('stock_transfer.delete', $role_permissions), 'stock_transfer.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_transfer.delete' ) }}
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
                    {!! html()->radio('radio_option[purchase_requisition_view]', in_array('purchase_requisition.view_all', $role_permissions), ('purchase_requisition.view_all' === null ? null : (string) 'purchase_requisition.view_all'))->attribute('id', 'radio_option[purchase_requisition_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[purchase_requisition_view]', in_array('purchase_requisition.view_own', $role_permissions), ('purchase_requisition.view_own' === null ? null : (string) 'purchase_requisition.view_own'))->attribute('id', 'radio_option[purchase_requisition_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('purchase_requisition.create', $role_permissions), 'purchase_requisition.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('purchase_requisition.delete', $role_permissions), 'purchase_requisition.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_purchase_requisition' ) }}
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
                    {!! html()->radio('radio_option[purchase_order_view]', in_array('purchase_order.view_all', $role_permissions), ('purchase_order.view_all' === null ? null : (string) 'purchase_order.view_all'))->attribute('id', 'radio_option[purchase_order_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->radio('radio_option[purchase_order_view]', in_array('purchase_order.view_own', $role_permissions), ('purchase_order.view_own' === null ? null : (string) 'purchase_order.view_own'))->attribute('id', 'radio_option[purchase_order_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('purchase_order.create', $role_permissions), 'purchase_order.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('purchase_order.update', $role_permissions), 'purchase_order.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! html()->checkbox('permissions[]', in_array('purchase_order.delete', $role_permissions), 'purchase_order.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_purchase_order' ) }}
                  </label>
                </div>
              </div>

            </div>
          </div>
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
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('sell.view', $role_permissions), 'sell.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
                      </label>
                    </div>
                </div>
                @if(in_array('pos_sale', $enabled_modules))
                    <div class="col-md-12">
                        <div class="checkbox">
                          <label>
                            {!! html()->checkbox('permissions[]', in_array('sell.create', $role_permissions), 'sell.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
                          </label>
                        </div>
                    </div>
                @endif
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('sell.update', $role_permissions), 'sell.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('sell.delete', $role_permissions), 'sell.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('edit_product_price_from_pos_screen', $role_permissions), 'edit_product_price_from_pos_screen')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.edit_product_price_from_pos_screen') }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('edit_product_discount_from_pos_screen', $role_permissions), 'edit_product_discount_from_pos_screen')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('edit_pos_payment', $role_permissions), 'edit_pos_payment')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.add_edit_payment') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('print_invoice', $role_permissions), 'print_invoice')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.print_invoice') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_pay_checkout', $role_permissions), 'disable_pay_checkout')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.disable_pay_checkout') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_draft', $role_permissions), 'disable_draft')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.disable_draft') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_express_checkout', $role_permissions), 'disable_express_checkout')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.disable_express_checkout') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_discount', $role_permissions), 'disable_discount')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.disable_discount') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_suspend_sale', $role_permissions), 'disable_suspend_sale')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.disable_suspend_sale') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_credit_sale', $role_permissions), 'disable_credit_sale')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.disable_credit_sale_button') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_quotation', $role_permissions), 'disable_quotation')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.disable_quotation') }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('disable_card', $role_permissions), 'disable_card')->attributes(['class' => 'input-icheck']); !!}
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
                {!! html()->radio('radio_option[sell_view]', in_array('direct_sell.view', $role_permissions), ('direct_sell.view' === null ? null : (string) 'direct_sell.view'))->attribute('id', 'radio_option[sell_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_sale' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[sell_view]', in_array('view_own_sell_only', $role_permissions), ('view_own_sell_only' === null ? null : (string) 'view_own_sell_only'))->attribute('id', 'radio_option[sell_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('view_paid_sells_only', $role_permissions), 'view_paid_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_paid_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('view_due_sells_only', $role_permissions), 'view_due_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_due_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('view_partial_sells_only', $role_permissions), 'view_partial_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_partially_paid_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('view_overdue_sells_only', $role_permissions), 'view_overdue_sells_only')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_overdue_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('direct_sell.access', $role_permissions), 'direct_sell.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_sell' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('direct_sell.update', $role_permissions), 'direct_sell.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.update_sale' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('direct_sell.delete', $role_permissions), 'direct_sell.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_sell' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('view_commission_agent_sell', $role_permissions), 'view_commission_agent_sell')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_commission_agent_sell' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('sell.payments', $role_permissions), 'sell.payments')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.add_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('edit_sell_payment', $role_permissions), 'edit_sell_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('delete_sell_payment', $role_permissions), 'delete_sell_payment')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.delete_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('edit_product_price_from_sale_screen', $role_permissions), 'edit_product_price_from_sale_screen')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_product_price_from_sale_screen') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('edit_product_discount_from_sale_screen', $role_permissions), 'edit_product_discount_from_sale_screen')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('discount.access', $role_permissions), 'discount.access')->attributes(['class' => 'input-icheck']); !!}
                {{ __('lang_v1.discount.access') }}
              </label>
            </div>
          </div>
          @if(in_array('types_of_service', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('access_types_of_service', $role_permissions), 'access_types_of_service')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_types_of_service' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('access_sell_return', $role_permissions), 'access_sell_return')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_all_sell_return' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('access_own_sell_return', $role_permissions), 'access_own_sell_return')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_own_sell_return' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('edit_invoice_number', $role_permissions), 'edit_invoice_number')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_edit_invoice_number' ) }}
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
                  {!! html()->radio('radio_option[so_view]', in_array('so.view_all', $role_permissions), ('so.view_all' === null ? null : (string) 'so.view_all'))->attribute('id', 'radio_option[so_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->radio('radio_option[so_view]', in_array('so.view_own', $role_permissions), ('so.view_own' === null ? null : (string) 'so.view_own'))->attribute('id', 'radio_option[so_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', in_array('so.create', $role_permissions), 'so.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', in_array('so.update', $role_permissions), 'so.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('permissions[]', in_array('so.delete', $role_permissions), 'so.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_so' ) }}
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
            {!! html()->radio('radio_option[draft_view]', in_array('draft.view_all', $role_permissions), ('draft.view_all' === null ? null : (string) 'draft.view_all'))->attribute('id', 'radio_option[draft_view]')->attributes([ 'class' => 'input-icheck']) !!} {{ __( 'lang_v1.view_all_drafts' ) }}
          </label>
        </div>
      </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[draft_view]', in_array('draft.view_own', $role_permissions), ('draft.view_own' === null ? null : (string) 'draft.view_own'))->attribute('id', 'radio_option[draft_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_drafts' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('draft.update', $role_permissions), 'draft.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_draft' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('draft.delete', $role_permissions), 'draft.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_draft' ) }}
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
            {!! html()->radio('radio_option[quotation_view]', in_array('quotation.view_all', $role_permissions), ('quotation.view_all' === null ? null : (string) 'quotation.view_all'))->attribute('id', 'radio_option[quotation_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_quotations' ) }}
          </label>
        </div>
      </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[quotation_view]', in_array('quotation.view_own', $role_permissions), ('quotation.view_own' === null ? null : (string) 'quotation.view_own'))->attribute('id', 'radio_option[quotation_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_quotations' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('quotation.update', $role_permissions), 'quotation.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_quotation' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('quotation.delete', $role_permissions), 'quotation.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_quotation' ) }}
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
                        {!! html()->radio('radio_option[shipping_view]', in_array('access_shipping', $role_permissions), ('access_shipping' === null ? null : (string) 'access_shipping'))->attribute('id', 'radio_option[shipping_view]')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.access_all_shipments') }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->radio('radio_option[shipping_view]', in_array('access_own_shipping', $role_permissions), ('access_own_shipping' === null ? null : (string) 'access_own_shipping'))->attribute('id', 'radio_option[shipping_view]')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.access_own_shipping') }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('access_pending_shipments_only', $role_permissions), 'access_pending_shipments_only')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.access_pending_shipments_only') }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('access_commission_agent_shipping', $role_permissions), 'access_commission_agent_shipping')->attributes(['class' => 'input-icheck']); !!}
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
                {!! html()->checkbox('permissions[]', in_array('view_cash_register', $role_permissions), 'view_cash_register')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_cash_register' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('close_cash_register', $role_permissions), 'close_cash_register')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.close_cash_register' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('brand.view', $role_permissions), 'brand.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('brand.create', $role_permissions), 'brand.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('brand.update', $role_permissions), 'brand.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('brand.delete', $role_permissions), 'brand.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('tax_rate.view', $role_permissions), 'tax_rate.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('tax_rate.create', $role_permissions), 'tax_rate.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('tax_rate.update', $role_permissions), 'tax_rate.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('tax_rate.delete', $role_permissions), 'tax_rate.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('unit.view', $role_permissions), 'unit.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('unit.create', $role_permissions), 'unit.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('unit.update', $role_permissions), 'unit.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('unit.delete', $role_permissions), 'unit.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('category.view', $role_permissions), 'category.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('category.create', $role_permissions), 'category.create')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('category.update', $role_permissions), 'category.update')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('category.delete', $role_permissions), 'category.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('purchase_n_sell_report.view', $role_permissions), 'purchase_n_sell_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_n_sell_report.view' ) }}
              </label>
            </div>
          </div>
        @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('tax_report.view', $role_permissions), 'tax_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('contacts_report.view', $role_permissions), 'contacts_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.contacts_report.view' ) }}
              </label>
            </div>
          </div>
          @if(in_array('expenses', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('expense_report.view', $role_permissions), 'expense_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.expense_report.view' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('profit_loss_report.view', $role_permissions), 'profit_loss_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('stock_report.view', $role_permissions), 'stock_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('trending_product_report.view', $role_permissions), 'trending_product_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.trending_product_report.view' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('register_report.view', $role_permissions), 'register_report.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.register_report.view' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('sales_representative.view', $role_permissions), 'sales_representative.view')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('view_product_stock_value', $role_permissions), 'view_product_stock_value')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_product_stock_value' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('business_settings.access', $role_permissions), 'business_settings.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('barcode_settings.access', $role_permissions), 'barcode_settings.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('invoice_settings.access', $role_permissions), 'invoice_settings.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('access_printers', $role_permissions), 'access_printers')->attributes(['class' => 'input-icheck']); !!}
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
                            {!! html()->radio('radio_option[expense_view]', in_array('all_expense.access', $role_permissions), ('all_expense.access' === null ? null : (string) 'all_expense.access'))->attribute('id', 'radio_option[expense_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_all_expense' ) }}
                          </label>
                        </div>
                      </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                      <label>
                        {!! html()->radio('radio_option[expense_view]', in_array('view_own_expense', $role_permissions), ('view_own_expense' === null ? null : (string) 'view_own_expense'))->attribute('id', 'radio_option[expense_view]')->attributes(['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.view_own_expense') }}
                      </label>
                        </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('expense.add', $role_permissions), 'expense.add')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'expense.add_expense' ) }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('expense.edit', $role_permissions), 'expense.edit')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'expense.edit_expense' ) }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! html()->checkbox('permissions[]', in_array('expense.delete', $role_permissions), 'expense.delete')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_expense' ) }}
                      </label>
                    </div>
                  </div>
                </div>
            </div>
        @endif
        <hr>
        <div class="row">
        <div class="col-md-3">
          <h4>@lang( 'role.dashboard' )</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('dashboard.data', $role_permissions), 'dashboard.data')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'role.dashboard.data' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('account.access', $role_permissions), 'account.access')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('edit_account_transaction', $role_permissions), 'edit_account_transaction')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_account_transaction' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('permissions[]', in_array('delete_account_transaction', $role_permissions), 'delete_account_transaction')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_account_transaction' ) }}
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
                {!! html()->radio('radio_option[bookings_view]', in_array('crud_all_bookings', $role_permissions), ('crud_all_bookings' === null ? null : (string) 'crud_all_bookings'))->attribute('id', 'radio_option[bookings_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->radio('radio_option[bookings_view]', in_array('crud_own_bookings', $role_permissions), ('crud_own_bookings' === null ? null : (string) 'crud_own_bookings'))->attribute('id', 'radio_option[bookings_view]')->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
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
                {!! html()->checkbox('permissions[]', in_array('access_default_selling_price', $role_permissions), 'access_default_selling_price')->attributes([ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
              </label>
            </div>
          </div>
          @if(count($selling_price_groups) > 0)
          @foreach($selling_price_groups as $selling_price_group)
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('spg_permissions[]', in_array('selling_price_group.' . $selling_price_group->id, $role_permissions), 'selling_price_group.' . $selling_price_group->id)->attributes([ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
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
                    {!! html()->checkbox('permissions[]', in_array('access_tables', $role_permissions), 'access_tables')->attributes([ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_tables') }}
                  </label>
                </div>
              </div>
            </div>
          </div>
        @endif
        @include('role.partials.module_permissions')
        <div class="row">
        <div class="col-md-12 text-center">
           <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang( 'messages.update' )</button>
        </div>
        </div>

        {!! html()->form()->close() !!}
    @endcomponent
</section>
<!-- /.content -->
@endsection