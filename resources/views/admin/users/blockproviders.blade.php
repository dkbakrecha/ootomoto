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
            <th>{{ __('messages.service_provider_id') }}</th>
            <th>{{ __('messages.service_provider') }}</th>
            <th>{{ __('messages.area') }}</th>
            <th>{{ __('messages.phone') }}</th>
            <th>{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->unique_id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->area_name }}</td>
            <td align="center">{{ $user->phone }}</td>
            <td align="center" class="user-buttons">
                <button type="button"  id="unblock_sp" class="btn btn-primary unblock_sp" data-id="{{ $user->id }}" >{{ __('messages.unblock') }}</button>            
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@section('page-js-script')
<script type="text/javascript">
    $(document).ready(function () {
        $(".unblock_sp").click(function () {
            var _provider_id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('/admin/providerUnblock') }}",
                method: 'post',
                data: {id: _provider_id},
                success: function (result) {
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            });
        });
    });
</script>
@endsection