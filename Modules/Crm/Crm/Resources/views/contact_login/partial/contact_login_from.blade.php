@php
	$index = isset($index) ? (int) $index : '';
@endphp
<div class="row">
	<div class="col-md-12">
		<hr>
		<button type="button" class="btn btn-primary more_btn" data-target="#add_contact_person_div_{{$index}}">@lang('crm::lang.add_contact_person', ['number' => $index + 1]) <i class="fa fa-chevron-down"></i></button>
	</div>
</div>
<br>
<div class="row @if($index !== 0)hide @endif" id="add_contact_person_div_{{$index}}">
	<div class="col-md-2">
        <div class="form-group">
         	{!! html()->label(__( 'business.prefix' ) . ':', "surname$index")->attributes([]) !!}
         	{!! html()->text($index === '' ? 'surname' : "contact_persons[$index][surname]", null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ), 'id' => "surname$index" ]); !!}
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
			{!! html()->label(__( 'business.first_name' ) . ':*', "first_name$index")->attributes([]) !!}
			{!! html()->text($index === '' ? 'first_name' : "contact_persons[$index][first_name]", null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ), 'id' => "first_name$index" ]); !!}
        </div>
	</div>
	<div class="col-md-5">
		<div class="form-group">
			{!! html()->label(__( 'business.last_name' ) . ':', "last_name$index")->attributes([]) !!}
			{!! html()->text($index === '' ? 'last_name' : "contact_persons[$index][last_name]", null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.last_name' ), 'id' => "last_name$index" ]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__( 'business.email' ) . ':', "email$index")->attributes([]) !!}
			{!! html()->text($index ==='' ? 'email' : "contact_persons[$index][email]", null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.email' ), 'id' => "email$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! html()->label(__( 'lang_v1.mobile_number' ) . ':', "contact_number$index")->attributes([]) !!}
		    {!! html()->text($index === '' ? 'contact_no' : "contact_persons[$index][contact_no]", !empty($user->contact_no) ? $user->contact_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.mobile_number'), 'id'=>"contact_number$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! html()->label(__( 'business.alternate_number' ) . ':', "alt_number$index")->attributes([]) !!}
		    {!! html()->text($index === '' ? 'alt_number' : "contact_persons[$index][alt_number]", !empty($user->alt_number) ? $user->alt_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.alternate_number'), 'id'=>"alt_number$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! html()->label(__( 'lang_v1.family_contact_number' ) . ':', "family_number$index")->attributes([]) !!}
		    {!! html()->text($index === '' ? 'family_number' : "contact_persons[$index][family_number]", !empty($user->family_number) ? $user->family_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.family_contact_number'), 'id'=>"family_number$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! html()->label(__( 'lang_v1.department' ) . ':', "department$index")->attributes([]) !!}
		    {!! html()->text($index === '' ? 'crm_department' : "contact_persons[$index][crm_department]", !empty($user->crm_department) ? $user->crm_department : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.department'), 'id'=>"department$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! html()->label(__( 'lang_v1.designation' ) . ':', "designation$index")->attributes([]) !!}
		    {!! html()->text($index === '' ? 'crm_designation' : "contact_persons[$index][crm_designation]", !empty($user->crm_designation) ? $user->crm_designation : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.designation'), 'id'=>"designation$index" ]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-6">
        <div class="form-group">
          {!! html()->label(__( 'lang_v1.cmmsn_percent' ) . ':', "cmmsn_percent$index")->attributes([]) !!}
            {!! html()->text("contact_persons[$index][cmmsn_percent]", !empty($user->cmmsn_percent) ? $user->cmmsn_percent : null)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ), 'id' => "cmmsn_percent$index" ]); !!}
        </div>
 	</div>
	<div class="col-md-6">
		<div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox($index === '' ? 'allow_login' : "contact_persons[$index][allow_login]", false, 1)->attributes([ 'class' => 'input-icheck allow_login', "data-loginDiv" => "loginDiv$index"]); !!} {{ __( 'lang_v1.allow_login' ) }}
              </label>
            </div>
        </div>
	</div>
</div>
<div class="row hide" id="loginDiv{{$index}}">
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__( 'business.username' ) . ':*', "username$index")->attributes([]) !!}
			{!! html()->text($index ==='' ? 'username' : "contact_persons[$index][username]", null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.username' ), 'required', 'id'=>"username$index"]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__( 'business.password' ) . ':*', "password$index")->attributes([]) !!}
			{!! html()->password($index === '' ? 'password' : "contact_persons[$index][password]")->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ), 'id'=>"password$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__( 'business.confirm_password' ) . ':*', "confirm_password$index")->attributes([]) !!}
			{!! html()->password($index === '' ? 'confirm_password' : "contact_persons[$index][confirm_password]")->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ), 'id' => "confirm_password$index", 'data-rule-equalTo' => "#password$index" ]); !!}
		</div>
	</div>
  	<div class="clearfix"></div>
	<div class="col-md-4">
		<div class="form-group">
			<label>
				{!! html()->checkbox($index === '' ? 'is_active' : "contact_persons[$index][is_active]", true, 'active')->attributes(['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
			</label>
			@show_tooltip(__('lang_v1.tooltip_enable_user_active'))
		</div>
	</div>
</div>