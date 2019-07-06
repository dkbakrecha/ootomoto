@extends('admin.layouts.app')
@section('sectionTitle', __('messages.user_reviews'))

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')

<div class="review-block">
    @foreach ($reviews as $review)
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 review-spacing">
            <div class="col-xs-9 col-sm-8 col-md-9 col-lg-9 customer-brief-info">
                <span>
                    @if(!empty($review->customer->profile_image))
                    <img src="{{ asset('images/profile/' . $review->customer->profile_image)  }}" width="40px">
                    @else
                    <img src="{{ asset('images/no_user.png')  }}" width="40px">
                    @endif
                </span>
                <div class="cname">{{ $review->customer->name }}
                    <span class="flagged">
                        @if($review->is_flagged == 1)
                        <i class="fa fa-flag"></i> {{ __('messages.flagged_by_service_provider') }}
                        @endif
                    </span>    
                </div>
                <div class="review-rating">
                    {{ show_rating($review->rating) }} - ({{ $review->shop->name }})

                </div>

                <div class="reviews">{{ $review->review_text }}</div>
            </div>

            <span class="col-xs-3 col-sm-4 col-md-3 col-lg-3 pull-right review-right">
                <p>{{ $review->created_at }}</p>

                <div class="review-approval">
                @if($review->status == 2)
                <span class="text-red pull-right">{{ __('messages.rejected') }}</span>
                <form class="pull-right" action="{{ route('admin.review.approve') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="review_id" name="review_id" value="{{ $review->id }}">
                    <button type="submit"  class="btn btn-primary pull-left" onclick="return confirm('Are you sure you want to approve selected review?')">
                        {{ __('messages.approve') }}
                    </button>
                </form>
                

                @else
                <span class="text-green pull-right">{{ __('messages.approved') }}</span>
                <form class="pull-right" action="{{ route('admin.review.reject') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="review_id" name="review_id" value="{{ $review->id }}">
                    <button type="submit"  class="btn btn-primary" onclick="return confirm('Are you sure you want to reject selected review?')">
                        {{ __('messages.reject') }}
                    </button>
                </form>
                
                @endif
				</div>

            </span>
        </div>
    </div>
    @endforeach
</div>

{!! $reviews->links() !!}

@endsection