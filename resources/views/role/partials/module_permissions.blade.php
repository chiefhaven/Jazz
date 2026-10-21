 @if(count($module_permissions) > 0)
  @php
    $module_role_permissions = [];
    if(!empty($role_permissions)) {
      $module_role_permissions = $role_permissions;
    }
  @endphp
  @foreach($module_permissions as $key => $value)
  <hr>
  <div class="row check_group">
    <div class="col-md-3">
      <h4>{{$key}}</h4>
    </div>
    <div class="col-md-9">
      @foreach($value as $module_permission)
      @php
        if(empty($role_permissions) && $module_permission['default']) {
          $module_role_permissions[] = $module_permission['value'];
        }
      @endphp
      <div class="col-md-12">
        <div class="checkbox">
          <label>
            @if(!empty($module_permission['is_radio']))
              {!! html()->radio('radio_option[' . $module_permission['radio_input_name'] . ']', in_array($module_permission['value'], $module_role_permissions), ($module_permission['value'] === null ? null : (string) $module_permission['value']))->attribute('id', 'radio_option[' . $module_permission['radio_input_name'] . ']')->attributes([ 'class' => 'input-icheck']); !!} {{ $module_permission['label'] }}
            @else
            {!! html()->checkbox('permissions[]', in_array($module_permission['value'], $module_role_permissions), $module_permission['value'])->attributes([ 'class' => 'input-icheck']); !!} {{ $module_permission['label'] }}
            @endif
          </label>
        </div>

        @if(isset($module_permission['end_group']) && $module_permission['end_group'])
        <hr>
        @endif
      </div>
      @endforeach
    </div>
  </div>
  @endforeach
@endif