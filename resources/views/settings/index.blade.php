@extends('layouts.app')

@section('content')

@section('sectionTitle', __('messages.settings'))
@include('elements.general_top')
@include('elements.messages')

<div class="panel">
    <form action="{{ route('settings.store') }}" method="post" class="form-horizontal"  enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="panel-body settings-body">
            <div class="row">
                <p class="subtitle fancy">
                    <span class="label label-info">{{ __('messages.photo_upload') }}</span>
                </p>
                <hr>
                <div class="form-group" >
                    <div class="col-sm-8 col-sm-offset-3">
                        <div class="pull-left image clearfix">
                            @php $currentUser = Auth::guard('web')->user(); @endphp
                            @if(!empty($currentUser->profile_image))

                            <img src="{{ asset("/images/profile/" . $currentUser->profile_image) }}" width="80px" class="img-circle" alt="User Image">
                            @else
                            <img src="{{ asset("/bower_components/admin-lte/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image">
                            @endif

                        </div>
                        <div class="file-text">
                            <input type="file" id="profile_image" name="profile_image" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <p class="subtitle fancy">
                    <span class="label label-info">{{ __("messages.preferred_language") }}</span>
                </p>
                <hr>
                <div class="form-group">
                    <div class="col-sm-8 col-sm-offset-3 align-radio">
                        <input type="radio" name="preferred_language" value="en" {{ ($preferredlanguage == 'en')?'checked':'' }}> {{ __('English') }}
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="preferred_language" value="ar" {{ ($preferredlanguage == 'ar')?'checked':'' }}> {{ __('عربى') }}        
                    </div>
                </div>       
            </div>
            <div class="row">
                <p class="subtitle fancy">
                    <span class="label label-info">{{ __('messages.other_settings') }}</span>
                </p>
                <hr>
                <div class="form-group">
                    <div class="col-sm-8 col-sm-offset-3 settings-links">
                        <a data-id="{{ $user->id }}" data-toggle="modal" data-target="#editProviderModal" href="">{{ __('messages.update_shop_profile') }}</a><br>
                        <a href="{{ route('working_hours') }}">{{ __('messages.shop_working_hours') }}</a><br>
                        <a href="{{ route('change_password') }}">{{ __('messages.change_password') }}</a>
                    </div>
                </div>  


            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
        </div>    
    </form>


    <!-- Modal -->
    <div class="modal fade" id="editProviderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_service_provider') }}</h4>
                </div>
                <form action="{{ route('profile.update','test') }}" method="post" class="form-horizontal" id="providerEditForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="alert alert-danger" style="display:none"></div>
                        <input type="hidden" id="id" name="id">
                        @include('users.profile',['act' => 'edit'])
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                    </div>    
                </form>
            </div>
        </div>
    </div>   

</div>

@endsection


@section('page-js-script')
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#providerEditForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            console.log($('meta[name="csrf-token"]').attr('content'));
//        var formData = new FormData(this);
            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "{{ route('profile.update','test') }}",
                method: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function (result) {
                    console.log(result);
                    if (result.errors)
                    {
                        jQuery('.alert-danger').html('');

                        jQuery.each(result.errors, function (key, value) {
                            jQuery('.alert-danger').show();
                            jQuery('.alert-danger').append('<li>' + value + '</li>');
                        });
                    } else
                    {
                        jQuery('.alert-danger').hide();
                        setCookie("success", result.success, 1);
                        location.reload();
                    }
                }
            });
        });
    });


    $('#editProviderModal #image_label, #viewProviderModal #image_label').click(function () {
        $('#editProviderModal .modal-body #previewImages, #viewProviderModal .modal-body #previewImages').show();
    });

    function closeImgBox() {
        $('#editProviderModal .modal-body #previewImages, #viewProviderModal .modal-body #previewImages').hide();
    }


//Open Edit Service Provider Model with Data
    $('#editProviderModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var _id = button.data('id')

        var modal = $(this)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/getProfile') }}",
            method: 'post',
            data: {id: _id},
            success: function (result) {
                modal.find('.modal-body #id').val(result.data.id);
                modal.find('.modal-body #unique_id').val(result.data.unique_id);
                modal.find('.modal-body #name').val(result.data.name);
                modal.find('.modal-body #area_id').val(result.data.area_id);
                modal.find('.modal-body #address').val(result.data.address);
                modal.find('.modal-body #map').val(result.data.map);
                modal.find('.modal-body #lat').val(result.data.lat);
                modal.find('.modal-body #long').val(result.data.long);
                modal.find('.modal-body #incharge_name').val(result.data.incharge_name);
                modal.find('.modal-body #email').val(result.data.email);
                modal.find('.modal-body #phone').val(result.data.phone);
                modal.find('.modal-body #owner_name').val(result.data.owner_name);
                modal.find('.modal-body #owner_phone').val(result.data.owner_phone);
                modal.find('.modal-body #crn').val(result.data.crn);
                modal.find('.modal-body #lincense').val(result.data.lincense);
                modal.find('.modal-body #comment').val(result.data.comment);
                modal.find('.modal-body .select2').val(result.service).trigger('change');
                modal.find('.modal-body #accept_payment').val(result.data.accept_payment);
                modal.find('.modal-body #commission').val(result.data.commission);
                modal.find('.modal-body #man').prop('checked', false);
                modal.find('.modal-body #women').prop('checked', false);
                modal.find('.modal-body #kid').prop('checked', false);

                if (result.data.man == 1) {
                    modal.find('.modal-body #man').iCheck('check');
                }
                if (result.data.women == 1) {
                    modal.find('.modal-body #women').iCheck('check');
                }
                if (result.data.kid == 1) {
                    modal.find('.modal-body #kid').iCheck('check');
                }

                modal.find(".modal-body input[name=auto_approve][value=" + result.data.auto_approve + "]").iCheck('check');

                modal.find('.modal-body #previewImages').html('<button type="button" class="close" id="close_imageBox" onClick="closeImgBox()"><span>×</span></button>');
                modal.find('.modal-body #previewImages').hide();
                if (result.shop_images.length == 0) {
                    modal.find('.modal-body #previewImages').append("{{ __('messages.no_images_uploaded') }}");
                    modal.find('#image_label').html("{{ __('messages.no_images_uploaded') }}");
                } else {
                    modal.find('#image_label').html("{{ __('messages.view_images') }}");
                    jQuery.each(result.shop_images, function (i, val) {
                        var source = "{!! asset('images/shop') !!}" + "/" + val.filename;
                        modal.find('.modal-body #previewImages').append('<span id="img_' + val.id + '" class="sp_image_wrap"><span class="pop"><img src="' + source + '" height="100px" width="100px"></span><i data-id="' + val.id + '" class="fa fa-trash shopimg-trash"></i></span>');
                    });
                }
            }
        });
    });

</script>
@endsection