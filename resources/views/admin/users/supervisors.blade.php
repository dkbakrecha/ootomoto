@extends('admin.layouts.app')

@section('content')

@section('sectionTitle', __('messages.user_management'))
@include('admin.elements.users_all_top')
@include('admin.elements.messages')

<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.supervisor_id') }}</th>
            <th width="220px">{{ __('messages.supervisor_name') }}</th>
            <th width="200px">{{ __('messages.barbershop_name') }}</th>
            <th width="100px">{{ __('messages.phone') }}</th>
            <th width="150px">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->unique_id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->shop->name }}</td>
            <td align="center">{{ $user->phone }}</td>
            <td align="center" class="user-buttons">
                <button class="btn btn-action" data-id="{{ $user->id }}" data-toggle="modal" data-target="#editSupervisorModal"><i class="fa fa-pencil"></i></button>
                <button class="btn btn-action" data-id="{{ $user->id }}" data-toggle="modal" data-target="#viewSupervisorModal"><i class="fa fa-eye"></i></button>
                <?php /*
                  <form action="{{ route('provider.sp_login') }}" method="POST" target="_blank">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <button type="submit"  class="btn btn-action" >
                  <i class="fa fa-lock text-green" title="{{ __('messages.login_as_service_provider') }}"></i>
                  </button>
                  </form>
                 */ ?>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="editSupervisorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_supervisor') }}</h4>
            </div>
            <form action="{{ route('supervisor.update','test') }}" method="post" class="form-horizontal" id="supervisorEditForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    @include('admin.users.supervisor_edit', ['act' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>      

<!-- Modal -->
<div class="modal fade" id="viewSupervisorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.supervisor_view') }}</h4>
            </div>
            <form action="{{ route('supervisor.update','test') }}" method="post" class="form-horizontal" id="supervisorEditForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    @include('admin.users.supervisor_edit', ['act' => 'view'])
                </div>
            </form>
        </div>
    </div>
</div>      
@endsection


@section('page-js-script')
<script type="text/javascript">


    $('#editSupervisorModal, #viewSupervisorModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var _id = button.data('id');
        var modal = $(this);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/getSupervisor') }}",
            method: 'post',
            data: {id: _id},
            success: function (result) {
                modal.find('.modal-body #id').val(result.data.id);
                modal.find('.modal-body #unique_id').val(result.data.unique_id);
                modal.find('.modal-body #name').val(result.data.name);
                modal.find('.modal-body #email').val(result.data.email);
                modal.find('.modal-body #phone').val(result.data.phone);

                if (result.data.isAdmin == 1) {
                    modal.find(".modal-body input[name=isAdmin]").iCheck('check');
                }
            }
        });
    });


</script>
@endsection