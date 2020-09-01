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
			
			<div class="row ">
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
						<div class="panel-heading"> Laporan Kinerja </div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/laporanbmd/kepegawaian/laporan kinerja">
										<div class="col-md-3">
											<select class="form-control select2" name="now_id_emp" id="now_id_emp" onchange="this.form.submit()">
												@forelse($pegawais as $pegawai)
												<option <?php if ($now_id_emp == $pegawai['id_emp']): ?> selected <?php endif ?> value="{{ $pegawai['id_emp'] }}">[{{ $pegawai['id_emp'] }}] - {{ ucwords(strtolower($pegawai['nm_emp'])) }}</option>
												@empty
												<option value="{{ $_SESSION['user_data']['id_emp'] }}">[{{ $_SESSION['user_data']['id_emp'] }}] - {{ ucwords(strtolower($_SESSION['user_data']['nm_emp'])) }}</option>
												@endforelse
											</select>
										</div>
										<div class="col-md-2">
											<select class="form-control" name="now_month" id="now_month" onchange="this.form.submit()">
												<option <?php if ($now_month == 1): ?> selected <?php endif ?> value="1">Januari</option>
												<option <?php if ($now_month == 2): ?> selected <?php endif ?> value="2">Februari</option>
												<option <?php if ($now_month == 3): ?> selected <?php endif ?> value="3">Maret</option>
												<option <?php if ($now_month == 4): ?> selected <?php endif ?> value="4">April</option>
												<option <?php if ($now_month == 5): ?> selected <?php endif ?> value="5">Mei</option>
												<option <?php if ($now_month == 6): ?> selected <?php endif ?> value="6">Juni</option>
												<option <?php if ($now_month == 7): ?> selected <?php endif ?> value="7">Juli</option>
												<option <?php if ($now_month == 8): ?> selected <?php endif ?> value="8">Agustus</option>
												<option <?php if ($now_month == 9): ?> selected <?php endif ?> value="9">September</option>
												<option <?php if ($now_month == 10): ?> selected <?php endif ?> value="10">Oktober</option>
												<option <?php if ($now_month == 11): ?> selected <?php endif ?> value="11">November</option>
												<option <?php if ($now_month == 12): ?> selected <?php endif ?> value="12">Desember</option>
											</select>
										</div>
										<div class=" col-md-2">
											<?php date_default_timezone_set('Asia/Jakarta'); ?>
											<select class="form-control" name="now_year" id="now_year" onchange="this.form.submit()">
												<option <?php if ($now_year == (int)date('Y')): ?> selected <?php endif ?> value="{{ (int)date('Y') }}">{{ (int)date('Y') }}</option>
												<option <?php if ($now_year == (int)date('Y') - 1): ?> selected <?php endif ?> value="{{ (int)date('Y') - 1 }}">{{ (int)date('Y') - 1 }}</option>
												<option <?php if ($now_year == (int)date('Y') - 2): ?> selected <?php endif ?> value="{{ (int)date('Y') - 2 }}">{{ (int)date('Y') - 2 }}</option>
											</select>
										</div>
										<div class=" col-md-2">
											<select class="form-control" name="now_valid" id="now_valid" onchange="this.form.submit()">
												<option <?php if ($now_valid == "= 1" ): ?> selected <?php endif ?> value="= 1">Approved</option>
												<option <?php if ($now_valid == "is null" ): ?> selected <?php endif ?> value="is null">Not Approved</option>
											</select>
										</div>
									</form>
								</div>
								<div class="row">
									<div class="col-md-12">
										<a href="/laporanbmd/kepegawaian/excel?id={{$now_id_emp}}&month={{$now_month}}&year={{$now_year}}&valid={{$now_valid}}"><button class="btn btn-info"> Excel </button></a>
									</div>
								</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover color-table primary-table" >
											<thead>
												<tr>
													<th class="col-md-1">Tanggal</th>
													<th class="col-md-1">Awal</th>
													<th class="col-md-1">Akhir</th>
													<th class="col-md-4">Uraian</th>
													<th class="col-md-4">Keterangan</th>
												</tr>
											</thead>
											<tbody>
												@php
												$nowdate = 0
												@endphp

												@foreach($laporans as $laporan)
												
												@if($nowdate != $laporan['tgl_trans'])
													@php $nowdate = $laporan['tgl_trans'] @endphp
													<tr style="background-color: #f7fafc !important">
														<td colspan="5"><b>TANGGAL: {{ date('D, d-M-Y',strtotime($laporan['tgl_trans'])) }} --- 
														@if($now_valid == "= 1")
														{{ $laporan['jns_hadir_app'] }}
														@else
														{{ $laporan['jns_hadir'] }}
														@endif

														@if($laporan['jns_hadir_app'] == 'Lainnya (sebutkan)' || $laporan['jns_hadir'] == 'Lainnya (sebutkan)')
														 --- {{ $laporan['lainnya'] }}
														@endif

														</b></td>
														<td style="display: none;"></td>
														<td style="display: none;"></td>
														<td style="display: none;"></td>
														<td style="display: none;"></td>
													</tr>
												@endif

												@if($now_valid == "= 1")
													@if($laporan['tipe_hadir_app'] != 2)
													<tr>
														<td>{{ date('d-M-Y',strtotime($laporan['tgl_trans'])) }}</td>
														<td>{{ date('H:i',strtotime($laporan['time1'])) }}</td>
														<td>{{ date('H:i',strtotime($laporan['time2'])) }}</td>
														<td>{{ $laporan['uraian'] }}</td>
														<td>{{ $laporan['keterangan'] }}</td>
													</tr>
													@endif
												@else
													@if($laporan['tipe_hadir'] != 2)
													<tr>
														<td>{{ date('d-M-Y',strtotime($laporan['tgl_trans'])) }}</td>
														<td>{{ date('H:i',strtotime($laporan['time1'])) }}</td>
														<td>{{ date('H:i',strtotime($laporan['time2'])) }}</td>
														<td>{{ $laporan['uraian'] }}</td>
														<td>{{ $laporan['keterangan'] }}</td>
													</tr>
													@endif
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
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<!-- start - This is for export functionality only -->
	<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
	<!-- end - This is for export functionality only -->

	<script>
		$(function () {
			$(".select2").select2();

			$('.myTable').DataTable({
				"paging":   false,
				"ordering": false,
				"info":     false,
				// dom: 'Bfrtip'
				// ,buttons: [
				// 	{
				// 		extend: 'excelHtml5',
				// 		title: 'Laporan Kinerja'
				// 	},
				// 	{
				// 		extend: 'pdfHtml5',
				// 		title: 'Laporan Kinerja'
				// 	}
				// ]
			});
		});
	</script>
@endsection