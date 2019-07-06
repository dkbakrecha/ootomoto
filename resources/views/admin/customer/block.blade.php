@extends('admin.layouts.app')
@section('sectionTitle', __('messages.block_list'))

@section('sectionNavButtons')
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs user-main-tabs">
        <li class="{{ request()->is('admin/block_providers*') ? 'active' : '' }}">
            <a href="{{ route('block_providers') }}">
                {{ __('messages.provider') }}
            </a>
        </li>
        <li class="{{ request()->is('admin/block_customers*') ? 'active' : '' }}">
            <a href="{{ route('block_customers') }}">
                {{ __('messages.customer') }}
            </a>
        </li>
    </ul>
</div>
@endsection

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')

<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.customer_id') }}</th>
            <th>{{ __('messages.customer_name') }}</th>
            <th>{{ __('messages.phone') }}</th>
            <th width="180px">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->unique_id }}</td>
            <td>{{ $user->name }}</td>
            <td align="center">{{ $user->phone }}</td>
            <td align="center" class="user-buttons">
                <button type="button"  id="unblock_customer" class="btn btn-primary unblock_customer" data-id="{{ $user->id }}" >{{ __('messages.unblock') }}</button>            
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection


@section('page-js-script')
<script type="text/javascript">
    $(document).ready(function () {
        $(".unblock_customer").click(function () {
            var customer_id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('/admin/customerUnblock') }}",
                method: 'post',
                data: {id: customer_id},
                success: function (result) {
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            });
        });
    });
</script>
@endsection