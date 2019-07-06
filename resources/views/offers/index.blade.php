@extends('layouts.app')


@section('sectionTitle', __('messages.offers'))

@section('sectionButtons')
<span class="label label-primary">{{ __('messages.create') }}</span>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#offerAddModal">
    {{ __('messages.offer') }}
</button>
@endsection

@section('content')
@include('elements.general_top')
@include('elements.messages')

<table id="datatable" class="table table-bordered flair-datatable offers">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.offer_id') }}</th>
            <th>{{ __('messages.description') }}</th>
            <th>{{ __('messages.title') }}</th>
            <th width="120px">{{ __('messages.offer_days') }}</th>
            <th width="180px">{{ __('messages.details') }}</th>
        </tr>
    </thead>
    @foreach ($offers as $offer)
    <tr>
        <td>{{ $offer->unique_id }}</td>
        <td>{{ $offer->description }}</td>
        <td><a href="#" data-id="{{ $offer->id }}" data-toggle="modal" data-target="#viewOfferModal">{{ $offer->title }}</a></td>
        <td align="center">{{ $offer->days }}</td>
        <td align="center" class="user-buttons admin-offers">
            @if($offer->status == 3)
            <div style="display:inline-block;">
                <button class="btn btn-action pull-left" data-id="{{ $offer->id }}" data-toggle="modal" data-target="#editOfferModal">
                    <i class="fa fa-pencil"></i>
                </button>
            </div>
            <form action="{{ route('offer.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" id="offer_id" name="offer_id" value="{{ $offer->id }}">
                <button type="submit"  class="btn btn-action" onclick="return confirm('Are you sure you want to delete selected offer?')">
                    <i class="fa fa-times"></i>
                </button>
            </form>
            @else

            @if($offer->status == 1)
            <span class="text-green activate detail-label">{{ __('messages.activated') }}</span>
            @else
            <span class="text-red detail-label">{{ __('messages.rejected') }}</span>
            @endif

            @endif
        </td>
    </tr>
    @endforeach
</table>

{!! $offers->links() !!}

<!-- Offer ADD Modal -->
<div class="modal fade" id="offerAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.offer_add') }}</h4>
            </div>
            <form action="{{ route('offer.store') }}" method="post" class="form-horizontal" id="offerAddForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('offers.form', ['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="offerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>


<!-- Offer Edit Modal -->
<div class="modal fade" id="editOfferModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.offer_edit') }}</h4>
            </div>
            <form action="{{ route('offer.update','test') }}" method="post" class="form-horizontal" id="offerEditForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.offer_id') }}</label>

                        <div class="col-sm-9">
                            <input id="unique_id" type="text" placeholder="{{ __('messages.offer_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" required disabled="">
                        </div>
                    </div>
                    @include('offers.form', ['act' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div> 


<!-- Offer View Modal -->
<div class="modal fade" id="viewOfferModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.offer_view') }}</h4>
            </div>
            <form action="#" method="post" class="form-horizontal" id="offerEditForm">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.offer_id') }}</label>

                        <div class="col-sm-9">
                            <input id="unique_id" type="text" placeholder="{{ __('messages.offer_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" required disabled="">
                        </div>
                    </div>
                    @include('offers.view', ['act' => 'edit'])
                </div>
            </form>
        </div>
    </div>
</div>    
@endsection

@section('page-js-script')
<script type="text/javascript">
    $('#editOfferModal, #offerAddModal').on('show.bs.modal', function (event) {
        jQuery('.alert-danger').hide();
        var modal = $(this);
        modal.find('input:text, input:password, select, textarea').val('');
        modal.find('input:radio, input:checkbox').prop('checked', false);
    });

    $(document).ready(function () {

        $('#editOfferModal, #offerAddModal').find('.select2').on('select2:select', function (e) {

            var data = $(this).select2("val");
            if (data != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url: "{{ url('getOfferPrice') }}",
                    method: 'post',
                    data: {ids: data},
                    success: function (result) {
                        $('#editOfferModal, #offerAddModal').find('.modal-body #price').val(result);
                    }
                });
            }

        });



        $('#editOfferModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var _id = button.data('id');

            var modal = $(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('getOffer') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    modal.find('.modal-body #id').val(result.data.id);
                    modal.find('.modal-body #unique_id').val(result.data.unique_id);
                    modal.find('.modal-body #title').val(result.data.title);
                    modal.find('.modal-body #description').val(result.data.description);
                    modal.find('.modal-body #days').val(result.data.days);
                    modal.find('.modal-body .select2').val(result.data.services).trigger('change');
                    modal.find('.modal-body #price').val(result.data.price);

                    modal.find('.modal-body #previewImage').html("");

                    if (result.data.offer_image == null) {
                        modal.find('.modal-body #previewImage').html('Offer Image not upload');
                    } else {
                        var source = "{!! asset('images/offer') !!}" + "/" + result.data.offer_image;
                        modal.find('.modal-body #previewImage').append('<img src="' + source + '" height="100px" width="100px">');
                    }
                }
            });
        });

        jQuery('#offerAddForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "{{ url('/offers/store') }}",
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

        jQuery('#offerEditForm').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "{{ route('offer.update','test') }}",
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

        $('#viewOfferModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var _id = button.data('id');

            var modal = $(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ route('getOffer') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    modal.find('.modal-body #id').val(result.data.id);
                    modal.find('.modal-body #unique_id').val(result.data.unique_id);
                    modal.find('.modal-body #title').val(result.data.title);
                    modal.find('.modal-body #description').val(result.data.description);
                    modal.find('.modal-body #days').val(result.data.days);
                    modal.find('.modal-body #price').val(result.data.price);

                    var _serivces = "";
                    var _i = 0;
                    $(result.services).each(function (key, val) {
                        if (_i > 0) {
                            _serivces = _serivces + ", ";
                        }
                        _serivces = _serivces + val.name;
                        _i++;
                    });
                    //console.log(_serivces);
                    modal.find('.modal-body #services').val(_serivces);

                    modal.find('.modal-body #previewImage').html("");

                    if (result.data.offer_image == null) {
                        modal.find('.modal-body #previewImage').html('Offer Image not upload');
                    } else {
                        var source = "{!! asset('images/offer') !!}" + "/" + result.data.offer_image;
                        modal.find('.modal-body #previewImage').append('<img src="' + source + '" height="100px" width="100px">');
                    }

                }
            });
        });
    });
</script>
@endsection