<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ url('/') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>F</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>Flair</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a> 

        <form action="{{ route('search') }}" method="get" class="navbar-form navbar-left">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="submit" id="search-btn" class="btn search-button"><i class="fa fa-search"></i>
                    </button>
                </span>
                <input type="text" name="q" class="form-control" placeholder="{{ __('messages.search_header') }}" value="{{ (!empty($searchTerm)?$searchTerm:"") }}" autocomplete="off">
            </div>
        </form>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">


                <!-- Notifications Menu -->
                <li class="dropdown notifications-menu">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle bell" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        
                        @php
                        $currentUserID = Auth::guard('web')->id();

                        $notificationsCount = DB::table('web_notifications')
                                                    ->where('notification_for', $currentUserID)
                                                    ->where('is_read', 0)
                                                    ->get()
                                                    ->count();
                        @endphp

                        @if ($notificationsCount)
                        <span class="label label-warning">{{ $notificationsCount }}</span>
                        @endif

                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <!-- Inner Menu: contains the notifications -->
                            <ul class="menu">
                                
                                <!-- start notification -->
                                   
                                @include('web_notifications.popup')

                                <!-- end notification -->

                            </ul>
                        </li>
                        <li class="footer"><a href="{{ route('notifications') }}">{{ __('messages.view_all') }}</a></li>
                    </ul>
                </li>

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs admin-text">{{ Auth::user()->name }}</span>
                        <i class="fa fa-angle-down"></i>
                        <!-- The user image in the navbar-->


                        @php $currentUser = Auth::guard('web')->user(); @endphp
                        @if(!empty($currentUser->profile_image))
                        <img src="{{ asset("/images/profile/" . $currentUser->profile_image) }}" class="user-image" alt="User Image">
                        @else
                        <img src="{{ asset("/bower_components/admin-lte/dist/img/user2-160x160.jpg") }}" class="user-image" alt="User Image">
                        @endif




                    </a>

                    <ul class="dropdown-menu logout-admin" role="menu">
                        <li>
                            <a href="{{ route('working_hours') }}">
                                {{ __('messages.shop_working_hours') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('change_password') }}">
                                {{ __('messages.change_password') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                                {{ __('messages.logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>