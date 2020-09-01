@extends('layouts.masterhome' )

@section('css')
    <!-- Bootstrap Core CSS -->
    <link href="{{ ('/laporanbmd/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- Menu CSS -->
    <link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    <!-- animation CSS -->
    <link href="{{ ('/laporanbmd/public/ample/css/animate.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ ('/laporanbmd/public/ample/css/style.css') }}" rel="stylesheet">
    <!-- color CSS -->
    <link href="{{ ('/laporanbmd/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
    <!-- page CSS -->
    <link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('content')
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <h4 class="page-title"><?php 
                                                $link = explode("/", url()->full());    
                                                echo ucwords($link[4]);
                                            ?> </h4> </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
                    <ol class="breadcrumb">
                        <li>{{config('app.name')}}</li>
                        <?php 
                            $link = explode("/", url()->full());
                            if (count($link) == 5) {
                                ?> 
                                    <li class="active"> {{ ucwords($link[4]) }} </li>
                                <?php
                            } elseif (count($link) == 6) {
                                ?> 
                                    <li class="active"> {{ ucwords($link[4]) }} </li>
                                    <li class="active"> {{ str_replace('%20', ' ', ucwords($link[5])) }} </li>
                                <?php
                            } 
                        ?>
                    </ol>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-sm-12">
                    @if(Session::has('message'))
                        <div class="alert <?php if(Session::get('msg_num') == 1) { ?>alert-success<?php } else { ?>alert-danger<?php } ?> alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="color: white;">&times;</button>{{ Session::get('message') }}</div>
                    @endif
                </div>
            </div>
            <div class="row ">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <!-- <div class="white-box"> -->
                        <div class="panel panel-default">
                            <div class="panel-heading">Tambah User</div>
                            <form class="form-horizontal" method="POST" action="/laporanbmd/security/form/tambahuser" data-toggle="validator">
                            @csrf   
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="name" class="col-md-2 control-label"><span style="color: red">*</span> Nama </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="name" name="name" autocomplete="off" data-error="Masukkan nama pengguna" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="deskripsi_user" class="col-md-2 control-label"> Deskripsi </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="deskripsi_user" name="deskripsi_user" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email_user" class="col-md-2 control-label"> Email </label>
                                            <div class="col-md-8">
                                                <input type="email" class="form-control" id="email_user" name="email_user" autocomplete="off" data-error="Masukkan format email yang benar">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="idgroup" class="col-md-2 control-label"><span style="color: red">*</span> Grup User </label>
                                            <div class="col-md-8">
                                                <select class="form-control select2" name="idgroup" id="idgroup" required>
                                                    <option value="<?php echo NULL; ?>" selected disabled>-- Pilih Grup --</option>
                                                    @foreach($idgroup as $group)
                                                        <option> {{ $group['idgroup'] }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="username" class="col-md-2 control-label"><span style="color: red">*</span> Username </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="username" name="username" autocomplete="off" data-error="Masukkan username" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="col-md-2 control-label"><span style="color: red">*</span> Password </label>
                                            <div class="col-md-8">
                                                <input type="password" class="form-control" id="password" name="password" autocomplete="off" data-minlength="6" data-error="Minimal 6 Karakter" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="conf_password" class="col-md-2 control-label"><span style="color: red">*</span> Konfirmasi Password </label>
                                            <div class="col-md-8">
                                                <input type="password" class="form-control" id="conf_password" name="conf_password" autocomplete="off" data-match="#password" data-match-error="Whoops, these don't match" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button type="submit" class="btn btn-success pull-right">Simpan</button>
                                        <!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- jQuery -->
    <script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="{{ ('/laporanbmd/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
    <!--slimscroll JavaScript -->
    <script src="{{ ('/laporanbmd/public/ample/js/jquery.slimscroll.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ ('/laporanbmd/public/ample/js/waves.js') }}"></script>
    <!-- Custom Theme JavaScript -->
    <script src="{{ ('/laporanbmd/public/ample/js/custom.min.js') }}"></script>
    <script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ ('/laporanbmd/public/ample/js/validator.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2();
        });
    </script>
@endsection