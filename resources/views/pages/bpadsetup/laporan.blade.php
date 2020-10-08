@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/produkhukum/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/produkhukum/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/produkhukum/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/produkhukum/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/produkhukum/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/produkhukum/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">

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
												echo str_replace('%20', ' ', ucwords(explode("?", $link[4])[0]));
											?> </h4> </div>
				<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
					<ol class="breadcrumb">
						<li>{{config('app.name')}}</li>
						<?php 
							if (count($link) == 5) {
								?> 
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[4])[0])) }} </li>
								<?php
							} elseif (count($link) > 5) {
								?> 
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[4])[0])) }} </li>
									<li class="active"> {{ str_replace('%20', ' ', ucwords(explode("?", $link[5])[0])) }} </li>
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
				<div class="col-sm-1"></div>
				<div class="col-md-10">
					<!-- <div class="white-box"> -->
					<div class="panel panel-info">
                        <div class="panel-heading"> Jenis Laporan </div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                            	<div class="row " style="margin-bottom: 10px">
                            		<button data-toggle="modal" data-target="#modal-insert" class="btn btn-info" style="margin-bottom: 10px">Tambah</button>                          		
                            	</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover">
											<thead>
												<tr>
													<th>No</th>
													<th>Kode</th>
													<th>Jenis Laporan</th>
													<th>Front / Back</th>
													<th>Tampilkan</th>
													<th class="col-md-2">Action</th>
												</tr>
											</thead>
											<tbody style="vertical-align: middle;">
												@foreach($laporans as $key => $lap)
												<tr>
													<td>{{ $key + 1 }}</td>
													<td>{{ $lap['kode'] }}</td>
													<td>{{ ucwords(strtolower($lap['jns_laporan'])) }}</td>
													<td>{{ $lap['front_column'] }} / {{ $lap['back_column'] }}</td>
													<td>
														@if($lap['tampilkan'] == 1)
															<span style="color: green"><i class="fa fa-check"></i></span>
														@else
															<span style="color: red"><i class="fa fa-close"></i></span>
														@endif
													</td>
													<td>
															<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="{{ $lap['ids'] }}" data-jns_laporan="{{ $lap['jns_laporan'] }}" data-kode="{{ $lap['kode'] }}" data-tampilkan="{{ $lap['tampilkan'] }}" data-front="{{ $lap['front_column'] }}" data-back="{{ $lap['back_column'] }}"><i class="fa fa-edit"></i></button>
															<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $lap['ids'] }}"><i class="fa fa-trash"></i></button>
													</td>
												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
                            	
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal-insert" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/setup/form/tambahlaporan" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b> Jenis Laporan Baru </b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="kode" class="col-sm-2 control-label"> Kode </label>
									<div class="col-sm-8">
										<input type="text" name="kode" id="kode" class="form-control" autocomplete="off" required="" placeholder="">
									</div>
								</div>

								<div class="form-group">
									<label for="jns_laporan" class="col-sm-2 control-label"> Nama </label>
									<div class="col-sm-8">
										<input type="text" name="jns_laporan" id="jns_laporan" class="form-control" autocomplete="off" required="" placeholder="Ketikkan nama saja, tanpa 'laporan'">
									</div>
								</div>

								<div class="form-group">
									<label for="jns_laporan" class="col-sm-2 control-label"> Front/Back </label>
									<div class="col-sm-4">
										<input type="text" name="front" id="front" class="form-control" autocomplete="off" required="">
									</div>
									<div class="col-sm-4">
										<input type="text" name="back" id="back" class="form-control" autocomplete="off" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="tampilkan" class="col-md-2 control-label"> Tampilkan? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilkan" id="tampil1" value="1" data-error="Pilih salah satu" required checked="">
												<label for="tampil1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilkan" id="tampil2" value="0">
												<label for="tampil2">Tidak </label>
											</div>
										</label>
										<div class="help-block with-errors"></div>  
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Tambah</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/setup/form/ubahlaporan" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b> Ubah Jenis Laporan </b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_update_ids">
								<div class="form-group">
									<label for="kode" class="col-sm-2 control-label"> Kode </label>
									<div class="col-sm-8">
										<input type="text" name="kode" id="modal_update_kode" class="form-control" autocomplete="off" required="" placeholder="">
									</div>
								</div>

								<div class="form-group">
									<label for="jns_laporan" class="col-sm-2 control-label"> Nama </label>
									<div class="col-sm-8">
										<input type="text" name="jns_laporan" id="modal_update_jns_laporan" class="form-control" autocomplete="off" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="jns_laporan" class="col-sm-2 control-label"> Front/Back </label>
									<div class="col-sm-4">
										<input type="text" name="front" id="modal_update_front" class="form-control" autocomplete="off" required="">
									</div>
									<div class="col-sm-4">
										<input type="text" name="back" id="modal_update_back" class="form-control" autocomplete="off" required="">
									</div>
								</div>

								<div class="form-group">
									<label for="tampilkan" class="col-md-2 control-label"> Tampilkan? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilkan" id="update_tampil1" value="1" data-error="Pilih salah satu" required>
												<label for="update_tampil1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilkan" id="update_tampil2" value="0">
												<label for="update_tampil2">Tidak </label>
											</div>
										</label>
										<div class="help-block with-errors"></div>  
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/setup/form/hapuslaporan" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Jenis Laporan</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
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
    </div>
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
	<script src="{{ ('/produkhukum/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ ('/produkhukum/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/produkhukum/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/produkhukum/public/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/produkhukum/public/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/produkhukum/public/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/produkhukum/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ ('/produkhukum/public/ample/js/validator.js') }}"></script>

	<script>
		$(function () {

			$('.btn-update').on('click', function () {
				var $el = $(this);

				$("#modal_update_ids").val($el.data('ids'));
				$("#modal_update_kode").val($el.data('kode'));
				$("#modal_update_jns_laporan").val($el.data('jns_laporan'));
				$("#modal_update_front").val($el.data('front'));
				$("#modal_update_back").val($el.data('back'));

				if ($el.data('tampilkan') == 1) {
					$("#update_tampil1").attr('checked', true);
				} else {
					$("#update_tampil2").attr('checked', true);
				}

			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus jenis laporan ini?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_jns_laporan").val($el.data('jns_laporan'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable();
		});
	</script>
@endsection