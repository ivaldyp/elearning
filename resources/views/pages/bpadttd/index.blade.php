@extends('layouts.masterhome')

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
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-info">
						<div class="panel-heading">Tanda tangan</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<div class="table-responsive">
										<table class="table table-hover table-borderless">
											<thead>
												<tr>
													<th>User</th>
													<th>Nama</th>
													<th>File</th>
													<th>Action</th>
												</tr>
											</thead>
											@if(Auth::user()->usname_skpd)
											<tbody>
												<tr>
													<td>{{ Auth::user()->usname_skpd }}</td>
													@if(is_null($ttd))
													<td><span style="color: red">Anda belum mengunggah tandatangan</span></td>
													<td><span style="color: red">Anda belum mengunggah tandatangan</span></td>
													<td>
														<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 "><i class="fa fa-trash"></i></button>
													</td>
													@else
													<td>{{ $ttd['nama'] }}</td>
													<td>
														<a target="_blank" href="{{ config('app.openfilettd') }}{{ Auth::user()->usname_skpd }}/{{ $ttd['ttd'] }}"><i class="fa fa-download"></i> {{ $ttd['ttd'] }}</a>
													</td>
													<td>
														<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5  btn-delete" data-toggle="modal" data-target="#modal-delete" data-usname="{{ Auth::user()->usname_skpd }}" data-nama="{{ $ttd['nama'] }}" ><i class="fa fa-trash"></i></button>
													</td>
													@endif
													
												</tr>
											</tbody>
											@else
											<tbody>
												<tr>
													<td colspan="4" class="text-center"><h3>Anda tidak login menggunakan akun SKPD / UKPD</h3></td>
												</tr>
											</tbody>
											@endif
										</table>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
			@if(Auth::user()->usname_skpd)
			<div class="row ">
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<form class="form-horizontal" method="POST" action="/laporanbmd/tanda tangan/form/tambahttd" data-toggle="validator" enctype="multipart/form-data">
						@csrf
						<div class="panel panel-info">
							<div class="panel-heading">Form Unggah</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="row" style="margin-bottom: 10px">
									
										<div class="form-group">
											<label for="usname" class="col-md-2 control-label"> User </label>
											<div class="col-md-8">
												<input type="hidden" name="usname" value="{{ Auth::user()->usname_skpd }}">
												<p class="form-control-static">
													{{ Auth::user()->usname_skpd }} <br> 
													{{ $_SESSION['user_laporan']['deskripsi_user'] }}
												</p>
											</div>
										</div>

										<div class="form-group">
											<label for="nama" class="col-md-2 control-label"> Nama </label>
											<div class="col-md-8">
												@if(is_null($ttd))
													@if($_SESSION['user_laporan']['TLEVEL'] == 2)
														<p class="form-control-static">{{ $_SESSION['user_laporan']['nm_ka'] }}</p>
														<input type="hidden" class="form-control" name="nama" value="{{ $_SESSION['user_laporan']['nm_ka'] }}">
													@else
														<p class="form-control-static">{{ $_SESSION['user_laporan']['nm_pb'] }}</p>
														<input type="hidden" class="form-control" name="nama" value="{{ $_SESSION['user_laporan']['nm_pb'] }}">
													@endif
												@else
													<p class="form-control-static">{{ $ttd['nama'] }}</p>
													<input type="hidden" class="form-control" name="nama" value="{{ $ttd['nama'] }}">
												@endif
												<span class="help-block" style="color: red">*ubah nama melalui sistem SIERA</span>
											</div>
										</div>

										<div class="form-group">
											<label for="ttd" class="col-md-2 control-label"> File <br> <span style="font-size: 10px">Hanya berupa JPG, JPEG, dan PNG</span> </label>
											<div class="col-md-8">
												<input type="file" class="form-control" id="ttd" name="ttd"><br>
												@if($ttd)
													<a target="_blank" href="{{ config('app.openfilettd') }}{{ Auth::user()->usname_skpd }}/{{ $ttd['ttd'] }}"><i class="fa fa-download"></i> {{ $ttd['ttd'] }}</a>
												@endif
											</div>
										</div>
									</div>
								</div>
								<div class="panel-footer">
	                                <button type="submit" class="btn btn-success pull-right">Simpan</button>
	                                <div class="clearfix"></div>
	                            </div>
							</div>
						</div>
					</form>
				</div>
			</div>
			@endif
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/tanda tangan/form/hapusttd" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Tanda Tangan</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="usname" id="modal_delete_usname" value="">
								<input type="hidden" name="nama" id="modal_delete_nama" value="">
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
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>

	<script>
		$(function () {
			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus tanda tangan milik <b>' + $el.data('nama') + '</b>?');
				$("#modal_delete_usname").val($el.data('usname'));
				$("#modal_delete_nama").val($el.data('nama'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$(".select2").select2();
			$('.myTable').DataTable({
				"ordering" : false,
				"searching": false,
				"bInfo" : false,
				"bPaginate": false,
				"lengthChange": false,
				drawCallback: function() {
			    	$('[data-toggle="tooltip"]').tooltip();
			  	} 
			});
		});
	</script>
@endsection