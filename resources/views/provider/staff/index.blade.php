@extends('layouts.app')


@section('sectionTitle', __('messages.staff'))

@section('sectionButtons')
<span class="label label-primary">{{ __('messages.create') }}</span>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staffAddModal">
    {{ __('messages.staff') }}
</button>
@endsection

@section('content')
@include('elements.general_top')

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-6 col-lg-6 col-lg-offset-6 pull-right service-form">
        <form action="{{ route('staff.index') }}" method="GET" role="search">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </span>
                <input type="text" class="form-control" name="q"
                       placeholder="{{ __('messages.search_name') }}" value="{{ $q }}">
            </div>
        </form>
    </div>
</div>

<div class="row">

    @if($users->count() > 0)

    @foreach ($users as $user)

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">        
        <div class="staff-users">
            <div>
                @if(!empty($user->profile_image))
                <img src="{{ asset('images/profile/' . $user->profile_image)  }}" width="100px">
                @else
                <img src="{{ asset('images/no_user.png')  }}" width="100px">
                @endif
            </div>
            <div class="staff-info pull-left">
                <div class="staff-name">{{ $user->name }}</div>
                <div class="staff-profession">{{ $user->profession }}</div>
                <div class="staff-phone">Mobile :&nbsp;{{ $user->phone }}</div>
            </div>
            <div class="pull-right staff-buttons">
                @php 
                $_loggedInUser = Auth::guard('web')->user(); 
                @endphp

                @if($_loggedInUser->user_type == 0)

                @if($user->isAdmin == 1)
                <form action="{{ route('staff.send_credentials') }}" method="POST" class="                        pull-left">
                    @csrf
                    <input type="hidden" id="offer_id" name="staff_id" value="{{ $user->id }}">
                    <button type="submit"  class="btn btn-danger pull-left" onclick="return confirm('Are you sure you want to send new user credentials?')">
                        <i class="fa fa-key"></i>
                    </button>
                </form>
                @endif

                @endif

                <button class="btn btn-primary pull-left" data-id="{{ $user->id }}" data-toggle="modal" data-target="#editStaffModal">
                    <i class="fa fa-pencil"></i>
                </button>

                <form action="{{ route('staff.destroy') }}" method="POST" class="pull-left">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="offer_id" name="staff_id" value="{{ $user->id }}">
                    <button type="submit"  class="btn btn-danger pull-left" onclick="return confirm('Are you sure you want to delete selected staff?')">
                        <i class="fa fa-times"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @else
    <div class="no-search-message">
        <h3> {{ __('messages.staff_list_empty') }} </h3>
    </div>
    @endif
</div>

<div class="site-pagination">
    {!! $users->links() !!}
</div>


<!-- Staff ADD Modal -->
<div class="modal fade" id="staffAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.staff_add') }}</h4>
            </div>
            <form action="{{ route('staff.store') }}" method="post" class="form-horizontal" id="staffAddForm"  enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('provider.staff.form', ['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="customerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.staff_edit') }}</h4>
            </div>
            <form action="{{ route('staff.update') }}" method="post" class="form-horizontal" id="staffEditForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    @include('provider.staff.form', ['act' => 'edit'])
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
    $(document).ready(function () {

        //Open Edit Staff Model with Data
        $('#editStaffModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var _id = button.data('id')

            var modal = $(this)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('getStaff') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    modal.find('.modal-body #id').val(result.data.id);
                    modal.find('.modal-body #unique_id').val(result.data.unique_id);
                    modal.find('.modal-body #name').val(result.data.name);
                    modal.find('.modal-body #email').val(result.data.email);
                    modal.find('.modal-body #phone').val(result.data.phone);
                    modal.find('.modal-body #profession').val(result.data.profession);
                    if (result.data.isAdmin == 1) {
                        modal.find('.modal-body #isAdmin').iCheck('check');
                        //modal.find('input[name=isAdmin]').prop('checked', true);
                    }
                    jQuery.each(result.barberService, function (i, val) {
                        //modal.find('input[name="' + val + '"]').prop('checked', true);
                        modal.find('input[name="' + val + '"]').iCheck('check');
                    });
                }
            });
        });

        jQuery('#staffAddForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "{{ url('/staff') }}",
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
                        //$('#staffAddModal').modal('hide');
                        //location.reload();
                    }
                }});
        });


        jQuery('#staffEditForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //        var formData = new FormData(this);
            var formData = new FormData($(this)[0]);
            jQuery.ajax({
                url: "{{ route('staff.update') }}",
                method: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function (result) {
                    //console.log(result);
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
    }
    );
</script>
@endsection