<section class="content-header">
    <div class="row">
        <h1 class="pull-left">@yield('sectionTitle')</h1>
    </div>

    <div class="pull-right">
        @yield('sectionButtons')
        
    </div>
</section>

<!-- Staff ADD Modal -->
<div class="modal fade" id="staffAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.staff_add') }}</h4>
            </div>
            <form action="{{ route('staff.store') }}" method="post" class="form-horizontal" id="staffAddForm"  enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    @include('provider.staff.form', ['act' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="submit" id="customerAddSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </div>    
            </form>
        </div>
    </div>
</div>