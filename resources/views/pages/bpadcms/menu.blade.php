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
                        <div class="panel-heading">Menu</div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
								@if($access['zadd'] == 'y')
								<button class="btn btn-info btn-insert" style="margin-bottom: 10px" data-toggle="modal" data-target="#modal-insert" data-ids="0" data-desk="Tidak Ada">Tambah Level 0</button>
								@endif
								<div class="table-responsive">
									<table id="myTable" class="table table-hover">
										<thead>
											<tr>
												<th>Level</th>
												<th>ID</th>
												<th>Nama</th>
												<th>Ket</th>
												<th>Icon</th>
												<th>Url</th>
												<th class="text-center">Urut</th>
												<th class="text-center">Child</th>
												<th class="text-center">Tampil</th>
												@if($access['zadd'] == 'y')
												<th class="text-center">Tambah Anak</th>
												@endif
												@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<th class="col-md-2">Action</th>
												@endif
											</tr>
										</thead>
										<tbody>
										{!! $menus !!}
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- </div> -->
				</div>
			</div>
			<div id="modal-insert" class="modal fade" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/cms/form/tambahmenu" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Tambah Menu</b></h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="desk" class="col-md-2 control-label"><span style="color: red">*</span> Nama Menu </label>
									<div class="col-md-8">
										<input type="text" name="desk" id="modal_insert_desk" class="form-control" data-error="Masukkan nama" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="desk" class="col-md-2 control-label"> Keterangan </label>
									<div class="col-md-8">
										<textarea name="zket" id="modal_insert_zket" class="form-control" autocomplete="off"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label for="sao" class="col-md-2 control-label"> Parent </label>
									<div class="col-md-8">
										<input type="text" name="sao" id="modal_insert_sao" class="form-control" disabled>
										<input type="hidden" name="sao" id="modal_insert_sao_real" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label for="urut" class="col-md-2 control-label"> Urut </label>
									<div class="col-md-8">
										<input type="text" name="urut" id="modal_insert_urut" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label for="iconnew" class="col-md-2 control-label"> Icon </label>
									<div class="col-md-8">
										<input type="text" name="iconnew" id="modal_insert_iconnew" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="urlnew" class="col-md-2 control-label"> URL </label>
									<div class="col-md-8">
										<input type="text" name="urlnew" id="modal_insert_urlnew" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-2 control-label"><span style="color: red">*</span> Tampilkan? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilnew" id="tampil1" value="1" data-error="Pilih salah satu" required>
												<label for="tampil1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilnew" id="tampil2" value="0">
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
						<form method="POST" action="/laporanbmd/cms/form/ubahmenu" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Menu</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_update_ids">
								<div class="form-group">
									<label for="desk" class="col-md-2 control-label"><span style="color: red">*</span> Nama Menu </label>
									<div class="col-md-8">
										<input type="text" name="desk" id="modal_update_desk" class="form-control" data-error="Masukkan nama" autocomplete="off" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="desk" class="col-md-2 control-label"> Keterangan </label>
									<div class="col-md-8">
										<textarea name="zket" id="modal_update_zket" class="form-control" autocomplete="off"></textarea>
									</div>
								</div>
								<!-- <div class="form-group">
									<label for="sao" class="col-md-2 control-label"> Parent </label>
									<div class="col-md-8">
										<input type="text" name="sao" id="modal_update_sao" class="form-control" disabled>
										<input type="hidden" name="sao" id="modal_update_sao_real" class="form-control">
									</div>
								</div> -->
								<div class="form-group">
									<label for="urut" class="col-md-2 control-label"> Urut </label>
									<div class="col-md-8">
										<input type="text" name="urut" id="modal_update_urut" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label for="iconnew" class="col-md-2 control-label"> Icon </label>
									<div class="col-md-8">
										<input type="text" name="iconnew" id="modal_update_iconnew" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="urlnew" class="col-md-2 control-label"> URL </label>
									<div class="col-md-8">
										<input type="text" name="urlnew" id="modal_update_urlnew" class="form-control" placeholder="Boleh dikosongkan" autocomplete="off">
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-2 control-label"><span style="color: red">*</span> Tampilkan? </label>
									<div class="radio-list col-md-8">
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilnew" id="update_tampil1" value="1" data-error="Pilih salah satu" required>
												<label for="update_tampil1">Ya</label> 
											</div>
										</label>
										<label class="radio-inline">
											<div class="radio radio-info">
												<input type="radio" name="tampilnew" id="update_tampil2" value="0">
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
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/cms/form/hapusmenu" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Menu</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="ids" id="modal_delete_ids" value="">
								<input type="hidden" name="sao" id="modal_delete_sao" value="">
								<input type="hidden" name="desk" id="modal_delete_desk" value="">
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
	<script src="{{ ('/laporanbmd/public/ample/js/validator.js') }}"></script>


	<script>
		$(function () {
			$('.btn-insert').on('click', function () {
				var $el = $(this);

				$("#modal_insert_sao").val("("+$el.data('ids')+") - "+$el.data('desk'));
				$("#modal_insert_sao_real").val($el.data('ids'));
			});

			$('.btn-update').on('click', function () {
				var $el = $(this);

				$("#modal_update_ids").val($el.data('ids'));
				$("#modal_update_desk").val($el.data('desk'));
				$("#modal_update_zket").val($el.data('zket'));
				$("#modal_update_urut").val($el.data('urut'));
				$("#modal_update_iconnew").val($el.data('iconnew'));
				$("#modal_update_urlnew").val($el.data('urlnew'));

				if ($el.data('tampilnew') == 1) {
					$("#update_tampil1").attr('checked', true);
				} else {
					$("#update_tampil2").attr('checked', true);
				}
			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus menu <b>' + $el.data('desk') + '</b>?');
				$("#modal_delete_ids").val($el.data('ids'));
				$("#modal_delete_sao").val($el.data('sao'));
				$("#modal_delete_desk").val($el.data('desk'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});
		});
	</script>
@endsection