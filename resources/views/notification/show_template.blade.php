<!-- Fix for scroll issue in new booking -->
<style type="text/css">
  .modal {
    overflow-y:auto; 
  }
</style>
<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => $notification_template['template_for'] == 'send_ledger' ? action([\App\Http\Controllers\ContactController::class, 'sendLedger']) : action([\App\Http\Controllers\NotificationController::class, 'send']), 'method' => 'post', 'id' => 'send_notification_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.send_notification' ) - {{$template_name}}</h4>
    </div>

    <div class="modal-body">
        <div>
            <strong>@lang('lang_v1.available_tags'):</strong> 
            @include('notification_template.partials.tags', ['tags' => $tags])
        </div>
        <div class="box-group" id="accordion">
            {{-- email --}}
            <div class="panel box box-solid">
              <div class="box-header with-border">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#email_collapse" aria-expanded="true">
                   @lang('lang_v1.send_email')
                  </a>
                </h4>
              </div>
              <div id="email_collapse" class="panel-collapse collapse in" aria-expanded="true">
                <div class="box-body">
                    @if($notification_template['template_for'] == 'send_ledger')
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.ledger_format').':', 'ledger_format')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('ledger_format', ['format_1' => __('lang_v1.format_1'), 'format_2' => __('lang_v1.format_2')], $ledger_format, ['class' => 'form-control'])); !!}
                        </div>
                    @endif
                    <div class="form-group @if($notification_template['template_for'] == 'send_ledger') hide @endif">
                        <label>
                          {!! html()->checkbox('notification_type[]', true, 'email')->attributes(['class' => 'input-icheck notification_type']); !!} @lang('lang_v1.send_email')
                        </label>
                    </div>
                  <div id="email_div">
                    <div class="form-group">
                      {!! html()->label(__('lang_v1.to').':', 'to_email')->attributes([]) !!} @show_tooltip(__('lang_v1.notification_email_tooltip'))
                      {!! html()->text('to_email', $contact->email)->attributes(['class' => 'form-control' , 'placeholder' => __('lang_v1.to')]); !!}
                    </div>
                    <div class="form-group">
                      {!! html()->label(__('lang_v1.email_subject').':', 'subject')->attributes([]) !!}
                      {!! html()->text('subject', $notification_template['subject'])->attributes(['class' => 'form-control' , 'placeholder' => __('lang_v1.email_subject')]); !!}
                    </div>
                    <div class="form-group">
                      {!! html()->label('CC:', 'cc')->attributes([]) !!}
                      {!! html()->email('cc', $notification_template['cc'])->attributes(['class' => 'form-control' , 'placeholder' => 'CC']); !!}
                    </div>
                    <div class="form-group">
                      {!! html()->label('BCC:', 'bcc')->attributes([]) !!}
                      {!! html()->email('bcc', $notification_template['bcc'])->attributes(['class' => 'form-control' , 'placeholder' => 'BCC']); !!}
                    </div>
                    <div class="form-group">
                      {!! html()->label(__('lang_v1.email_body').':', 'email_body')->attributes([]) !!}
                      {!! html()->textarea('email_body', $notification_template['email_body'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.email_body'), 'rows' => 6]); !!}
                    </div>
                    @if(config('constants.enable_download_pdf') && $notification_template['template_for'] == 'new_sale')
                        <label>
                          {!! html()->checkbox('attach_pdf', false, true)->attributes(['class' => 'input-icheck notification_type']); !!}
                          @lang('lang_v1.attach_pdf_in_email')
                        </label>
                    @endif
                    @if($notification_template['template_for'] == 'send_ledger')
                      <p class="help-block">*@lang('lang_v1.ledger_attacment_help')</p>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            {{-- sms /whatsapp--}}
            @if($notification_template['template_for'] != 'send_ledger')
                <div class="panel box box-solid">
                  <div class="box-header with-border">
                    <h5 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#sms_collapse" class="collapsed" aria-expanded="false">
                        @lang('lang_v1.send_sms_whatsapp_notification')
                      </a>
                    </h5>
                  </div>
                  <div id="sms_collapse" class="panel-collapse collapse" aria-expanded="false">
                    <div class="box-body">
                        <div class="form-group @if($notification_template['template_for'] == 'send_ledger') hide @endif">
                            <label>
                              {!! html()->checkbox('notification_type[]', false, 'sms')->attributes(['class' => 'input-icheck notification_type']); !!} @lang('lang_v1.send_sms')
                            </label>
                            <label>
                              {!! html()->checkbox('notification_type[]', false, 'whatsapp')->attributes(['class' => 'input-icheck notification_type']); !!} @lang('lang_v1.send_whatsapp')
                            </label>
                          </div>
                            <div class="form-group">
                            {!! html()->label(__('lang_v1.mobile_number').':', 'mobile_number')->attributes([]) !!}
                            {!! html()->text('mobile_number', $contact->mobile)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.mobile_number')]); !!}
                            </div>
                          <div id="sms_div" class="hide">
                            <div class="form-group">
                              {!! html()->label(__('lang_v1.sms_body').':', 'sms_body')->attributes([]) !!}
                              {!! html()->textarea('sms_body', $notification_template['sms_body'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_body'), 'rows' => 6]); !!}
                            </div>
                          </div>
                          <div id="whatsapp_div" class="hide">
                              {!! html()->label(__('lang_v1.whatsapp_text').':', 'whatsapp_text')->attributes([]) !!}
                              {!! html()->textarea('whatsapp_text', $notification_template['whatsapp_text'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.whatsapp_text'), 'rows' => 6]); !!}
                          </div>
                    </div>
                  </div>
                </div>
            @endif
        </div>
        
        @if(!empty($transaction))
            {!! html()->hidden('transaction_id', $transaction->id)->attributes([]); !!}
        @endif

        @if($notification_template['template_for'] == 'send_ledger')
            {!! html()->hidden('contact_id', $contact->id)->attributes([]); !!}
            {!! html()->hidden('start_date', $start_date)->attributes([]); !!}
            {!! html()->hidden('end_date', $end_date)->attributes([]); !!}
            {!! html()->hidden('location_id', $location_id)->attributes([]); !!}
        @endif
        {!! html()->hidden('template_for', $notification_template['template_for'])->attributes([]); !!}
        <div class="modal-footer">
          <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="send_notification_btn">@lang('lang_v1.send')</button>
          <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! html()->form()->close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
// Fix for not updating textarea value on modal
  // CKEDITOR.on('instanceReady', function(){
  //    $.each( CKEDITOR.instances, function(instance) {
  //     CKEDITOR.instances[instance].on("change", function(e) {
  //         for ( instance in CKEDITOR.instances )
  //         CKEDITOR.instances[instance].updateElement();
  //     });
  //    });
  // });

  if (_.isNull(tinyMCE.activeEditor)) {
        tinymce.init({
            selector: 'textarea#email_body',
        });
    }
    
  $(document).ready(function(){
    //initialize iCheck
    $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue'
    });
  });

  $(document).on('ifChanged', '.notification_type', function(){
    var notification_type = $(this).val();
    console.log(notification_type);
    if (notification_type == 'email') {
      if ($(this).is(':checked')) {
        $('div#email_div').removeClass('hide');
      } else {
        $('div#email_div').addClass('hide');
      }
    } else if(notification_type == 'sms'){
      if ($(this).is(':checked')) {
        $('div#sms_div').removeClass('hide');
      } else {
        $('div#sms_div').addClass('hide');
      }
    } else if(notification_type == 'whatsapp'){
      if ($(this).is(':checked')) {
        $('div#whatsapp_div').removeClass('hide');
      } else {
        $('div#whatsapp_div').addClass('hide');
      }
    }
  });
  $('#send_notification_form').submit(function(e){
    e.preventDefault();
    tinyMCE.triggerSave();
    var data = $(this).serialize();
    var btn = $('#send_notification_btn');
    btn.text("@lang('lang_v1.sending')...");
    btn.attr('disabled', 'disabled');
    $.ajax({
      method: "POST",
      url: $(this).attr("action"),
      dataType: "json",
      data: $(this).serialize(),
      beforeSend: function(xhr) {
          __disable_submit_button(btn);
      },
      success: function(result){
        if(result.success == true){
          if (result.whatsapp_link) {
            window.open(result.whatsapp_link);
          }
          $('div.view_modal').modal('hide');
          toastr.success(result.msg);
        } else {
          toastr.error(result.msg);
        }
        $('#send_notification_btn').text("@lang('lang_v1.send')");
        $('#send_notification_btn').removeAttr('disabled');
      }
    });
  });
</script>