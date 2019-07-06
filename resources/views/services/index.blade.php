@extends('layouts.app')


@section('sectionTitle', __('messages.services'))

@section('sectionButtons')
<span class="label label-primary">{{ __('messages.create') }}</span>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#serviceAddModal">
    {{ __('messages.service') }}
</button>
@endsection

@section('content')
@include('elements.general_top')
@include('elements.messages')
<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.service_id') }}</th>
            <th width="300px">{{ __('messages.service') }}</th>
            <th width="100px">{{ __('messages.category') }}</th>
            <th width="100px">{{ __('messages.price') }} (SAR)</th>
            <th width="180px">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($services as $service)
        <tr>
            <td>{{ $service->unique_id }}</td>
            <td>{{ $service->name }} <span>({{ $service->duration }} min)</span></td>
            <td>{{ $service->category->name }}</td>
            <td align="center">{{ $service->price }}</td>
            <td align="center" class="user-buttons">
                <button class="btn btn-action" data-id="{{ $service->id }}" data-toggle="modal" data-target="#editServiceModal">
                    <i class="fa fa-pencil"></i>
                </button>

                <form action="{{ route('services.delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $service->id }}">
                    <button type="submit"  class="btn btn-action" onclick="return confirm('Are you sure you want to delete selected service?')">
                        <i class="fa fa-times"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


<!-- Service ADD Modal -->
<div class="modal fade" id="serviceAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.add_service') }}</h4>
            </div>
            <form action="{{ route('service.store') }}" method="post" class="form-horizontal" id="serviceAddForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="unique_id" name="unique_id">
                    <input type="hidden" id="service_id" name="service_id">
                    <input type="hidden" id="category_id" name="category_id">
                    <input type="hidden" id="name" name="name">
                    @include('services.form', ['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="offerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_service') }}</h4>
            </div>
            <form action="{{ route('services.update','test') }}" method="post" class="form-horizontal" id="serviceEditForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="unique_id" name="unique_id">
                    <input type="hidden" id="service_id" name="service_id">
                    <input type="hidden" id="category_id" name="category_id">
                    <input type="hidden" id="name" name="name">

                    @include('services.form', ['act' => 'update'])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>      
@endsection



@section('page-js-script')
<script type="text/javascript">
    $('#editServiceModal, #serviceAddModal').on('show.bs.modal', function (event) {
        jQuery('.alert-danger').hide();
        var modal = $(this);
        modal.find('input:text, input:password, select, textarea').val('');
        modal.find('input:radio, input:checkbox').prop('checked', false);
    });

    $(document).ready(function () {

        $('#editServiceModal, #serviceAddModal').find('select').on('change', function () {

            var data = this.value;

            if (data != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url: "{{ url('getService') }}",
                    method: 'post',
                    data: {id: data},
                    success: function (result) {
                        $('#editServiceModal, #serviceAddModal').find('.modal-body #unique_id').val(result.data.unique_id);
                        $('#editServiceModal, #serviceAddModal').find('.modal-body #category_id').val(result.data.category_id);
                        $('#editServiceModal, #serviceAddModal').find('.modal-body #price').val(result.data.price);
                        $('#editServiceModal, #serviceAddModal').find('.modal-body #duration').val(result.data.duration);
                        $('#editServiceModal, #serviceAddModal').find('.modal-body #name').val(result.data.name);
                    }
                });
            }
        });

        $('#editServiceModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var _id = button.data('id');

            var modal = $(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('getShopService') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    $('#editServiceModal, #serviceAddModal').find('.modal-body #id').val(result.data.id);
                    $('#editServiceModal, #serviceAddModal').find('.modal-body #unique_id').val(result.data.unique_id);
                    $('#editServiceModal, #serviceAddModal').find('.modal-body #name').val(result.data.name);
                    $('#editServiceModal, #serviceAddModal').find('.modal-body #price').val(result.data.price);
                    $('#editServiceModal, #serviceAddModal').find('.modal-body #duration').val(result.data.duration);
                    $('#editServiceModal, #serviceAddModal').find('.modal-body #name').val(result.data.name);
                }
            });
        });


        jQuery('#serviceAddForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "{{ url('/services/store') }}",
                method: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function (result) {
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
                }});
        });

        jQuery('#serviceEditForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "{{ route('service.update','test') }}",
                method: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function (result) {
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
</script>
@endsection