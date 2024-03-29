<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ config('app.name') }} | Create Service Provider</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{ asset("/bower_components/bootstrap/dist/css/bootstrap.min.css") }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset("/bower_components/font-awesome/css/font-awesome.min.css") }}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset("/bower_components/Ionicons/css/ionicons.min.css") }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css") }}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/plugins/iCheck/square/blue.css") }}">
        <!-- Select 2 -->
        <link rel="stylesheet" href="{{ asset("/bower_components/select2/dist/css/select2.min.css") }}">



        <link rel="stylesheet" href="{{ asset("/css/site_ui.css") }}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}">
        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition login-page register-page">
        <div class="login-box register-box">
            <div class="login-box-body register-box-body">
                <div class="container reset-width">
                    <div class="">
                        <!-- Your Page Content Here -->
                        @yield('content')
                    </div>

                </div>
            </div>
        </div>
        <!-- /.login-box -->

        <!-- jQuery 3 -->
        <script src="{{ asset("/bower_components/jquery/dist/jquery.min.js") }}"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="{{ asset("/bower_components/bootstrap/dist/js/bootstrap.min.js") }}"></script>
        <!-- iCheck -->
        <script src="{{ asset("/bower_components/admin-lte/plugins/iCheck/icheck.min.js") }}"></script>
        <!-- Select 2 -->
        <script src="{{ asset("/bower_components/select2/dist/js/select2.full.min.js") }}"></script>

        <script type="text/javascript">
$(function () {
    $('.select2').select2();

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });

    $("#registerSP").submit(function () {
        if ($('.service_for[name="service_mw"]:checked').length < 1) {
            alert('Please Select atleast one Service For');
            return false;
        }
        return true;
    });
});
        </script>


    </body>
</html>
