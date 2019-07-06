@extends('layouts.app')
@section('sectionTitle', __('messages.user_reviews'))

@section('content')
@include('elements.general_top')
@include('elements.messages')

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
                <p class="cname">{{ $review->customer->name }}
                    <span class="flagged">
                        @if($review->is_flagged == 1)
                        <i class="fa fa-flag"></i> {{ __('messages.flagged_by_service_provider') }}
                        @endif
                    </span>   
                </p>
                <div class="review-rating">
                    {{ show_rating($review->rating) }} - ({{ $review->shop->name }})
                </div>

                <p class="reviews">{{ $review->review_text }}</p>
            </div>
            <span class="col-xs-3 col-sm-4 col-md-3 col-lg-3 pull-right review-right">
                <p>{{ $review->created_at }}</p>

                <div class="review-approval flag-approval">
                    @if($review->status == 1)
                    <span class="text-green pull-right">{{ __('messages.approved') }}</span>
                    @else
                    {{ __('messages.pending') }}
                    @endif
                    
                    @if($review->is_flagged != 1)
                    <form action="{{ route('review.flagged') }}" method="POST" class="pull-right">
                        {{ csrf_field() }}
                        <input type="hidden" id="review_id" name="review_id" value="{{ $review->id }}">
                        <button type="submit"  class="btn btn-primary pull-left" onclick="return confirm('Are you sure you want to flagged selected review?')">
                            {{ __('messages.flag_review') }}
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