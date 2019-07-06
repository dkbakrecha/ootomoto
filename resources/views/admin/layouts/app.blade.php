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

        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/plugins/iCheck/square/blue.css") }}">

        <link rel="stylesheet" href="{{ asset("/css/site_ui.css") }}">

        <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
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
            @include('admin.layouts.header')

            <!-- Sidebar -->
            @include('admin.layouts.sidebar')

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
            <?php /* @include('admin.layouts.footer') */ ?>
            <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">              
                        <div class="modal-body">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <img src="" class="imagepreview" style="width: 100%;" >
                        </div>
                    </div>
                </div>
            </div>
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
        <!-- Sparkline -->
        <script src="{{ asset("/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js") }}"></script>

        <!-- Morris.js charts -->
        <script src="{{ asset("/bower_components/raphael/raphael.min.js") }}"></script>
        <script src="{{ asset("/bower_components/morris.js/morris.min.js") }}"></script>

        <script src="{{ asset("/js/bootstrap-notify/dist/bootstrap-notify.min.js") }}"></script>

        <!-- DataTables -->
        <script src="{{ asset("/bower_components/datatables.net/js/jquery.dataTables.min.js") }}"></script>
        <script src="{{ asset("/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js") }}"></script>

        <!-- iCheck -->
        <script src="{{ asset("/bower_components/admin-lte/plugins/iCheck/icheck.min.js") }}"></script>


        <!-- AdminLTE App -->
        <script src="{{ asset("/bower_components/admin-lte/dist/js/adminlte.min.js") }}"></script>


        <!-- Optionally, you can add Slimscroll and FastClick plugins.
             Both of these plugins are recommended to enhance the
             user experience. -->

        <script type="text/javascript">

$('#supAddModal').on('show.bs.modal', function (event) {
    jQuery('.alert-danger').hide();
    var modal = $(this);
    modal.find('input:text, input:password, select, textarea').val('');
});

$(document).ready(function () {

    $('#supAddModal').find('select#service_provider_id').on('change', function () {

        var _id = this.value;
        var _title = $(this).data('title');
        ;

        if (_id != "") {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('admin/staff_list') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    if (result.data == "") {
                        $('#supAddModal').find("#staff_id").html('');
                        $('#supAddModal').find("#staff_id").append('<option value="" disabled selected>' + _title + '</option>');
                        $('#supAddModal').find("#emptyStaff").html('{{ __("messages.no_staff_to_shop") }}');

                    } else {
                        $('#supAddModal').find("#staff_id").html('');
                        $('#supAddModal').find("#staff_id").append('<option value="" disabled selected>' + _title + '</option>');
                        $('#supAddModal').find("#emptyStaff").html('');
                        $.each(result.data, function (key, value) {
                            $('#supAddModal').find("#staff_id").append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                }
            });
        }
    });

    jQuery('#supervisorAddForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "{{ route('users.supervisor_add') }}",
            method: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                console.log(result);
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                    jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }});
    });
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

$('#editModal').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget)
    var title = button.data('title')
    var cate_id = button.data('cate_id')
    var _id = button.data('cat_id')

    var modal = $(this)
    modal.find('.modal-body #name').val(title)
    modal.find('.modal-body #cat_id').val(_id)
    modal.find('.modal-body #cate_id').val(cate_id)
});


//Open Edit Service Model with Data
$('#editServiceModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var _id = button.data('id')

    var modal = $(this)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery.ajax({
        url: "{{ url('/admin/getService') }}",
        method: 'post',
        data: {id: _id},
        success: function (result) {
            modal.find('.modal-body #id').val(result.data.id)
            modal.find('.modal-body #unique_id').val(result.data.unique_id)
            modal.find('.modal-body #name').val(result.data.name)
            modal.find('.modal-body #category_id').val(result.data.category_id)
            modal.find('.modal-body #duration').val(result.data.duration)
            modal.find('.modal-body #price').val(result.data.price)
        }
    });
});



//Open Edit Service Model with Data
$('#areaEditModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var _id = button.data('id')

    var modal = $(this)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery.ajax({
        url: "{{ url('/admin/getArea') }}",
        method: 'post',
        data: {id: _id},
        success: function (result) {
            modal.find('.modal-body #id').val(result.data.id)
            modal.find('.modal-body #unique_id').val(result.data.unique_id)
            modal.find('.modal-body #name').val(result.data.name)
            modal.find('.modal-body #address').val(result.data.address)
        }
    });
});


