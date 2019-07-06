@extends('admin.layouts.app')
@section('sectionTitle', __('messages.coupon_codes'))

@section('sectionButtons')
<span class="label label-primary">{{ __('messages.create') }}</span>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#couponAddModal">
    {{ __('messages.coupon_code') }}
</button>
@endsection

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')


<table id="datatable" class="table table-bordered flair-datatable coupon-table">
    <thead>
        <tr class="table-heading">
            <th>{{ __('messages.coupon_code') }}</th>
            <th>{{ __('messages.coupon_type') }}</th>
            <th>{{ __('messages.coupon_value') }}</th>
            <th width="180px;">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($couponcodes as $code)
        <tr>
            <td>
                <a href="#" data-id="{{ $code->id }}" data-toggle="modal" data-target="#viewCouponModal">
                    {{ $code->coupon_code }}
                </a>
            </td>
            <td>
                @if($code->coupon_type == 1)
                {{ __('messages.percentage') }}
                @else
                {{ __('messages.amount') }}
                @endif
            </td>
            <td align="center">{{ $code->coupon_amount }}</td>
            <td align="center" class="user-buttons">
                @if($code->status == 1)
                <span class="text-green">{{ __('messages.active') }}</span>

                <form action="{{ route('admin.coupon_codes.inactivate') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="offer_id" name="coupon_id" value="{{ $code->id }}">
                    <button type="submit"  class="btn btn-primary" onclick="return confirm('Are you sure you want to deactivate selected coupon?')">
                        {{ __('messages.deactivate') }}
                    </button>
                </form>


                @else
                <span class="text-red">{{ __('messages.deactive') }}</span>

                <form action="{{ route('admin.coupon_codes.activate') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="offer_id" name="coupon_id" value="{{ $code->id }}">
                    <button type="submit"  class="btn btn-primary" onclick="return confirm('Are you sure you want to activate selected coupon?')">
                        {{ __('messages.activate') }}
                    </button>
                </form>
                @endif


            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Offer ADD Modal -->
<div class="modal fade" id="couponAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.coupon_code_add') }}</h4>
            </div>
            <form action="{{ route('admin.coupon_code.store') }}" method="post" class="form-horizontal" id="couponAddForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('admin.coupon_codes.form', ['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="offerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>

<!-- Offer View Modal -->
<div class="modal fade" id="viewCouponModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.coupon_code_info') }}</h4>
            </div>
            <form action="#" method="post" class="form-horizontal" id="viewCouponForm">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    @include('admin.coupon_codes.view', ['act' => 'view'])
                </div>
            </form>
        </div>
    </div>
</div>    
@endsection

@section('page-js-script')

<script type="text/javascript">
    $(document).ready(function () {
        jQuery('#couponAddForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "{{ url('/admin/coupon_code_store') }}",
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

        $('#viewCouponModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var _id = button.data('id');

            var modal = $(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ route('admin.getCouponInfo') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    modal.find('.modal-body #coupon_code').val(result.data.coupon_code);
                    modal.find(".modal-body input[name=coupon_type][value=" + result.data.coupon_type + "]").iCheck('check');

                    modal.find('.modal-body #coupon_amount').val(result.data.coupon_amount);
                }
            });
        });
    });
</script>
@endsection