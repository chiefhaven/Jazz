@extends('layouts.app')
@section('title', __('lang_v1.add_selling_price_group_prices'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.add_selling_price_group_prices')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ProductController::class, 'saveSellingPrices']), 'method' => 'post', 'id' => 'selling_price_form' ]))->open() !!}
	{!! html()->hidden('product_id', $product->id)->attributes([]); !!}
	<div class="row">
		<div class="col-xs-12">
		<div class="box box-solid">
			<div class="box-header">
	            <h3 class="box-title">@lang('sale.product'): {{$product->name}} ({{$product->sku}})</h3>
	        </div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="table-responsive">
							<table class="table table-condensed table-bordered table-th-green text-center table-striped">
								<thead>
									<tr>
										@if($product->type == 'variable')
											<th>
												@lang('lang_v1.variation')
											</th>
										@endif
										<th>@lang('lang_v1.default_selling_price_inc_tax')</th>
										@foreach($price_groups as $price_group)
											<th>{{$price_group->name}} @show_tooltip(__('lang_v1.price_group_price_type_tooltip'))</th>
										@endforeach
									</tr>
								</thead>
								<tbody>
									@foreach($product->variations as $variation)
										<tr>
										@if($product->type == 'variable')
											<td>
												{{$variation->product_variation->name}} - {{$variation->name}} ({{$variation->sub_sku}})
											</td>
										@endif
										<td><span class="display_currency" data-currency_symbol="true">{{$variation->sell_price_inc_tax}}</span></td>
											@foreach($price_groups as $price_group)
												<td>
													{!! html()->text('group_prices[' . $price_group->id . '][' . $variation->id . '][price]', !empty($variation_prices[$variation->id][$price_group->id]['price']) ? @num_format($variation_prices[$variation->id][$price_group->id]['price']) : 0)->attributes(['class' => 'form-control input_number input-sm']); !!}
                                                    
                                                    @php
                                                        $price_type = !empty($variation_prices[$variation->id][$price_group->id]['price_type']) ? $variation_prices[$variation->id][$price_group->id]['price_type'] : 'fixed';

                                                        $name = 'group_prices[' . $price_group->id . '][' . $variation->id . '][price_type]';
                                                    @endphp

                                                    <select name={{$name}} class="form-control">
                                                        <option value="fixed" @if($price_type == 'fixed') selected @endif>@lang('lang_v1.fixed')</option>
                                                        <option value="percentage" @if($price_type == 'percentage') selected @endif>@lang('lang_v1.percentage')</option>
                                                    </select>
												</td>
											@endforeach
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			{!! html()->hidden('submit_type', 'save')->attributes(['id' => 'submit_type']); !!}
			<div class="text-center">
      			<div class="btn-group">
					<button id="opening_stock_button" @if($product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="tw-dw-btn tw-text-white tw-dw-btn-lg bg-purple submit_form">@lang('lang_v1.save_n_add_opening_stock')</button>
					<button type="submit" value="save_n_add_another" class="tw-dw-btn tw-text-white tw-dw-btn-lg bg-maroon submit_form">@lang('lang_v1.save_n_add_another')</button>
          			<button type="submit" value="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg submit_form">@lang('messages.save')</button>
          		</div>
          	</div>
		</div>
	</div>

	{!! html()->form()->close() !!}
</section>
@stop
@section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){
			$('button.submit_form').click( function(e){
				e.preventDefault();
				$('input#submit_type').val($(this).attr('value'));

				if($("form#selling_price_form").valid()) {
		            $("form#selling_price_form").submit();
		        }
			});
		});
	</script>
@endsection
