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
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
						<div class="panel-heading">Users</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								@if($access['zadd'] == 'y')
								<a href="/laporanbmd/security/tambah user"><button class="btn btn-info" style="margin-bottom: 10px">Tambah</button></a>
								@endif
								<div class="table-responsive">
									<table id="myTable" class="table table-hover">
										<thead>
											<tr>
												<th class="col-md-1">No</th>
												<th>Username</th>
												<th>Nama</th>
												<th>Deskripsi</th>
												<th>Email</th>
												<th>Idgroup</th>
												<th>Created At</th>
												@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<th class="col-md-2">Action</th>
												@endif
											</tr>
										</thead>
										<tbody>
										@foreach($users as $key => $user)
											<tr>
												<td>{{ $key + 1 }}</td>
												<td>{{ $user['usname'] }}</td>
												<td>{{ (!($user['nama_user']) ? '-' : $user['nama_user']) }}</td>
												<td>{{ (!($user['deskripsi_user']) ? '-' : $user['deskripsi_user']) }}</td>
												<td>{{ (!($user['email_user']) ? '-' : $user['email_user']) }}</td>
												<td>{{ $user['idgroup'] }}</td>
												<td>{{ (!($user['createdate']) ? '-' : date('d/M/Y', strtotime(str_replace('/', '-', $user['createdate'])))) }}</td>
												@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
												<td>
													@if($access['zupd'] == 'y')
													<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="{{ $user['ids'] }}" data-usname="{{ $user['usname'] }}" data-idgroup="{{ $user['idgroup'] }}" data-nama_user="{{ $user['nama_user'] }}" data-deskripsi_user="{{ $user['deskripsi_user'] }}" data-email_user="{{ $user['email_user'] }}"><i class="fa fa-edit"></i></button>
													<button type="button" class="btn btn-warning btn-password" data-toggle="modal" data-target="#modal-password-{{$key}}"><i class="fa fa-key"></i></button>
													@endif
													@if($access['zdel'] == 'y')
													<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-usname="{{ $user['usname'] }}" data-ids="{{ $user['ids'] }}"><i class="fa fa-trash"></i></button>
													@endif
													<div id="modal-password-{{$key}}" class="modal fade" role="dialog">
														<div class="modal-dialog">
															<div class="modal-content">
																<form method="POST" action="/laporanbmd/security/form/ubahpassuser" class="form-horizontal">
																@csrf
																	<div class="modal-header">
																		<h4 class="modal-title"><b>Ubah Password</b></h4>
																	</div>
																	<div class="modal-body">
																		<h4>Masukkan password baru  </h4>

																		<input type="hidden" name="ids" value="{{ $user['ids'] }}">
																		<input type="hidden" name="usname" value="{{ $user['usname'] }}">

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
			<div class="modal fade" id="modal-update">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/security/form/ubahuser" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah User</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="ids" id="modal_update_ids">
								<div class="form-group">
									<label for="modal_update_usname" class="col-lg-2 control-label"> Username </label>
									<div class="col-lg-8">
										<input type="text" name="usname" id="modal_update_usname" class="form-control" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label for="modal_update_nama_user" class="col-lg-2 control-label"> Nama </label>
									<div class="col-lg-8">
										<input type="text" name="nama_user" id="modal_update_nama_user" class="form-control" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label for="model_update_deskripsi_user" class="col-md-2 control-label"> Deskripsi </label>
									<div class="col-md-8">
										<input type="text" class="form-control" id="model_update_deskripsi_user" name="deskripsi_user" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label for="modal_update_email_user" class="col-md-2 control-label"> Email </label>
									<div class="col-md-8">
										<input type="email" class="form-control" id="modal_update_email_user" name="email_user" autocomplete="off" data-error="Masukkan format email yang benar">
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="modal_update_idgroup" class="col-md-2 control-label"> Grup User </label>
									<div class="col-md-8">
										<select class="form-control" name="idgroup" id="modal_update_idgroup">
											@foreach($idgroup as $group)
												<option value="{{ $group['idgroup'] }}"> {{ $group['idgroup'] }} </option>
											@endforeach
										</select>
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
						<form method="POST" action="/laporanbmd/security/form/hapususer" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus User</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="usname" id="modal_delete_usname" value="">
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
		<!-- /.container-fluid -->
		<footer class="footer text-center"> 
			<span>&copy; Copyright <?php echo date('Y'); ?> BPAD DKI Jakarta.</span></span></a>
		</footer>
	</div>
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

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
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/ample/js/validator.js') }}"></script>

	<script>
		$(document).ready(function() {

			$('.btn-update').on('click', function () {
				var $el = $(this);      
				$("#modal_update_ids").val($el.data('ids'));
				$("#modal_update_usname").val($el.data('usname'));
				$("#modal_update_nama_user").val($el.data('nama_user'));
				$("#model_update_deskripsi_user").val($el.data('deskripsi_user'));
				$("#modal_update_email_user").val($el.data('email_user'));
				$("#modal_update_idgroup").val($el.data('idgroup'));
			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);      
				$("#label_delete").append('Apakah anda yakin ingin menghapus user <b>' + $el.data('usname') + '</b>?');
				$("#modal_delete_usname").val($el.data('usname'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('#myTable').DataTable();
		});
	</script>
@endsection