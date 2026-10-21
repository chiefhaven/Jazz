@extends('layouts.app')
@section('title', __('expense.import_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('expense.import_expense')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
                    @elseif(session('notification.msg'))
                        {{ session('notification.msg') }}
                    @endif
                </div>
            </div>  
        </div>     
    @endif
    
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
                {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ExpenseController::class, 'storeExpenseImport']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]))->open() !!}
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="col-sm-8">
                            <div class="form-group">
                                {!! html()->label(__( 'product.file_to_import' ) . ':', 'name')->attributes([]) !!}
                                {!! html()->file('expense_csv')->attributes(['accept'=> '.xls, .xlsx, .csv', 'required' => 'required']); !!}
                              </div>
                        </div>
                        <div class="col-sm-4">
                        <br>
                            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.submit')</button>
                        </div>
                        </div>
                    </div>
                {!! html()->form()->close() !!}
                <br><br>
                <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ asset('files/import_expense_csv_template.csv') }}" class="tw-dw-btn tw-dw-btn-success tw-text-white" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.instructions')])
                
                <table class="table table-striped">
                    <tr>
                        <th>@lang('lang_v1.col_no')</th>
                        <th>@lang('lang_v1.col_name')</th>
                        <th>@lang('lang_v1.instruction')</th>
                    </tr>
                   
                    <tr>
                        <td>1</td>
                        <td>@lang('purchase.business_location')</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>@lang('expense.expense_category') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.category_ins') <br><small class="text-muted">(@lang('lang_v1.category_ins2'))</small></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('product.sub_category') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.sub_category_ins') <br><small class="text-muted">(@lang('lang_v1.sub_category_ins2'))</small></td>
                    </tr>

                    <tr>
                        <td>4</td>
                        <td>@lang('purchase.ref_no') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.leave_empty_to_autogenerate')</td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td>@lang('messages.date') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('expense.date_format_instruction')</td>
                    </tr>

                    <tr>
                        <td>6</td>
                        <td>@lang('expense.expense_for') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('expense.expense_for_help')</td>
                    </tr>

                    <tr>
                        <td>7</td>
                        <td>@lang('lang_v1.contact_id') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>8</td>
                        <td>@lang('purchase.attach_document') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>@lang('product.applicable_tax') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>@lang('expense.expense_note') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>11</td>
                        <td>@lang('sale.total_amount')</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>12</td>
                        <td>@lang('lang_v1.paid_amount')</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>13</td>
                        <td>@lang('lang_v1.paid_on') </td>
                        <td>@lang('expense.date_format_instruction')</td>
                    </tr>
                    
                    <tr>
                        <td>14</td>
                        <td>@lang('lang_v1.payment_method') </td>
                        <td>{{ implode(", ", $payment_types) }}</td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td>@lang('lang_v1.payment_account') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>16</td>
                        <td>@lang('sale.payment_note') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td></td>
                    </tr>

                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection