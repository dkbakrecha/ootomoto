<section class="content-header">
    <div class="row">
        <h1 class="pull-left">@yield('sectionTitle')</h1>
        <div class="pull-right user-right-pills">
            <span class="label label-primary">{{ __('messages.create') }}</span>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#areaAddModal">
                {{ __('messages.area') }}
            </button>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#providerAddModal">
                {{ __('messages.provider') }}
            </button>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#supAddModal">
                {{ __('messages.supervisor') }}
            </button>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customerAddModal">
                {{ __('messages.customer') }}
            </button>
        </div>
    </div>
    <div class="row">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs user-main-tabs">
                <li class="{{ request()->is('admin/area*') ? 'active' : '' }}">
                    <a href="{{ route('area.index') }}">
                        {{ __('messages.area') }}
                    </a>
                </li>
                <li class="{{ request()->is('admin/providers*') ? 'active' : '' }}">
                    <a href="{{ route('users.providers') }}">
                        {{ __('messages.provider') }}
                    </a>
                </li>
                <li class="{{ request()->is('admin/supervisors*') ? 'active' : '' }}">
                    <a href="{{ route('users.supervisor') }}">
                        {{ __('messages.supervisor') }}
                    </a>
                </li>
                <li class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}">
                        {{ __('messages.customer') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</section>


<!-- ADD MODAL SECTION -->

<!-- Area ADD Modal -->
<div class="modal fade" id="areaAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.add_area') }}</h4>
            </div>
            <form action="{{ route('area.store') }}" method="post" class="form-horizontal" id="areaAddForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('admin.areas.form')
                </div>
                <div class="modal-footer">
                    <button type="submit" id="areaAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>

        </div>
    </div>
</div>

<!-- Service Provider ADD Modal -->
<div class="modal fade" id="providerAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.add_service_provider') }}</h4>
            </div>
            <form action="{{ route('provider.store') }}" method="post" class="form-horizontal" id="providerAddForm"  enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('admin.users.form_provider',['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="providerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>

<!-- Supervisor ADD Modal -->
<div class="modal fade" id="supAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.add_supervisor') }}</h4>
            </div>
            <form action="{{ route('users.supervisor_add') }}" method="post" class="form-horizontal" id="supervisorAddForm"  enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('admin.users.supervisor_add',['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="providerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>

<!-- Customer ADD Modal -->
<div class="modal fade" id="customerAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.add_customer') }}</h4>
            </div>
            <form action="{{ route('customer.store') }}" method="post" class="form-horizontal" id="customerAddForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('admin.customer.form', ['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="customerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>

        </div>
    </div>
</div>