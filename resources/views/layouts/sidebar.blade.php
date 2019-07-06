<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                @php $currentUser = Auth::guard('web')->user(); @endphp
                @if(!empty($currentUser->profile_image))

                <img src="{{ asset("/images/profile/" . $currentUser->profile_image) }}" class="img-circle" alt="User Image">
                @else
                <img src="{{ asset("/bower_components/admin-lte/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image">
                @endif

            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->name }}</p>
            </div>
        </div>

        {{ $mnuTerm = $mnuDashboard = $mnuSchedule = $mnuReservation =  $mnuStaff = $mnuServices = $mnuOffers = $mnuFeedback = $mnuReview =  $mnuSetting = $mnuReports = ""  }}

        @if (request()->is('/'))
        @php $mnuDashboard = "active" @endphp
        @elseif (request()->is('schedule*'))
        @php $mnuSchedule = "active" @endphp
        @elseif (request()->is('reservation*') || request()->is('bookings*'))
        @php $mnuReservation = "active" @endphp
        @elseif (request()->is('staff*'))
        @php $mnuStaff = "active" @endphp
        @elseif (request()->is('services*'))
        @php $mnuServices = "active" @endphp
        @elseif (request()->is('offers*'))
        @php $mnuOffers = "active" @endphp
        @elseif (request()->is('feedbacks*'))
        @php $mnuFeedback = "active" @endphp
        @elseif (request()->is('reviews*'))
        @php $mnuReview = "active" @endphp
        @elseif (request()->is('reports*'))
        @php $mnuReports = "active" @endphp
        @elseif (request()->is('settings*') || request()->is('working_hours*') || request()->is('change_password*'))
        @php $mnuSetting = "active" @endphp
        @elseif (request()->is('terms*'))
        @php $mnuTerm = "active" @endphp
        @endif

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="{{  $mnuDashboard }}"><a href="{{ url('/') }}"><img src="{{ url('images') }}/dashboard.png" alt="Dashboard"> &nbsp;  <span>{{ __('messages.dashboard') }}</span></a></li>
            <li class="{{  $mnuSchedule }}"><a href="{{ route('schedule') }}"><img src="{{ url('images') }}/schedule-booking.png" alt="Schedule"> &nbsp; <span>{{ __('messages.schedule') }}</span></a></li>
            <li class="{{  $mnuReservation }}"><a href="{{ route('reservation') }}"><img src="{{ url('images') }}/user-reservation.png" alt="Reservation"> &nbsp; <span>{{ __('messages.reservation') }}</span></a></li>
            <li class="{{  $mnuStaff }}"><a href="{{ route('staff') }}"><img src="{{ url('images') }}/staff-financial.png" alt="Staff"> &nbsp; <span>{{ __('messages.staff') }}</span></a></li>
            <li class="{{  $mnuServices }}"><a href="{{ route('services') }}"><img src="{{ url('images') }}/schedule-booking.png" alt="Services"> &nbsp; <span>{{ __('messages.services') }}</span></a></li>
            <li class="{{  $mnuOffers }}"><a href="{{ route('offers') }}"><img src="{{ url('images') }}/offers-feedback.png" alt="Managing Offers" class="set-gap"> &nbsp; <span>{{ __('messages.offers_management') }}</span></a></li>
            <li class="{{  $mnuFeedback }}"><a href="{{ route('feedbacks') }}"><img src="{{ url('images') }}/offers-feedback.png" alt="Feedback" class="set-gap"> &nbsp; <span>{{ __('messages.feedback') }}</span></a></li>
            <li class="{{  $mnuReview }}"><a href="{{ route('reviews') }}"><i class="fa fa-comment"></i>&nbsp; <span>{{ __('messages.reviews') }}</span></a></li>
            @if($currentUser->user_type == 0)
            <li class="{{  $mnuReports }}"><a href="{{ route('reports') }}"><img src="{{ url('images') }}/settings-report.png" alt="Reports" class="set-gap"> &nbsp; <span>{{ __('messages.reports') }}</span></a></li>
            @endif
            <li class="{{  $mnuSetting }}"><a href="{{ route('settings') }}"><img src="{{ url('images') }}/settings-report.png" alt="Settings" class="set-gap"> &nbsp; <span>{{ __('messages.settings') }}</span></a></li>
            <li class="{{  $mnuTerm }}"><a href="{{ route('siteterms') }}"><i class="glyphicon glyphicon-print"></i> &nbsp; <span>{{ __('messages.terms') }}</span></a></li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>