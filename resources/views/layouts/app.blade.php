<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('sectionTitle') - {{ config('app.name') }}</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="{{ asset("/bower_components/bootstrap/dist/css/bootstrap.min.css") }}">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset("/bower_components/font-awesome/css/font-awesome.min.css") }}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset("/bower_components/Ionicons/css/ionicons.min.css") }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css") }}">
        <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
              page. However, you can choose any other skin. Make sure you
              apply the skin class to the body tag so the changes take effect. -->
        <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/dist/css/skins/skin-red.min.css") }}">

        <link rel="stylesheet" href="{{ asset("bower_components/morris.js/morris.css") }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset("/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css") }}">

        <link rel="stylesheet" href="{{ asset("/bower_components/select2/dist/css/select2.min.css") }}">

        <!-- fullCalendar -->
        <link rel="stylesheet" href="{{ asset("/bower_components/fullcalendar/dist/fullcalendar.min.css") }}">
        <link rel="stylesheet" href="{{ asset("/bower_components/fullcalendar/dist/fullcalendar.print.min.css") }}" media="print">

        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/plugins/iCheck/square/blue.css") }}">

        <!-- Bootstrap time Picker -->
        <link rel="stylesheet" href="{{ asset("/bower_components/bootstrap-timepicker/css/bootstrap-timepicker.css") }}">


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}">
        <!-- Google Font -->
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset("/css/site_ui.css") }}">
    </head>
    <!--
    BODY TAG OPTIONS:
    =================
    Apply one or more of the following classes to get the
    desired effect
    |---------------------------------------------------------|
    | SKINS         | skin-blue                               |
    |               | skin-black                              |
    |               | skin-purple                             |
    |               | skin-yellow                             |
    |               | skin-red                                |
    |               | skin-green                              |
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
    -->
    <body class="hold-transition skin-red sidebar-mini">
        <div class="wrapper">
            <!-- Header -->
            @include('layouts.header')

            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Main content -->
                <section class="content container-fluid">

                    <!-- Your Page Content Here -->
                    @yield('content')

                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Footer -->
            <?php /* @include('layouts.footer') */ ?>

            <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div>
        <!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->

        <!-- jQuery 3 -->
        <script src="{{ asset("/bower_components/jquery/dist/jquery.min.js") }}"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="{{ asset("/bower_components/bootstrap/dist/js/bootstrap.min.js") }}"></script>
        <!-- Select 2 -->
        <script src="{{ asset("/bower_components/select2/dist/js/select2.full.min.js") }}"></script>

        <script src="{{ asset("/bower_components/jquery-slimscroll/jquery.slimscroll.min.js") }}"></script>

        <!-- Morris.js charts -->
        <script src="{{ asset("/bower_components/raphael/raphael.min.js") }}"></script>
        <script src="{{ asset("/bower_components/morris.js/morris.min.js") }}"></script>

        <script src="{{ asset("/js/bootstrap-notify/dist/bootstrap-notify.min.js") }}"></script>

        <!-- fullCalendar -->
        <script src="{{ asset("/bower_components/moment/moment.js") }}"></script>
        <script src="{{ asset("/bower_components/fullcalendar/dist/fullcalendar.min.js") }}"></script>

        <!-- bootstrap-timepicker -->
        <script src="{{ asset("bower_components/bootstrap-timepicker/js/bootstrap-timepicker.min.js") }}"></script>

        <!-- iCheck -->
        <script src="{{ asset("/bower_components/admin-lte/plugins/iCheck/icheck.min.js") }}"></script>

        <!-- DataTables -->
        <script src="{{ asset("/bower_components/datatables.net/js/jquery.dataTables.min.js") }}"></script>
        <script src="{{ asset("/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js") }}"></script>


        <!-- AdminLTE App -->
        <script src="{{ asset("/bower_components/admin-lte/dist/js/adminlte.min.js") }}"></script>


        <script type="text/javascript">



$('#editStaffModal, #addStaffModal').on('show.bs.modal', function (event) {
    jQuery('.alert-danger').hide();
    var modal = $(this);
    modal.find('input:text, input:password, select, textarea').val('');
    modal.find('input:radio, input:checkbox').prop('checked', false);
});

jQuery(document).ready(function () {

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
    });

    $('.select2').select2();

    $('.flair-datatable').DataTable({
        'paging': true,
        'lengthChange': false,
        'searching': true,
        'ordering': true,
        'info': false,
        'autoWidth': false,
        'aaSorting': [],
        'sDom': '<lf<"user-table"t>ip>',
        'columnDefs': [
            {orderable: false, targets: -1}
        ],
        "language": {
            "paginate": {
                "previous": "<",
                "next": ">",
            },
            "search": "{{ (Auth::user()->preferred_language == 'en')?'Search:':'بحث:' }}",
            "emptyTable": "{{ (Auth::user()->preferred_language == 'en')?'No records found':'لا توجد سجلات' }}",
            "zeroRecords":"{{ (Auth::user()->preferred_language == 'en')?'No matching records found':'لم يتم العثور على سجلات مطابقة' }}",
        },
    });

    $('body').on('click', 'span.pop', function () {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
    });

    $('body').on('click', 'i.shopimg-trash', function (e) {
        var r = confirm("Are you sure, you want to delete selected shop image!");
        if (r == true) {
            var _id = $(this).data('id');

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('/removeShopImage') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    $('body').find("#img_" + _id).remove();
                }});
        }
    });

    var _msg = getCookie("success");
    if (_msg != "") {
        $.notify({
            // options
            message: _msg
        }, {
            // settings
            type: 'success'
        });
        setCookie("success", "", 1);
    }
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

        </script>

        <!-- Optionally, you can add Slimscroll and FastClick plugins.
             Both of these plugins are recommended to enhance the
             user experience. -->

        @yield('page-js-script')

    </body>
</html>