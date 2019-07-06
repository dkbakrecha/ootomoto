@extends('admin.layouts.app')

@section('content')

@section('sectionTitle', __('messages.user_management'))
@include('admin.elements.users_all_top')
@include('admin.elements.messages')

<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.service_provider_id') }}</th>
            <th width="350px">{{ __('messages.service_provider') }}</th>
            <th width="180px">{{ __('messages.area') }}</th>
            <th width="100px">{{ __('messages.phone') }}</th>
            <th width="150px">{{ __('messages.details') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->unique_id }}</td>
            <td>{{ $user->name }}</td>
            <td>
                @if(!empty($user->map))
                <a href="{{ $user->map }}" target="_BLANK">
                    {{ (!empty($user->area_name)) ? $user->area_name : "-" }}
                </a>
                @else
                {{ (!empty($user->area_name)) ? $user->area_name : "-" }}
                @endif
            </td>
            <td align="center">{{ $user->phone }}</td>
            <td align="center" class="user-buttons">
                <button class="btn btn-action" data-id="{{ $user->id }}" data-toggle="modal" data-target="#editProviderModal">
                    <i class="fa fa-pencil" title="{{ __('messages.edit_service_provider') }}"></i>
                </button>
                <button class="btn btn-action" data-id="{{ $user->id }}" data-toggle="modal" data-target="#viewProviderModal">
                    <i class="fa fa-eye" title="{{ __('messages.service_provider_info') }}"></i>
                </button>
                @if($user->status == 3)
                <form action="{{ route('provider.activate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button type="submit"  class="btn btn-action"  onclick="return confirm('Are you sure you want to activate selected service provider?')">
                        <i class="fa fa-dot-circle-o" title="{{ __('messages.title_activate_sp') }}"></i>
                    </button>
                </form>
                @endif

                @if($user->status == 1)
                <form action="{{ route('provider.sp_login') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button type="submit"  class="btn btn-action" >
                        <i class="fa fa-lock text-green" title="{{ __('messages.login_as_service_provider') }}"></i>
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="editProviderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_service_provider') }}</h4>
            </div>
            <form action="{{ route('provider.update','test') }}" method="post" class="form-horizontal" id="providerEditForm"  enctype="multipart/form-data">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    @include('admin.users.form_provider',['act' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>   


<!-- View Modal -->
<div class="modal fade" id="viewProviderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.service_provider_info') }}</h4>
            </div>
            <form action="#" method="post" class="form-horizontal" id="providerEditForm">
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    @include('admin.users.provider_info')
                </div>
                <div class="modal-footer">
                    <button type="button"  id="block_sp" class="btn btn-primary block_sp" data-id="" >{{ __('messages.block') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>   

@endsection

@section('page-js-script')
<script type="text/javascript">




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
            url: "{{ url('/admin/getProvider') }}",
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
                modal.find(".modal-body input[name=commission_type][value=" + result.data.commission_type + "]").iCheck('check');

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

//Open View Provider Model with Data
    $('#viewProviderModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var _id = button.data('id');

        var modal = $(this)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/viewProvider') }}",
            method: 'POST',
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
                modal.find('.modal-body #commission').val(result.data.commission);
                modal.find('.modal-body .select2').val(result.service).trigger('change');
                modal.find('.modal-body #accept_payment').val(result.data.accept_payment);

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
                modal.find(".modal-body input[name=commission_type][value=" + result.data.commission_type + "]").iCheck('check');

                modal.find('.modal-body #previewImages').html('<button type="button" class="close" id="close_imageBox" onClick="closeImgBox()"><span>×</span></button>');
                modal.find('.modal-body #previewImages').hide();
                if (result.shop_images.length == 0) {
                    modal.find('.modal-body #previewImages').append("{{ __('messages.no_images_uploaded') }}");
                    modal.find('#image_label').html("{{ __('messages.no_images_uploaded') }}");
                } else {
                    modal.find('#image_label').html("{{ __('messages.view_images') }}");
                    jQuery.each(result.shop_images, function (i, val) {
                        var source = "{!! asset('images/shop') !!}" + "/" + val.filename;
                        modal.find('.modal-body #previewImages').append('<span class="pop"><img src="' + source + '" height="100px" width="100px"></span>');
                    });
                }

                modal.find('.modal-footer .block_sp').attr('data-id', result.data.id);
            }
        });
    });



    $(document).ready(function () {

    });
</script>
@endsection