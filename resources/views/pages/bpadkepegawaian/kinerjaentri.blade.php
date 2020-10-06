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
                        <div class="panel-heading"> Entri Kinerja </div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                            	<div class="row" style="margin-bottom: 10px">
                            		<div class="col-md-1">
										@if ($access['zadd'] == 'y' && $_SESSION['user_laporan']['id_emp'] )
										<form class="form-horizontal" method="POST" action="/laporanbmd/kepegawaian/kinerja tambah">
											<button class="btn btn-info" style="margin-bottom: 10px">Tambah</button>
										@csrf
										</form>
										@endif
									</div>
                            	</div>
								<div class="row container">
									<h3 class="text-center">tabel kinerja belum tervalidasi</h3>
									<div class="table-responsive">
										<table class="myTable table table-hover color-table primary-table" >
											<thead>
												<tr>
													<th class="col-md-2">Tanggal</th>
													<th class="col-md-4">Kehadiran</th>
													<th class="col-md-2">lainnya</th>
													<th class="col-md-1">Action</th>
												</tr>
											</thead>
											<tbody>
												@php
												$nowdate = ''
												@endphp

												@if($laporans != null)

													@foreach($laporans as $key => $laporan)
														@if ($nowdate != $laporan['tgl_trans'])
															@php
															$nowdate = $laporan['tgl_trans']
															@endphp
															<?php 
																if ($laporan['tipe_hadir'] == 1) {
																	$tipe_hadir = 'Hadir';
																} elseif ($laporan['tipe_hadir'] == 2) {
																	$tipe_hadir = 'Tidak Hadir';
																} elseif ($laporan['tipe_hadir'] == 3) {
																	$tipe_hadir = 'DL Full';
																}
															?>

															<tr>
																<td>{{ date('d-M-Y',strtotime($laporan['tgl_trans'])) }}</td>
																<td>{{ $tipe_hadir }} --- {{ $laporan['jns_hadir'] }}
																	@if($laporan['keterangan'] == null && $laporan['uraian'] == null)
																	<br><span style="color: red">Tidak ada kinerja</span>
																	@endif
																</td>
																<td>{{ ($laporan['lainnya'] ? $laporan['lainnya'] : '-') }}</td>
																<td>
																	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																		<form method="POST" action="/laporanbmd/kepegawaian/kinerja tambah">
																			@csrf
																			<input type="hidden" name="now_tgl_trans" value="{{ $laporan['tgl_trans'] }}">
																			<input type="hidden" name="now_tipe_hadir" value="{{ $laporan['tipe_hadir'] }}">
																			<input type="hidden" name="now_jns_hadir" value="{{ $laporan['jns_hadir'] }}">
																			<input type="hidden" name="now_lainnya" value="{{ $laporan['lainnya'] }}">
																			<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn_update_kinerja"><i class='fa fa-edit'></i></button>
																		</form>
																	</div>
																	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																		@if($laporan['stat'] != 1)
																		<form method="POST" action="/laporanbmd/kepegawaian/form/hapuskinerja">
																			@csrf
																			<input type="hidden" name="idemp" value="{{ $laporan['idemp'] }}">
																			<input type="hidden" name="tgl_trans" value="{{ $laporan['tgl_trans'] }}">
																			<button type="submit" class="btn btn-danger btn-outline btn-circle m-r-5 btn_delete_kinerja" ><i class='fa fa-trash'></i></button>
																		</form>
																		@endif
																	</div>
																	
																</td>
															</tr>
														@endif
													
													@endforeach

												@endif
											</tbody>
										</table>
									</div>
								</div>
                            	
                            </div>
                        </div>
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
	<!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <!-- end - This is for export functionality only -->

	<script>
		$(function () {
			$('.myTable').DataTable({
				"paging":   false,
		        "ordering": false,
		        "info":     false,
			});
		});
	</script>
@endsection