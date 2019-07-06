<!-- Main Header -->
<header class="main-header">
    <!-- Logo -->
    <a href="{{ url('/admin') }}" class="logo">
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
        
        <form action="{{ route('admin.search') }}" method="get" class="navbar-form navbar-left">
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
                        <i class="fa fa-bell"></i>

                        @php
                        $adminNotificationsCount = DB::table('web_notifications')
                                                    ->where('notification_for', 1)
                                                    ->where('is_read', 0)
                                                    ->get()
                                                    ->count();
                        @endphp

                        @if ($adminNotificationsCount)
                        <span class="label label-warning">
                            {{ $adminNotificationsCount }}
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <!-- Inner Menu: contains the notifications -->
                            <ul class="menu">

                                <!-- start notification -->
                                   
                                @include('admin.web_notifications.popup')

                                <!-- end notification -->
                                
                            </ul>
                        </li>
                        <li class="footer"><a href="{{ route('admin.notifications') }}">{{ __('messages.view_all') }}</a></li>
                    </ul>
                </li>

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs admin-text">{{ Auth::user()->name }}</span>
                        <i class="fa fa-angle-down"></i>
                        <img src="{{ asset("/images/logo.png") }}" class="user-image" alt="User Image" />                        
                    </a>
                    <ul class="dropdown-menu logout-admin" role="menu">
                        <li>
                            <a href="{{ route('admin.auth.logout') }}"
                               onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                                {{ __('messages.logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('admin.auth.logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>