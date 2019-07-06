@extends('layouts.app')
@section('sectionTitle', __('messages.reports'))

@section('content')
<section class="content-header">
	<h1>
		{{ __('messages.reports') }}
	</h1>
</section>

<div class="row report-boxes">
	<div class="col-xs-6 col-sm-6 col-md-5 col-lg-4 r-box">
		<div class="info-box report-box">
			<div class="info-box-title">
				<img src="{{ url('images') }}/money.png" width="40" height="40" alt="">
				<span class="bb1"> {{ (!empty($totalRevenue->sumRevenue))?$totalRevenue->sumRevenue:0 }} SAR</span>
				<h4>{{ __('messages.total_income') }}</h4>
			</div>
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-5 col-lg-4 r-box">
		<div class="info-box report-box">
			<div class="info-box-title">
				<img src="{{ url('images') }}/cart.png" width="40" height="40" alt="">
				<span class="bb2">
					{{ (!empty($totalRevenue->adminCommission))?$totalRevenue->adminCommission:0 }} SAR
				</span>
				<h4>{{ __('messages.admin_commission') }}</h4>
			</div>
		</div>
	</div>
</div>

<div class="row report-tables">
	<div class="col-md-7 col-lg-8">
		<div class="panel panel-default seperator">
			<div class="row line-sep top-services">
				<div class="col-xs-7 pull-left panel-heading">
					<h3> {{ __('messages.top_services') }} </h3>
				</div>
				<div class="col-xs-5 pull-right">
					<select name="report_top_services" id="report_top_services" class="form-control filter pull-right" required="">
						<option value="currentMonth" selected>{{ __('messages.current_month') }}</option>
						<option value="lastMonth">{{ __('messages.last_month') }}</option>
						<option value="last6Months">{{ __('messages.last_6_month') }}</option>
						<option value="last1Year">{{ __('messages.last_year') }}</option>
					</select>
				</div>
			</div>

			<div class="panel-body">
				<table class="table table-bordered" id="TopServices">
					<thead>
						<tr>
							<th width="450px">{{ __('messages.service') }}</th>
							<th width="100px">{{ __('messages.sale_count') }}</th>
							<th width="80px">{{ __('messages.total') }} (SAR)</th>
						</tr>
					</thead>

				</table>
			</div>
		</div>
	</div>

	<div class="col-md-5 col-lg-4">
		<div class="panel panel-default seperator">
			<div class="row line-sep">
				<div class="col-xs-7 pull-left panel-heading">
					<h3> {{ __('messages.quick_details') }} </h3>
				</div>
				<div class="col-xs-5 pull-right" style="padding-left:0px;">
					<select name="report_quick_detail" id="report_quick_detail" class="form-control filter pull-right" required="">
						<option value="currentMonth" selected>{{ __('messages.current_month') }}</option>
						<option value="lastMonth">{{ __('messages.last_month') }}</option>
						<option value="last6Months">{{ __('messages.last_6_month') }}</option>
						<option value="last1Year">{{ __('messages.last_year') }}</option>
					</select>
				</div>
			</div>

			<div class="panel-body">
				<div class="add-user">
					<div>
						<img src="{{ url('images') }}/add-user.png" width="40" height="40" alt="user">
						<span class="user-actions">
							{{ __('messages.customers') }}
						</span>
				</div>
                                    <div class="pull-right"  id="qd_customers">{{ $quickDetails['customers'] }}</div>
					</div>
					

				<div class="add-user">
					<div>
						<img src="{{ url('images') }}/refresh.png" width="40" height="40" alt="user">
						<span class="user-actions">
							{{ __('messages.bookings') }}
						</span>
					</div>
					<div class="pull-right"id="qd_bookings">{{ $quickDetails['bookings'] }}</div>
				</div>

				<div class="add-user">
					<div>
						<img src="{{ url('images') }}/time.png" width="40" height="40" alt="user">
						<span class="user-actions">
							{{ __('messages.walkings') }}
						</span>
					</div>
					<div class="pull-right" id="qd_walkins">{{ $quickDetails['walkings'] }}</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('page-js-script')
<script type="text/javascript">
	$(document).ready(function() {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$("#report_quick_detail").change(function() {
			var _val = $(this).val();

			console.log(_val);

			jQuery.ajax({
                url: "{{ url('/get_quick_details') }}",
                method: 'post',
                data: {val: _val},
                success: function (result) {
	                    //console.log(result);
	                    $("#qd_customers").html(result.data.customers);
	                    $("#qd_bookings").html(result.data.bookings);
	                    $("#qd_walkins").html(result.data.walkings);
            	}
        	});

		});

		

		$('#TopServices').DataTable({
			ajax: "{{ route('getTopServices') }}",
			'paging': true,
			'lengthChange': false,
			'searching': true,
			'ordering': true,
			'info': false,
			'autoWidth': false,
			'aaSorting': [],
			'sDom': '<lf<"user-table"t>ip>',
			"language": {
				"paginate": {
					"previous": "<",
					"next": ">",
				}
			},
		});

		

	});

	$("#report_top_services").change(function() {
			var _val = $(this).val();

			console.log(_val);

			jQuery.ajax({
                url: "{{ url('/getTopServices') }}",
                method: 'post',
                data: {val: _val},
                success: function (result) {
       	            var _table = $("#TopServices").DataTable();
                    _table.clear();
    				_table.rows.add(result.data);
    				_table.draw()
            	}
        	});

		});
</script>
@endsection