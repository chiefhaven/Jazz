<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\Restaurant\ProductModifierSetController::class, 'update'], [$modifer_set->id]), 'method' => 'post', 'id' => 'table_add_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'restaurant.products_for_modifier' ): <span class="text-success">{{$modifer_set->name}}</span>
      </h4>
    </div>

    <div class="modal-body">
      <div class="row">
        
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->text('name', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.search_product_placeholder' ), 'id' => 'search_product' ]); !!}
          </div>
        </div>
        
        <div class="col-sm-12">
          <table class="table table-condensed" id="add-modifier-table">
            <thead>
              <tr>
                <th>@lang( 'restaurant.products')</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              @foreach($modifer_set->modifier_products as $product)
                <tr>
                  <td>{{$product->name}} ({{$product->sku}})</td>
                  <input type="hidden" name="products[]" value="{{$product->id}}">
                  <td><button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error remove_modifier_product"><i class="fa fa-times"></i></button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready(function(){
    $( "#search_product" ).autocomplete({
      source: function(request, response) {
        $.getJSON("/products/list-no-variation", { term: request.term }, response);
      },
      minLength: 2,
      appendTo: "#table_add_form",
      response: function(event,ui) {
        if (ui.content.length == 1)
        {
          ui.item = ui.content[0];
          if(ui.item.qty_available > 0){
            $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
            $(this).autocomplete('close');
          }
        } else if (ui.content.length == 0) {
          swal(LANG.no_products_found)
              .then((value) => {
            $('input#search_product').select();
          });
        }
      },
      select: function( event, ui ) {
        add_product_row(ui.item.product_id);
      }
  })
  .autocomplete( "instance" )._renderItem = function( ul, item ) {
    var string =  "<div>" + item.name;
    string += ' (' + item.sku + ')' + "</div>";
    return $( "<li>" ).append(string).appendTo( ul );
  };
});

function add_product_row(product_id){
  $.ajax({
    method: "GET",
    url: '/modules/product-modifiers/product-row/' + product_id,
    dataType: "html",
    success: function(result){
      $('table#add-modifier-table').append(result);
    }
  });
}
</script>