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
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<!-- <div class="white-box"> -->
					<div class="panel panel-info">
						<div class="panel-heading">Laporan</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/laporanbmd/laporan">
										<div class="row col-md-12">
											
											<div class=" col-md-2">
												<select class="form-control" name="yearnow" id="yearnow" onchange="this.form.submit()">
													@foreach($years as $year)
													<option <?php if ($yearnow == $year['tahun']): ?> selected <?php endif ?> value="{{ $year['tahun'] }}">{{ $year['tahun'] }}</option>
													@endforeach
												</select>
											</div>
											<div class=" col-md-2">
												<select class="form-control" name="wilnow" id="wilnow" onchange="this.form.submit()">
													<option <?php if ($wilnow == "all"): ?> selected <?php endif ?> value="all">--SEMUA--</option>
													<option <?php if ($wilnow == "prov"): ?> selected <?php endif ?> value="prov">Provinsi</option>
													<option <?php if ($wilnow == 1): ?> selected <?php endif ?> value="1">Jakarta Pusat</option>
													<option <?php if ($wilnow == 2): ?> selected <?php endif ?> value="2">Jakarta Utara</option>
													<option <?php if ($wilnow == 3): ?> selected <?php endif ?> value="3">Jakarta Barat</option>
													<option <?php if ($wilnow == 4): ?> selected <?php endif ?> value="4">Jakarta Selatan</option>
													<option <?php if ($wilnow == 5): ?> selected <?php endif ?> value="5">Jakarta Timur</option>
													<option <?php if ($wilnow == 6): ?> selected <?php endif ?> value="6">Pulau Seribu</option>
												</select>
											</div>
											<button type="submit" class="btn btn-info">Cari</button>
										</div>
									</form>
								</div>
								<hr>
								<div class="row">
									<form method="POST" action="/laporanbmd/laporan/excel" class="form-horizontal">
										@csrf
										<div class="col-md-1"></div>
										<div class="col-md-10">
											
											<input type="hidden" name="tahun" value="{{ $yearnow }}">
											<input type="hidden" name="wilayah" value="{{ $wilnow }}">

											@if(!(is_null($pds)))
											<table class="myTable table table-hover table-striped">
												<thead>
													<tr>
														<th><input id="kolokall" type="checkbox"></th>
														<th>Kolok SKPD</th>		
														<th>Kolok</th>	
														<th>Nalok</th>											
													</tr>
												</thead>
												<tbody id="bodykolok">
													@foreach($pds as $pd)
													<tr>
														<td><input class="kolokchoose" type="checkbox" name="kolok[]" value="{{ $pd['kolok'] }}"></td>
														<td>{{ $pd['kolokskpd'] }}</td>
														<td>{{ $pd['kolok'] }}</td>
														<td>{{ $pd['nalok'] }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
											@endif

											<div class="form-group">
												<label for="kib" class="col-md-2 control-label"> KIB </label>
												<div class="col-md-8">
													<!-- <input id="kiball" type="checkbox" name="kib[]" value="semua" checked=""> <b> SEMUA</b><br> -->
													<input class="kib" type="checkbox" name="kib[]" value="A"> <b> A</b><br>
													<input class="kib" type="checkbox" name="kib[]" value="B"> <b> B</b><br>
													<input class="kib" type="checkbox" name="kib[]" value="C"> <b> C</b><br>
													<input class="kib" type="checkbox" name="kib[]" value="D"> <b> D</b><br>
													<input class="kib" type="checkbox" name="kib[]" value="E"> <b> E</b><br>
													<input class="kib" type="checkbox" name="kib[]" value="F"> <b> F</b>
												</div>
											</div>

											<div class="form-group">
												<label for="durasi" class="col-md-2 control-label"> Durasi </label>
												<div class="col-md-8">
													<select class="form-control" name="durasi" id="durasi">
														<!-- <option value="saldoawal::semester1">Saldo Awal -> Semester 1</option> -->
														<option value="saldoawal::saldoakhir">Saldo Awal -> Saldo Akhir</option>
														<!-- <option value="saldoawal::audited">Saldo Awal -> Audited</option> -->
													</select>
												</div>
											</div>

											<div class="form-group">
												<label for="laporan" class="col-md-2 control-label"> Laporan </label>
												<div class="col-md-8">
													<select class="form-control" name="laporan" id="laporan">
														@foreach($laporans as $lap)
														<option value="{{ $lap['kode'] }}">{{ ucwords(strtolower($lap['jns_laporan'])) }}</option>
														@endforeach
													</select>
												</div>
											</div>

											<!-- <div class="form-group">
												<label for="tipe" class="col-md-2 control-label"> Tipe </label>
												<div class="col-md-8">
													<select class="form-control" name="tipe" id="tipe">
														<option value="rekap">Rekap</option>
														<option value="detail">Detail</option>
													</select>
												</div>
											</div> -->

											<div class="form-group">
												<div class="col-md-10">
													<button type="submit" class="btn btn-success pull-right">SUSUN</button>	
												</div>
											</div>


										</div>
										
									</form>
									<div class="clearfix"></div>
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
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>

	<script>
		var ckbox = $('#kiball');
		var ckbox2 = $('#kolokall');

		$('#kiball').on('click' , function() {
			$('.kib').each(function(){
				$(this).removeAttr('checked');
			})
		});

		$('.kib').on('click', function(){
			$('#kiball').removeAttr('checked');
		});

		$('#kolokall').change(function(){
			if($(this).prop('checked')){
				$('#bodykolok tr td input[type="checkbox"]').each(function(){
					$(this).prop('checked', true);
				});
			}else{
				$('#bodykolok tr td input[type="checkbox"]').each(function(){
					$(this).prop('checked', false);
				});
			}
		});

		$(function () {

			$(".select2").select2();
			$('.myTable').DataTable({
				"ordering" : false,
				// "searching": false,
				// "bPaginate": false,
				// "bInfo" : false,
				"lengthChange": false,
				"pageLength": 50,
			});
		});
	</script>
@endsection