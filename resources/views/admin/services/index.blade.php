@extends('admin.layouts.app')

@section('content')

@section('sectionTitle', __('messages.services'))
@include('admin.elements.service_cat_top')

@include('admin.elements.messages')
<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.service_id') }}</th>
            <th width="300px">{{ __('messages.service') }}</th>
            <th width="120px">{{ __('messages.category') }}</th>
            <th width="80px">{{ __('messages.price') }}</th>
            <th width="180px">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($services as $service)
        <tr>
            <td>{{ $service->unique_id }}</td>
            <td>{{ $service->name }} <span>({{ $service->duration }} min)</span></td>
            <td align="center">{{ $service->price }}</td>
            <td align="center" class="user-buttons">
                <button class="btn btn-action" data-id="{{ $service->id }}" data-toggle="modal" data-target="#editServiceModal">
                    <i class="fa fa-pencil"></i>
                </button>

                <form action="{{ route('services.destroy',$service->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"  class="btn btn-action" onclick="return confirm('Are you sure you want to delete selected service?')">
                        <i class="fa fa-times"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_service') }}</h4>
            </div>
            <form action="{{ route('services.update','test') }}" method="post" class="form-horizontal" id="serviceEditForm">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.service_id') }}</label>

                        <div class="col-sm-9">
                            <input id="unique_id" type="text" placeholder="{{ __('messages.service_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" required disabled="">
                        </div>
                    </div>
                    @include('admin.services.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>      
@endsection