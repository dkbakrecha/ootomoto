@extends('admin.layouts.app')
@section('sectionTitle', __('messages.offers'))

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')


<table id="datatable" class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th>{{ __('messages.offer_id') }}</th>
            <th>{{ __('messages.description') }}</th>
            <th>{{ __('messages.service_provider') }}</th>
            <th>{{ __('messages.offer_days') }}</th>
            <th>{{ __('messages.expire_on') }}</th>
            <th width="180px;">{{ __('messages.details') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($offers as $offer)
        <tr>
            <td>{{ $offer->unique_id }}</td>
            <td>{{ $offer->description }}</td>
            <td align="center"><a href="#" data-id="{{ $offer->id }}" data-toggle="modal" data-target="#viewOfferModal">{{ $offer->shop->name }}</a></td>
            <td align="center">{{ $offer->days }}</td>
            <td align="center">{{ (!empty($offer->expire_date))?date('d-m-Y h:m:A',strtotime($offer->expire_date)):"-" }}</td>
            <td align="right" class="user-buttons admin-offers vertical">
                @if($offer->status == 3)
                <form action="{{ route('admin.offer.approve') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="offer_id" name="offer_id" value="{{ $offer->id }}">
                    <button type="submit"  class="btn btn-approve pull-left" onclick="return confirm('Are you sure you want to approve selected offer?')">
                        {{ __('messages.approve') }}
                    </button>
                </form>

                <form action="{{ route('admin.offer.reject') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="offer_id" name="offer_id" value="{{ $offer->id }}">
                    <button type="submit"  class="btn btn-primary" onclick="return confirm('Are you sure you want to reject selected offer?')">
                        {{ __('messages.reject') }}
                    </button>
                </form>
                @else

                @if($offer->status == 1)
                <span class="text-green activate detail-label">{{ __('messages.activated') }}</span>

                <form action="{{ route('admin.offer.inactive') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="offer_id" name="offer_id" value="{{ $offer->id }}">
                    <button type="submit"  class="btn btn-primary" onclick="return confirm('Are you sure you want to inactive selected offer?')">
                        {{ __('messages.inactive') }}
                    </button>
                </form>
                @endif

                @if($offer->status == 0)
                <span class="text-red detail-label">{{ __('messages.rejected') }}</span>

                <form action="{{ route('admin.offer.approve') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="offer_id" name="offer_id" value="{{ $offer->id }}">
                    <button type="submit"  class="btn btn-approve pull-left" onclick="return confirm('Are you sure you want to approve selected offer?')">
                        {{ __('messages.approve') }}
                    </button>
                </form>
                @endif

                @if($offer->status == 2)
                <span class="text-red inactivate detail-label">{{ __('messages.inactivated') }}</span>

                <form action="{{ route('admin.offer.active') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="offer_id" name="offer_id" value="{{ $offer->id }}">
                    <button type="submit"  class="btn btn-approve pull-left" onclick="return confirm('Are you sure you want to active selected offer?')">
                        {{ __('messages.active') }}
                    </button>
                </form>
                @endif

                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

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
                    @include('admin.offers.view', ['act' => 'edit'])
                </div>
            </form>
        </div>
    </div>
</div>    

@endsection

@section('page-js-script')

<script type="text/javascript">
    $(document).ready(function () {
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
                url: "{{ route('admin.getOffer') }}",
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