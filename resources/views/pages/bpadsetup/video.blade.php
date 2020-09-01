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
					<div class="panel panel-default">
						<div class="panel-heading">{{ strtoupper($videos[0]['nm_materi']) }}</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<a href="{{ url('/setup/materi') }}"><strong><i class="fa fa-arrow-left"></i> Kembali ke halaman materi</strong></a>
								<hr width="0">
								<div class="row " style="margin-bottom: 10px">
									<div class="col-md-2">
										<button class="btn btn-info btn-insert" style="margin-bottom: 10px" data-toggle="modal" data-target="#modal-insert">Tambah</button>
									</div>
								</div>

								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover">
											<thead>
												<tr>
													<th class="text-center">Urut</th>
													<th>Nama</th>
													<th>URL</th>
													<th class="text-center col-md-2">Tampilkan?</th>
													<th class="col-md-2">Action</th>
												</tr>
											</thead>
											<tbody>
												@foreach($videos as $key => $vid)
												@if($key > 0)
												<tr>
													<td class="text-center">{{ intval($vid['urut']) }}</td>
													<td>{{ $vid['nm_materi'] }}</td>
													<td><a href="{{ $vid['url'] }}" target="_blank">{{ $vid['url'] }}</a></td>
													<td class="text-center col-md-2">{!! $vid['tampilkan'] == 0 ? '<i class="fa fa-close" style="color: red"></i>' : '<i class="fa fa-check" style="color: green"></i>' !!}</td>
													
													<td class="col-md-2">
														<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="{{ $vid['ids'] }}" data-nm_materi="{{ $vid['nm_materi'] }}" data-urut="{{ $vid['urut'] }}" data-tampilkan="{{ $vid['tampilkan'] }}" data-sao="{{ $videos[0]['ids'] }}" data-url="{{ $vid['url'] }}" data-parent="{{ $videos[0]['ids'] }}"><i class="fa fa-edit"></i></button>
														</form>

														<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-ids="{{ $vid['ids'] }}" data-nm_materi="{{ $vid['nm_materi'] }}" data-parent="{{ $videos[0]['ids'] }}"><i class="fa fa-trash"></i></button>
													</td>
												</tr>
												@endif
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
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/setup/tambah video" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Sub Materi</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="sao" value="{{ $videos[0]['ids'] }}">

								<div class="form-group">
									<label for="nm_materi" class="col-md-2 control-label"><span style="color: red">*</span> Sub Materi </label>
									<div class="col-md-8">
										<input type="text" name="nm_materi" id="modal_insert_desk" class="form-control" data-error="Masukkan nama materi" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="url" class="col-md-2 control-label"><span style="color: red">*</span> URL </label>
									<div class="col-md-8">
										<input type="text" name="url" id="modal_insert_url" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off" required data-error="Masukkan url video">
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="urut" class="col-md-2 control-label"> Urut </label>
									<div class="col-md-8">
										<input type="text" name="urut" id="modal_insert_urut" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-md-2 control-label"><span style="color: red">*</span> Tampilkan? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilkan" id="tampil1" value="1" data-error="Pilih salah satu" required>
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
								<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update" class="modal fade" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/setup/ubah video" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Sub Materi</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="sao" id="modal_update_parent">
								<input type="hidden" name="ids" id="modal_update_ids">

								<div class="form-group">
									<label for="nm_materi" class="col-md-2 control-label"><span style="color: red">*</span> Sub Materi </label>
									<div class="col-md-8">
										<input type="text" name="nm_materi" id="modal_update_nm_materi" class="form-control" data-error="Masukkan nama materi" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="url" class="col-md-2 control-label"><span style="color: red">*</span> URL </label>
									<div class="col-md-8">
										<input type="text" name="url" id="modal_update_url" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off" required data-error="Masukkan url video">
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="urut" class="col-md-2 control-label"> Urut </label>
									<div class="col-md-8">
										<input type="text" name="urut" id="modal_update_urut" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-md-2 control-label"><span style="color: red">*</span> Tampilkan? </label>
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
								<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/setup/hapus video" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Surat Keluar</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="parent" id="modal_delete_parent" value="">
								<input type="hidden" name="nm_materi" id="modal_delete_nm_materi" value="">
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

	<script>
		$(function () {

			$('.btn-update').on('click', function () {
				var $el = $(this);

				$("#modal_update_ids").val($el.data('ids'));
				$("#modal_update_parent").val($el.data('parent'));
				$("#modal_update_nm_materi").val($el.data('nm_materi'));
				$("#modal_update_url").val($el.data('url'));
				$("#modal_update_urut").val($el.data('urut'));

				if ($el.data('tampilkan') == 1) {
					$("#update_tampil1").attr('checked', true);
				} else {
					$("#update_tampil2").attr('checked', true);
				}
			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus sub materi <b>' + $el.data('nm_materi') + '</b>?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_parent").val($el.data('parent'));
				$("#modal_delete_nm_materi").val($el.data('nm_materi'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable();
		});
	</script>
@endsection