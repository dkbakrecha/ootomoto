@extends('admin.layouts.app')

@section('content')

@section('sectionTitle', __('messages.categories'))
@include('admin.elements.service_cat_top')
@include('admin.elements.messages')

<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.category_id') }}</th>
            <th>{{ __('messages.category') }}</th>
            <th>{{ __('messages.staff') }}</th>
            <th>{{ __('messages.services') }}</th>
            <th width="180px">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($categories as $category)
        <tr>
            <td>{{ $category->unique_id }}</td>
            <td>{{ $category->name }}</td>
            <td align="center">{{ $category->count }}</td>
            <td align="center">{{ $category->services->count() }}</td>
            <td align="center" class="user-buttons">

                <button data-title="{{ $category->name }}" data-cat_id="{{ $category->id }}" data-cate_id="{{ $category->cate_id }}" class="btn btn-action" data-toggle="modal" data-target="#editModal">
                    <i class="fa fa-pencil"></i>
                </button>

                <form action="{{ route('categories.destroy',$category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit"  class="btn btn-action" onclick="return confirm('Are you sure you want to delete selected category?')">
                        <i class="fa fa-times"></i>
                    </button>            
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.edit_category') }}</h4>
            </div>
            <form action="{{ route('categories.update', 'test') }}" method="post">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="cat_id" name="cat_id">
                    @include('admin.categories.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>

        </div>
    </div>
</div>      
@endsection