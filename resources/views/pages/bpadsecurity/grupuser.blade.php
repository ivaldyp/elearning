@extends('layouts.masterhome' )

@section('css')
    <!-- Bootstrap Core CSS -->
    <link href="{{ ('/elearning/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ ('/elearning/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- Menu CSS -->
    <link href="{{ ('/elearning/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    <!-- animation CSS -->
    <link href="{{ ('/elearning/public/ample/css/animate.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ ('/elearning/public/ample/css/style.css') }}" rel="stylesheet">
    <!-- color CSS -->
    <link href="{{ ('/elearning/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">

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
                        <div class="panel-heading">Grup User</div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                @if($access['zadd'] == 'y')
                                <button data-toggle="modal" data-target="#modal-create" class="btn btn-info" style="margin-bottom: 10px">Tambah</button>
                                @endif
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-md-1">No</th>
                                                <th>Grup User</th>
                                                @if($access['zupd'] == 'y' || $access['zdel'] == 'y')
                                                <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($groups as $key => $group)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $group['idgroup'] }}</td>
                                                @if($access['zupd'] == 'y' || $access['zdel'] == 'y')
                                                <td>
                                                    @if($access['zupd'] == 'y')
                                                    <a href="/elearning/security/group user/ubah?name={{ $group['idgroup'] }}">
                                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-default"><i class="fa fa-edit"></i></button>
                                                    </a>
                                                    @endif
                                                    @if($access['zdel'] == 'y')
                                                    <button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-idgroup="{{ $group['idgroup'] }}"><i class="fa fa-trash"></i></button>
                                                    @endif
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->
                </div>
            </div>
            <div class="modal fade" id="modal-create">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="/elearning/security/form/tambahgrup" class="form-horizontal">
                        @csrf
                            <div class="modal-header">
                                <h4 class="modal-title"><b>Tambah Grup</b></h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="modal_insert_idgroup" class="col-lg-2 control-label"> Nama </label>
                                    <div class="col-lg-8">
                                        <input type="text" name="idgroup" id="modal_insert_idgroup" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success pull-right">Simpan</button>
                                <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal-delete">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="/elearning/security/form/hapusgrup" class="form-horizontal">
                        @csrf
                            <div class="modal-header">
                                <h4 class="modal-title"><b>Hapus Grup User</b></h4>
                            </div>
                            <div class="modal-body">
                                <h4 id="label_delete"></h4>
                                <input type="hidden" name="idgroup" id="modal_delete_idgroup" value="">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger pull-right">Hapus</button>
                                <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 
            <span>&copy; Copyright <?php echo date('Y'); ?> BPAD DKI Jakarta.</span></span></a>
        </footer>
    </div>
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
    <!-- jQuery -->
    <script src="{{ ('/elearning/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="{{ ('/elearning/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="{{ ('/elearning/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
    <!--slimscroll JavaScript -->
    <script src="{{ ('/elearning/public/ample/js/jquery.slimscroll.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ ('/elearning/public/ample/js/waves.js') }}"></script>
    <!-- Custom Theme JavaScript -->
    <script src="{{ ('/elearning/public/ample/js/custom.min.js') }}"></script>
    <script src="{{ ('/elearning/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.btn-delete').on('click', function () {
                var $el = $(this);      
                console.log($el.data('idgroup'));
                $("#label_delete").append('Apakah anda yakin ingin menghapus grup user <b>' + $el.data('idgroup') + '</b>?');
                $("#modal_delete_idgroup").val($el.data('idgroup'));
            });

            $("#modal-delete").on("hidden.bs.modal", function () {
                $("#label_delete").empty();
            });

            $('#myTable').DataTable();
        });
    </script>
@endsection