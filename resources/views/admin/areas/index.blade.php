@extends('admin.layouts.app')

@section('content')

@section('sectionTitle', __('messages.user_management'))
@include('admin.elements.users_all_top')
@include('admin.elements.messages')


<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.area_id') }}</th>
            <th width="580px">{{ __('messages.area') }}</th>
            <th width="150px">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($areas as $area)
        <tr>
            <td>{{ $area->unique_id }}</td>
            <td>{{ $area->name }}</td>
            <td align="center" class="user-buttons">

                <button class="btn btn-action" data-id="{{ $area->id }}" data-toggle="modal" data-target="#areaEditModal">
                    <i class="fa fa-pencil"></i>
                </button>

                <button class="btn btn-action" data-id="{{ $area->id }}" data-toggle="modal" data-target="#areaViewModal">
                    <i class="fa fa-eye"></i>
                </button>

                <form action="{{ route('area.destroy',$area->id) }}" method="POST">



                    @csrf
                    @method('DELETE')

                    <button type="submit"  class="btn btn-action"  onclick="return confirm('Are you sure you want to delete selected area?')">
                        <i class="fa fa-times"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Area Edit Modal -->
<div class="modal fade" id="areaEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_area') }}</h4>
            </div>
            <form action="{{ route('area.update','test') }}" method="post" class="form-horizontal" id="areaEditForm">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.area_id') }}</label>

                        <div class="col-sm-9">
                            <input id="unique_id" type="text" placeholder="{{ __('messages.area_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" required disabled="">
                        </div>
                    </div>
                    @include('admin.areas.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>

<!-- Area View Modal -->
<div class="modal fade" id="areaViewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.area_info') }}</h4>
            </div>
            <form action="{{ route('area.update','test') }}" method="post" class="form-horizontal" id="areaEditForm">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.area_id') }}</label>

                        <div class="col-sm-9">
                            <input id="unique_id" type="text" placeholder="{{ __('messages.area_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" required disabled="">
                        </div>
                    </div>
                    @include('admin.areas.view')
                </div>

            </form>
        </div>
    </div>
</div>

@endsection