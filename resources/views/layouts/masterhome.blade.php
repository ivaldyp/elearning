<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.komponen.head')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ ('/laporanbmd/public/img/photo/bpad-logo-05.png') }}">

    <title>BPAD</title>
    @yield('css')
</head>

<body class="fix-header">
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>
    </div>
    <!-- ============================================================== -->
    <!-- Wrapper -->
    <!-- ============================================================== -->
    <div id="wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header">
                <div class="top-left-part">
                    <!-- Logo -->
                    <a class="logo" href="/laporanbmd/home">
                        <span class="hidden-sm hidden-md hidden-lg"><img width="50%" src="{{ ('/laporanbmd/public/img/photo/bpad-logo-05.png') }}"></span>
                        <span class="hidden-xs"><img width="20%" src="{{ ('/laporanbmd/public/img/photo/bpad-logo-000.png32') }}"><strong>BPAD</strong>
                        </span>
                    </a>
                </div>
                <!-- /Logo -->
                <!-- Search input and Toggle icon -->
                <ul class="nav navbar-top-links navbar-right pull-right">
                    @include('layouts.komponen.profilepic')
                </ul>
                <ul class="nav navbar-top-links navbar-left pull-right">
                    <li><a href="javascript:void(0)" class="open-close waves-effect waves-light visible-xs"><i class="ti-close ti-menu"></i></a></li>
                    @include('layouts.komponen.notification')
                </ul>
            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <!-- End Top Navigation -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav slimscrollsidebar">
                <div class="sidebar-head">
                    <h3><span class="fa-fw open-close"><i class="ti-menu hidden-xs"></i><i class="ti-close visible-xs"></i></span> <span class="hide-menu">Menu</span></h3> 
                </div>
                {!! $_SESSION['menus_laporan'] !!}
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Left Sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        
        @yield('content')
        <div id="modal-password" class="modal fade" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/laporanbmd/home/password" class="form-horizontal">
                @csrf
                    <div class="modal-header">
                        <h4 class="modal-title"><b>Ubah Password</b></h4>
                    </div>
                    <div class="modal-body">
                        <h4>Masukkan password baru  </h4>

                        <div class="form-group col-md-12">
                            <label for="idunit" class="col-md-2 control-label"> Password </label>
                            <div class="col-md-8">
                                <input autocomplete="off" type="text" name="passmd5" class="form-control" required>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger pull-right">Simpan</button>
                        <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <!-- ============================================================== -->
        <!-- End Page Content -->
        <!-- ============================================================== -->
    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    @yield('js')

</body>

</html>