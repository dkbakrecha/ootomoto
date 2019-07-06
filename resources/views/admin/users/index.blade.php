@extends('admin.layouts.app')
@section('sectionTitle', __('messages.user_management'))

@section('content')
@include('admin.elements.users_all_top')
@include('admin.elements.messages')

<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.customer_id') }}</th>
            <th width="420px">{{ __('messages.customer_name') }}</th>
            <th width="100px">{{ __('messages.phone') }}</th>
            <th width="150px">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->unique_id }}</td>
            <td>{{ $user->name }}</td>
            <td align="center">{{ $user->phone }}</td>
            <td align="center" class="user-buttons">
                <button class="btn btn-action" data-id="{{ $user->id }}" data-toggle="modal" data-target="#editCustomerModal">
                     <i class="fa fa-pencil"></i>
                </button>

                <button class="btn btn-action" data-id="{{ $user->id }}" data-toggle="modal" data-target="#viewCustomerModal">
                    <i class="fa fa-eye"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_customer') }}</h4>
            </div>
            <form action="{{ route('users.update','test') }}" method="post" class="form-horizontal" id="customerEditForm">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    @include('admin.customer.form', ['act' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div> 

<!-- View Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.customer_info') }}</h4>
            </div>
            <form action="#" method="post" class="form-horizontal" id="customerEditForm">
                <div class="modal-body">
                    @include('admin.customer.view')
                </div>
                <div class="modal-footer">
                    <button type="button"  id="block_customer" class="btn btn-primary block_customer" data-id="" >{{ __('messages.block') }}</button>
                </div>
            </form>
        </div>
    </div>
</div> 
@endsection



@section('page-js-script')

<script type="text/javascript">
    $('#viewCustomerModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var _id = button.data('id');
        
        var modal = $(this)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/viewCustomer') }}",
            method: 'POST',
            data: {id: _id},
            success: function (result) {
                modal.find('.modal-body #id').val(result.data.id);
                modal.find('.modal-body #unique_id').val(result.data.unique_id);
                modal.find('.modal-body #name').val(result.data.name);
                modal.find('.modal-body #email').val(result.data.email);
                modal.find('.modal-body #phone').val(result.data.phone);

                modal.find('.modal-body #address').val(result.data.address);
                modal.find('.modal-body #visit').val(result.data.visit);
                modal.find('.modal-body #payments').val(result.data.payments);
                
                modal.find('.modal-body input:radio[name=gender]').filter('[value="' + result.data.gender + '"]').iCheck('check');

                modal.find('.modal-footer .block_customer').attr('data-id', result.data.id);
                if (result.data.area != null) {
                    modal.find('.modal-body #area').val(result.data.area.name);
                }



            }
        });
    });

    $(document).ready(function () {
        $("#block_customer").click(function () {
            var customer_id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('/admin/customerBlock') }}",
                method: 'post',
                data: {id: customer_id},
                success: function (result) {
                    console.log(result);
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            });
        });

        $('#customerEditForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ route('customer.update','test') }}",
                method: 'post',
                data: $(this).serialize(),
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
                        $('#editCustomerModal').modal('hide');
                        location.reload();
                    }
                }});
        });
    });
</script>
@endsection