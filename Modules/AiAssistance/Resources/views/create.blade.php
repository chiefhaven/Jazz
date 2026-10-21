@extends('layouts.app')

@section('title', __('aiassistance::lang.aiassistance'))

@section('content')

@include('aiassistance::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'aiassistance::lang.aiassistance' )</h1>
</section>

<section class="content no-print">
    <div class="row">

        <form action="{{action([\Modules\AiAssistance\Http\Controllers\AiAssistanceController::class, 'generate'], ['tool' => $tool_details['name']])}}" method="POST" id="create_form">

            <div class="col-md-8">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{$tool_details['label']}}</h3>
                        <div class="box-tools pull-right">
                            <i class="{{$tool_details['icon']}}"></i>
                        </div>
                        <p>{{$tool_details['description']}}</p>
                    </div>

                    <div class="box-body">

                        @if(in_array($tool_details['name'], ['brandproduct-descriptions', 'google-ads', 'fb-ads', 'product_review']))
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__('aiassistance::lang.brandproduct_name') . ':*', 'name')->attributes([])!!}
                                {!! html()->text('name', null)->attributes(['class' => 'form-control', 'placeholder' => __('aiassistance::lang.brandproduct_name'), 'required', 'autofocus']); !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.brandproduct_details' ) . ':*', 'description')->attributes([]) !!}
                                {!! html()->textarea('description', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.brandproduct_placeholder' ), 'rows' => 3, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>

                        @endif

                        @if(in_array($tool_details['name'], ['product_review']))
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__('aiassistance::lang.product_review_what_like') . ':*', 'features_liked')->attributes([])!!}
                                {!! html()->text('features_liked', null)->attributes(['class' => 'form-control', 'placeholder' => __('aiassistance::lang.product_review_what_like'), 'required']); !!}
                            </div>
                        </div>
                        @endif

                        @if(in_array($tool_details['name'], ['review_response']))
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.customer_review' ) . ':*', 'customer_review')->attributes([]) !!}
                                {!! html()->textarea('customer_review', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.customer_review' ), 'rows' => 3, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>
                        @endif

                        @if($tool_details['name'] == 'social_post')

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.social_post_details' ) . ':*', 'description')->attributes([]) !!}
                                {!! html()->textarea('description', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.social_post_details' ), 'rows' => 3, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>

                        @endif

                        @if($tool_details['name'] == 'copywriting')
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.product_service_description' ) . ':*', 'description')->attributes([]) !!}
                                {!! html()->textarea('description', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.product_service_description' ), 'rows' => 2, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>
                        @endif

                        @if(in_array($tool_details['name'], ['email']))
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__('aiassistance::lang.sender') . ':*', 'sender')->attributes([])!!}
                                {!! html()->text('sender', null)->attributes(['class' => 'form-control', 'placeholder' => __('aiassistance::lang.sender_placeholder'), 'required']); !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__('aiassistance::lang.recipient') . ':*', 'recipient')->attributes([])!!}
                                {!! html()->text('recipient', null)->attributes(['class' => 'form-control', 'placeholder' => __('aiassistance::lang.recipient_placeholder'), 'required']); !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.email_about' ) . ':*', 'email_about')->attributes([]) !!}
                                {!! html()->textarea('email_about', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.email_about' ), 'rows' => 2, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>
                        @endif

                        @if(in_array($tool_details['name'], ['email']))
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.tone' ) . ':', 'tone')->attributes([]) !!}
                                <select class="form-control" style="width: 50%;" name="tone">
                                    <option value="">@lang('messages.please_select')</option>
                                    @foreach($tones as $k => $v)
                                    <option value="{{$k}}">{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @endif


                        @if($tool_details['name'] == 'proposal')
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.what_biz_does' ) . ':*', 'what_biz_does')->attributes([]) !!}
                                {!! html()->textarea('what_biz_does', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.what_biz_does' ), 'rows' => 2, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.what_do_for_client' ) . ':*', 'what_do_for_client')->attributes([]) !!}
                                {!! html()->textarea('what_do_for_client', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.what_do_for_client' ), 'rows' => 2, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>

                        @endif

                        @if($tool_details['name'] == 'kb')
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'aiassistance::lang.kb_details' ) . ':*', 'kb_details')->attributes([]) !!}
                                {!! html()->textarea('kb_details', null)->attributes(['class' => 'form-control',
                                'placeholder' => __( 'aiassistance::lang.kb_details' ), 'rows' => 2, 'required', 'maxlength' => 200]); !!}
                            </div>
                        </div>

                        @endif

                    </div>

                    <div class="box-footer">

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! html()->label(__( 'business.language' ) . ':', 'language')->attributes([]) !!}
                                <select class="form-control" style="width: 50%;" name="language">
                                    @foreach($languages as $v)
                                        <option value="{{$v}}" @if($v == $default_lang) selected @endif>{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-5 pull-right">
                            <button type="reset" class="btn btn-default">@lang( 'aiassistance::lang.reset' )</button>

                            <button type="submit" class="btn btn-primary pull-right ladda-button" id="submit_btn">@lang( 'aiassistance::lang.create' )</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>


    <div class="row output_row">

    </div>
</section>

@stop

@section('javascript')
<script>
    $(document).ready(function() {
        $('#create_form').on('submit', function(e) {
            e.preventDefault();
            url = $('form#create_form').attr('action');

            var ladda = Ladda.create(document.querySelector('.ladda-button'));
		    ladda.start();

            $.ajax({
                url: url,
                method: "post",
                data: $('form#create_form').serialize(),
                dataType: "json",
                success: function(response) {
                    // $('#submit_btn').removeAttr('disabled');
                    ladda.stop();
                    if (response.success == true) {
                        var htmlObject = $(response.html);
                        $('.output_row').append(htmlObject);

                        $('html, body').animate({
                            scrollTop: $(htmlObject).offset().top - 350
                        }, 2000);
                    } else {
                        alert(response.msg);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    ladda.stop();
                    alert("something went wrong, please try again");
                    // $('#submit_btn').removeAttr('disabled');
                }
            });
        });
    })
</script>
@endsection