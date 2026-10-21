@extends('layouts.app')
@section('title', __('hms::lang.edit_room'))
@section('content')
@include('hms::layouts.nav')
<section class="content-header">
    <h1> @lang('hms::lang.edit_room')
    </h1>
    <p><i class="fa fa-info-circle"></i> @lang('hms::lang.edit_rooms_help_text') </p>
</section>
    <!-- Main content -->
    <section class="content">
        <div class="box box-solid">
            {{-- <div class="box-header">
                <h3 class="box-title">@lang('hms::lang.edit_room')</h3>
            </div> --}}
            <div class="box-body">
                {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })([
                    'url' => action([\Modules\Hms\Http\Controllers\RoomController::class, 'update'], ['room' => $room_type->id]),
                    'method' => 'put',
                    'id' => 'edit_room',
                    'files' => true
                ]))->open() !!}
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('hms::lang.type') . ':', 'type')->attributes([]) !!}
                            {!! html()->text('type', $room_type->type)->attributes([
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('hms::lang.type'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('hms::lang.max_no_of_adult') . ':', 'no_of_adult')->attributes([]) !!}
                            {!! html()->input('number', 'no_of_adult', $room_type->no_of_adult)->attributes([
                                'required',
                                'class' => 'form-control',
                                'placeholder' => __('hms::lang.no_of_adult'),
                                'min' => 0,
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('hms::lang.max_no_of_child') . ':', 'no_of_child')->attributes([]) !!}
                            {!! html()->input('number', 'no_of_child', $room_type->no_of_child)->attributes([
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('hms::lang.no_of_child'),
                            ]) !!}
                        </div>
                    </div>
                   
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label(__('hms::lang.max_occupancy') . ':', 'max_occupancy')->attributes([]) !!}
                            {!! html()->input('number', 'max_occupancy', $room_type->max_occupancy)->attributes([
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('hms::lang.max_occupancy'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-12 add_room">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light-green">
                                    <th>@lang('hms::lang.room_no')</th>
                                    <th style="width: 100px;">@lang('messages.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($room_type['rooms'] as $index => $room)
                                <tr><td><input type="hidden" name="rooms[{{ $index }}][id]" value="{{ $room->id }}">
                                    <input type="text" name="rooms[{{ $index }}][name]" class="form-control room-input" required value="{{ $room->room_number }}">
                                    <div class="invalid-feedback error" style="display:none">@lang('hms::lang.room_number_unick')</div>
                                </td><td><button type="button" class="btn btn-sm btn-danger remove"><i class="fas fa-trash-alt"></i></button></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary add-room"> @lang('messages.add') @lang('hms::lang.rooms')</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12">
                        {!! html()->label(__('hms::lang.amenities') . ':', 'amenities')->attributes([]) !!}
                    </div>
                        @foreach ($amenities as $amenity)
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                    {!! html()->checkbox('amenities[]', in_array($amenity->id, $existing_amenities), $amenity->id)->attributes([ 'class' => 'input-icheck']); !!} {{ $amenity->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    <div class="col-md-12">
                        {!! html()->label(__('hms::lang.images') . ':', 'images')->attributes([]) !!} <br>
                        @foreach($room_type->media as $media)
                            <div class="img-thumbnail">
                                <span class="badge bg-red delete-media" data-href="{{ action([\App\Http\Controllers\ProductController::class, 'deleteMedia'], ['media_id' => $media->id])}}"><i class="fas fa-times"></i></span>
                                {!! $media->thumbnail() !!}
                            </div>
                        @endforeach
                        <div class="form-group">
                            {!! html()->file('images[]')->attributes(['id' => 'upload_image', 'accept' => 'image/*',
                            'required' => false, 'multiple' => true, 'class' => 'upload-element']); !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('hms::lang.description') . ':', 'description')->attributes([]) !!}
                            {!! html()->textarea('description', $room_type->description)->attributes(['class' => 'form-control', 'rows'=> 5]); !!}
                        </div>
                    </div>
                </div>
                    <div class="col-md-12 text-center">

                        <input type="hidden" name="submit_type" id="submit_type">
                        <button type="submit" name="submit_action" value="save_and_pricing" class="btn bg-purple btn-big submit_form">@lang('hms::lang.update_and_add_price')</button>
                        <button type="submit" name="submit_action" value="save" class="btn btn-primary btn-big submit_form">@lang('messages.update')</button>                    </div>

                    {!! html()->form()->close() !!}
            </div>
        </div>

    </section>
    <!-- /.content -->
@endsection

@section('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
            var currentIndex = parseFloat("{{ count($room_type['rooms']) }}") + 1;

            $(document).on('click', '.add-room', function(e) {  
                var place_holder = "{{ __('hms::lang.room_no') }}";
                currentIndex ++;
                var newRoomField = $('<tr><td><input type="text" name="rooms['+currentIndex+'][name]" class="form-control room-input" required placeholder="'+place_holder+'" ><div class="invalid-feedback error" style="display:none">@lang('hms::lang.room_number_unick')</div></td><td><button type="button" class="btn btn-sm btn-danger remove"><i class="fas fa-trash-alt"></i></button></td></tr>');
                $('.add_room table tbody').append(newRoomField);
            });
          
            tinymce.init({
                    selector: 'textarea#description', 
                    height:250
            });
             // Remove row functionality
            $(document).on('click', '.remove', function() {
                    swal({
                    title: LANG.sure,
                    text: "Once deleted, you will not be able to recover this Room !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    }).then((confirmed) => {
                        if (confirmed) {
                            $(this).closest('tr').remove();
                        }
                    });
            });

            $("form#edit_room").validate({
                rules: {
                    "rooms[][name]": {
                        required: true
                    }
                },
            });

            $(document).on('click', '.submit_form', function(e) { 
                e.preventDefault();
                var submit_type = $(this).attr('value');
                $('#submit_type').val(submit_type);
                if ($('form#edit_room').valid()) {
                    if (!checkUniqueRoomNumbers()) {
                        return false;
                    }
                    $('form#edit_room').submit();
                }
            });

            function checkUniqueRoomNumbers() {
                var roomNumbers = {};
                var hasDuplicate = false;
                // Loop through each room input field
                $('.room-input').each(function() {
                    var roomNumber = $(this).val();
                    // Check if the room number is already added to the object
                    if (roomNumbers[roomNumber]) {
                        $(this).addClass('is-invalid');
                        $(this).siblings('.invalid-feedback').show();
                        hasDuplicate = true;
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).siblings('.invalid-feedback').hide();
                    }
                    // Add the room number to the object
                    roomNumbers[roomNumber] = true;
                });
                return !hasDuplicate;
            }
        });

      
    </script>

@endsection
