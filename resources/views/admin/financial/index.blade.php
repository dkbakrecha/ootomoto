@extends('admin.layouts.app')
@section('sectionTitle', __('messages.reports'))

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')


<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box finance-box">

            <div class="info-box-title">
                <h4>{{ __('messages.refund') }}</h4>
            </div>
            <div class="info-box-content">
                <div class="info-block">
                    <span class="info-box-number">{{ $statics['total_refund'] }} SAR</span>

                    @if ($statics['refund_prev10days'] > 0)

                    @if($statics['refund_persentage'] > 0)
                    <span class="info-box-description text-green">
                        <i class="fa fa-arrow-up"></i>
                        {{ $statics['refund_persentage'] }}%
                    </span>
                    @else
                    <span class="info-box-description text-danger">
                        <i class="fa fa-arrow-down"></i>
                        {{ $statics['refund_persentage'] }}%
                    </span>
                    @endif

                    @endif
                </div>


                <div class="info-box-graph">
                    <div class="sparkbar pad" data-color="#55D8FE" data-width="9">{{ $statics['refund_days_graph'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box finance-box">
            <div class="info-box-title">
                <h4>{{ __('messages.revenue') }}</h4>
            </div>
            <div class="info-box-content">
                <div class="info-block">
                    <span class="info-box-number">{{ $statics['total_commission'] }} SAR</span>
                    
                    @if ($statics['revenue_prev10days'] > 0)

                    @if($statics['revenue_persentage'] > 0)
                    <span class="info-box-description text-green">
                        <i class="fa fa-arrow-up"></i>
                        {{ $statics['revenue_persentage'] }}%
                    </span>
                    @else
                    <span class="info-box-description text-danger">
                        <i class="fa fa-arrow-down"></i>
                        {{ $statics['revenue_persentage'] }}%
                    </span>
                    @endif

                    @endif
                </div>


                <div class="info-box-graph">
                    <div class="sparkbar pad" data-color="#A3A0FB" data-width="9">{{ $statics['revenue_days_graph'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box finance-box">
            <div class="info-box-title">
                <h4>{{ __('messages.receipt') }}</h4>
            </div>
            <div class="info-box-content">
                <div class="info-block">
                    <span class="info-box-number">{{ $statics['total_receipt'] }} SAR</span>
                </div>


                <div class="info-box-graph">
                    <div class="sparkbar pad" data-color="#5EE2A0" data-width="9">{{ $statics['receipt_days_graph'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12 graph-content">
        <div class="panel panel-default">
            <div class="row dash-graph-header">
                <div class="col-xs-7 col-sm-8 col-md-9 col-lg-10 pull-left">
                    <div class="panel-heading">
                        <h3>{{ __('messages.statistics') }} </h3>
                    </div>
                </div>

                <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2 pull-right">
                    <select name="rrChartStats" id="rrChartStats" class="form-control filter pull-right">
                        <option value="currentMonth">{{ __('messages.current_month') }}</option>
                        <option value="lastMonth">{{ __('messages.last_month') }}</option>
                        <option value="last6Months">{{ __('messages.last_6_month') }}</option>
                        <option value="last1Year">{{ __('messages.last_year') }}</option>
                    </select>
                </div>
            </div>

            <div class="panel-body">
                <div class="chart tab-pane active" id="rr-statistics-chart" style="position: relative; height: 300px;">
                </div>
                <div class="shapes">
                    <div>
                        <div class="rectangle-one">
                        </div>
                        <span>{{ __('messages.revenue') }}</span>
                    </div>
                    <div>
                        <div class="rectangle-two">
                        </div>
                        <span>{{ __('messages.refund') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nav-tabs-custom finance-tabs">
    <ul class="nav nav-tabs user-main-tabs">
        <li class="active"><a href="#receipt" data-toggle="tab" aria-expanded="true">{{ __('messages.receipt') }}</a></li>
        <li class=""><a href="#refund" data-toggle="tab" aria-expanded="false">{{ __('messages.refund') }}</a></li>
        <li class=""><a href="#revenue" data-toggle="tab" aria-expanded="false">{{ __('messages.revenue') }}</a></li>
    </ul>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="receipt">

        <table id="datatableReceipt" class="table table-bordered flair-datatable">
            <thead>
                <tr class="table-heading">
                    <th width="110px">{{ __('messages.receipt_id') }}</th>
                    <th width="300px">{{ __('messages.service_provider') }}</th>
                    <th width="120px">{{ __('messages.customer') }}</th>
                    <th width="100px">{{ __('messages.price') }} (SAR)</th>
                    <th width="80px;">{{ __('messages.details') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($receipts as $receipt)
                <tr>
                    <td>{{ $receipt->unique_id }}</td>
                    <td>{{ $receipt->shop->name }}</td>
                    <td>{{ $receipt->customer->name }}</td>
                    <td align="center">{{ $receipt->final_amount }}</td>
                    <td align="center" class="user-buttons admin-offers vertical">
                        <a href="#" data-id="{{ $receipt->id }}" data-toggle="modal" data-target="#viewReceiptModal">{{ __('messages.more') }}</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="tab-pane" id="refund">
        <table id="datatableReceipt" class="table table-bordered flair-datatable">
            <thead>
                <tr class="table-heading">
                    <th>{{ __('messages.refund_id') }}</th>
                    <th>{{ __('messages.customer') }}</th>
                    <th>{{ __('messages.amount') }} (SAR)</th>
                    <th>{{ __('messages.reason') }}</th>
                    <th width="180px;">{{ __('messages.details') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($refunds as $refund)
                <tr>
                    <td>{{ $refund->unique_id }}</td>
                    <td>{{ $refund->customer->name }}</td>
                    <td align="center">{{ $refund->amount }}</td>
                    <td>{{ $refund->reason }}</td>
                    <td align="center" class="user-buttons admin-offers vertical">
                        <a href="#" data-id="{{ $refund->id }}" data-toggle="modal" data-target="#viewRefundModal">{{ __('messages.more') }}</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="tab-pane" id="revenue">
        <table id="datatableRevenue" class="table table-bordered flair-datatable">
            <thead>
                <tr class="table-heading">
                    <th>{{ __('messages.income') }} (SAR)</th>
                    <th>{{ __('messages.service_provider') }}</th>
                    <th>{{ __('messages.shop_benefits') }} (SAR)</th>
                    <th>{{ __('messages.commission') }}</th>
                    <th width="180px;">{{ __('messages.admin_commission') }} (SAR)</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($revenue as $_revenue)
                <tr>
                    <td>{{ $_revenue->final_amount }}</td>
                    <td>{{ $_revenue->shop->name }}</td>
                    <td align="center">{{ ($_revenue->final_amount - $_revenue->admin_commission) }}</td>
                    <td align="center">
                        <?php //pr($_revenue); ?>
                        @if($_revenue->commission_type == 0)
                        {{ number_format((float)(($_revenue->admin_commission / $_revenue->final_amount) * 100), 2, '.', '')  }}%
                        @else
                        {{ $_revenue->admin_commission }} SAR
                        @endif
                    </td>
                    <td align="center">{{ $_revenue->admin_commission }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



<!-- Receipt View Modal -->
<div class="modal fade" id="viewReceiptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.receipt_info') }}</h4>
            </div>
            <form action="#" method="post" class="form-horizontal" id="receiptViewForm">
                <div class="modal-body">
                    @include('admin.financial.view_receipt')
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt View Modal -->
<div class="modal fade" id="viewRefundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.refund_info') }}</h4>
            </div>
            <form action="#" method="post" class="form-horizontal" id="refundViewForm">
                <div class="modal-body">
                    @include('admin.financial.view_refund')
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page-js-script')

<script type="text/javascript">
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var userGraph = new Morris.Area({
        element: 'rr-statistics-chart',
                resize: true,
                data: {!!$rrGraph!!}
        ,
        xkey: 'y',
                ykeys: ['revenue', 'refund'],
                labels: ['Revenue', 'Refund'],
                lineColors: ['#A3A1FB', '#54D8FF'],
        hideHover: 'auto'
    });
    // Fetch User Registraion Data based on duration set by admin
    $('#rrChartStats').change(function (e) {
        jQuery.ajax({
            url: "{{ route('admin.getRrChartStats') }}",
            method: 'post',
            dataType: "json",
            data: {
                'duration': $('#rrChartStats').val()
            },
            success: function (res) {
                userGraph.setData(res);
                userGraph.redraw();
            }
        });
    });
    $('#viewReceiptModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var _id = button.data('id');
        var modal = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: "{{ route('admin.getReceipt') }}",
            method: 'post',
            data: {
                id: _id
            },
            success: function (result) {
                modal.find('.modal-body #id').val(result.data.id);
                modal.find('.modal-body #unique_id').val(result.data.unique_id);
                modal.find('.modal-body #customer_name').val(result.data.customer.name);
                modal.find('.modal-body #service_provider').val(result.data.shop.name);
                modal.find('.modal-body #phone').val(result.data.shop.phone);
                modal.find('.modal-body #services').val(result.data.services);
                modal.find('.modal-body #date').val(result.data.date);
                modal.find('.modal-body #time').val(result.data.time);
                modal.find('.modal-body #price').val(result.data.final_amount);
                modal.find('.modal-body #payment_method').val(result.data.payment_method);
                modal.find('.modal-body #booking_id').val(result.data.booking_id);
            }
        });
    });
    $('#viewRefundModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
            var _id = button.data('id');
            var modal = $(this);
            $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            jQuery.ajax({
            url: "{{ route('admin.getRefund') }}",
                    method: 'post',
                    data: {
                    id: _id
                    },
                    success: function(result) {
                    modal.find('.modal-body #id').val(result.data.id);
                            modal.find('.modal-body #unique_id').val(result.data.unique_id);
                            modal.find('.modal-body #customer_name').val(result.data.customer.name);
                            modal.find('.modal-body #service_provider').val(result.data.shop.name);
                            modal.find('.modal-body #date').val(result.data.date);
                            modal.find('.modal-body #time').val(result.data.time);
                            modal.find('.modal-body #amount').val(result.data.amount);
                            modal.find('.modal-body #reason').val(result.data.reason);
                            modal.find('.modal-body #booking_id').val(result.data.booking_id);
                    }
            });
    });
    }
    );
</script>
@endsection