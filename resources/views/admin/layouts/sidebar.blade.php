<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset("/images/logo.png") }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->name }}</p>
            </div>
        </div>

        {{ $mnuDashboard = $mnuService = $mnuUser = $mnuFeedback = $mnuOffer = $mnuBlock = $mnuReviews = $mnuBooking = $mnuCoupon =  $mnuFinancial = ""  }}

        @if (request()->is('admin'))
        @php $mnuDashboard = "active" @endphp
        @elseif (request()->is('admin/bookings*'))
        @php $mnuBooking = "active" @endphp
        @elseif (request()->is('admin/services*') || request()->is('admin/categories*'))
        @php $mnuService = "active" @endphp
        @elseif (request()->is('admin/users*') || request()->is('admin/supervisors*') || request()->is('admin/providers*') || request()->is('admin/area*'))
        @php $mnuUser = "active" @endphp
        @elseif (request()->is('admin/financial*'))
        @php $mnuFinancial = "active" @endphp
        @elseif (request()->is('admin/feedbacks*'))
        @php $mnuFeedback = "active" @endphp
        @elseif (request()->is('admin/block*'))
        @php $mnuBlock = "active" @endphp
        @elseif (request()->is('admin/reviews*'))
        @php $mnuReviews = "active" @endphp
        @elseif (request()->is('admin/offers*'))
        @php $mnuOffer = "active" @endphp
        @elseif (request()->is('admin/coupon_code*'))
        @php $mnuCoupon = "active" @endphp
        @endif

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="{{  $mnuDashboard }}"><a href="{{ url('/admin') }}"><img src="{{ url('images') }}/dashboard.png" alt="Dashboard"> &nbsp; <span>{{ __('messages.dashboard') }}</span></a></li>
            <li class="{{  $mnuBooking }}"><a href="{{ route('admin.bookings') }}"><img src="{{ url('images') }}/schedule-booking.png" alt="Booking"> &nbsp;  <span>{{ __('messages.booking') }}</span></a></li>
            <li class="{{  $mnuUser }}"><a href="{{ route('users.index') }}"><img src="{{ url('images') }}/user-reservation.png" alt="User Management"> &nbsp; <span>{{ __('messages.user_management') }}</span></a></li>
            <li class="{{  $mnuFinancial }}"><a href="{{ route('admin.financial') }}"><img src="{{ url('images') }}/staff-financial.png" alt="Financial"> &nbsp; <span>{{ __('messages.financials') }}</span></a></li>
            <li class="{{  $mnuBlock }}"><a href="{{ route('block_providers') }}"><img src="{{ url('images') }}/schedule-booking.png" alt="Blocked"> &nbsp; <span>{{ __('messages.blocked') }}</span></a></li>
            <li class="{{  $mnuFeedback }}"><a href="{{ route('admin.feedbacks') }}"><img src="{{ url('images') }}/offers-feedback.png" alt="Feedback" class="set-gap"> &nbsp; <span>{{ __('messages.feedback') }}</span></a></li>
            <li class="{{  $mnuReviews }}"><a href="{{ route('admin.reviews') }}"><i class="fa fa-comments"></i>&nbsp; <span>{{ __('messages.reviews') }}</span></a></li>
            <li class="{{  $mnuOffer }}"><a href="{{ route('admin.offers') }}"><img src="{{ url('images') }}/offers-feedback.png" alt="Managing Offers" class="set-gap"> &nbsp; <span>{{ __('messages.offers_management') }}</span></a></li>
            <li class="{{  $mnuCoupon }}"><a href="{{ route('admin.coupon_codes') }}"><i class="fa fa-star"></i> <span>{{ __('messages.coupon_codes') }}</span></a></li>
            <li class="{{  $mnuService }}"><a href="{{ route('services.index') }}"><i class="fa fa-star"></i> <span>{{ __('messages.services') }}</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><img src="{{ url('images') }}/settings-report.png" alt="Settings" class="set-gap"> &nbsp; <span>{{ __('messages.settings') }}</span></a></li>

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>