//Open View Service Model with Data
$('#areaViewModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var _id = button.data('id');

    var modal = $(this)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery.ajax({
        url: "{{ url('/admin/viewArea') }}",
        method: 'POST',
        data: {id: _id},
        success: function (result) {
            modal.find('.modal-body #id').val(result.data.id);
            modal.find('.modal-body #unique_id').val(result.data.unique_id);
            modal.find('.modal-body #name').val(result.data.name);
            modal.find('.modal-body #service_provider').val(result.providers + " Provider");
            modal.find('.modal-body #users').val(result.customers + " Users");
            modal.find('.modal-body #income').val(result.income);
        }
    });
});




$('#editProviderModal #image_label, #viewProviderModal #image_label').click(function () {
    $('#editProviderModal .modal-body #previewImages, #viewProviderModal .modal-body #previewImages').show();
});

function closeImgBox() {
    $('#editProviderModal .modal-body #previewImages, #viewProviderModal .modal-body #previewImages').hide();
}

//Open Edit Customer Model with Data
$('#editCustomerModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var _id = button.data('id')

    var modal = $(this)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery.ajax({
        url: "{{ url('/admin/getCustomer') }}",
        method: 'post',
        data: {id: _id},
        success: function (result) {
            modal.find('.modal-body #id').val(result.data.id)
            modal.find('.modal-body #unique_id').val(result.data.unique_id)
            modal.find('.modal-body #name').val(result.data.name)
            modal.find('.modal-body #email').val(result.data.email)
            modal.find('.modal-body #phone').val(result.data.phone)

            modal.find('.modal-body input:radio[name=gender]').filter('[value="' + result.data.gender + '"]').iCheck('check');

            modal.find('.modal-body #gender').val(result.data.gender)
            modal.find('.modal-body #area_id').val(result.data.area_id)
            modal.find('.modal-body #address').val(result.data.address)
        }
    });
});


$('#serviceAddModal, #categoryAdd, #areaAddModal, #providerAddModal, #customerAddModal').on('show.bs.modal', function (event) {
    jQuery('.alert-danger').hide();
    var modal = $(this);
    modal.find('input:text, input:password, select, textarea').val('');
    modal.find('input:radio, input:checkbox').prop('checked', false);
});


jQuery(document).ready(function () {
    $('.select2').select2();

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
                url: "{{ url('/admin/removeShopImage') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    $('body').find("#img_" + _id).remove();
                }});
        }
    });

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
    });

    var table = $('.flair-datatable').DataTable({
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
            "emptyTable": "{{ (Auth::user()->preferred_language == 'en')?'No records found':'لا توجد سجلات' }}",
            "zeroRecords":"{{ (Auth::user()->preferred_language == 'en')?'No matching records found':'لم يتم العثور على سجلات مطابقة' }}",
        },
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


    $('.sparkbar').each(function () {
        var $this = $(this);
        $this.sparkline('html', {
            type: 'bar',
            height: $this.data('height') ? $this.data('height') : '30',
            barColor: $this.data('color'),
            barWidth: $this.data('width')
        });
    });

    //jQuery('#categorySubmit').click(function (e) {
    jQuery('#categoryAddForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/categories') }}",
            method: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                     jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }});
    });


    $("#block_sp").click(function () {
        var _provider_id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/providerBlock') }}",
            method: 'post',
            data: {id: _provider_id},
            success: function (result) {
                 jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
            }
        });
    });

    jQuery('#serviceAddForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/services') }}",
            method: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                     jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }});
    });

    jQuery('#serviceEditForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ route('services.update','test') }}",
            method: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                     jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }});
    });


    jQuery('#areaAddForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/area') }}",
            method: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                    jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }});
    });

    jQuery('#areaEditForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ route('area.update','test') }}",
            method: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                    jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }});
    });



    jQuery('#providerAddForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if ($('.service_for[name="service_mw"]:checked').length < 1) {
            alert('Please Select atleast one Service For');
            return false;
        }
        
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "{{ url('/admin/provider') }}",
            method: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                    jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }});
    });

    jQuery('#supervisorEditForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "{{ route('supervisor.update','test') }}",
            method: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                    jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }

        });
    });



    jQuery('#providerEditForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

//        var formData = new FormData(this);
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "{{ route('provider.update','test') }}",
            method: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                console.log(result);
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                    jQuery('.alert-danger').hide();
                    setCookie("success", result.success, 1);
                    location.reload();
                }
            }
        });
    });

    jQuery('#customerAddForm').submit(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/customer') }}",
            method: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.errors)
                {
                    jQuery('.alert-danger').html('');

                    jQuery.each(result.errors, function (key, value) {
                        jQuery('.alert-danger').show();
                        jQuery('.alert-danger').append('<li>' + value + '</li>');
                    });
                } else
                {
                    jQuery('.alert-danger').hide();
                    $('#customerAddModal').modal('hide');
                    location.reload();
                }
            }});
    });

    

});
        </script>


        @yield('page-js-script')

    </body>
</html>