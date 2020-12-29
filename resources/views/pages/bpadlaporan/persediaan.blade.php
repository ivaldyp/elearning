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
				<div class="col-md-10" >
					<!-- <div class="white-box"> -->
					<div class="panel panel-info">
						<div class="panel-heading">Persediaan</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/laporanbmd/laporan/persediaan" class="form-horizontal">
										<div class="form-group">
											<label for="yearnow" class="col-md-1 control-label"> Filter </label>
											<div class="col-md-2">
												<select class="form-control" name="yearnow" id="yearnow" onchange="this.form.submit()">
													<option value="2020">2020</option>
												</select>
											</div>
											<div class="col-md-3">
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
										</div>
									</form>
								</div>
								<hr>	
								<div class="row">
									<form method="GET" action="/laporanbmd/laporan/persediaan/pdf" class="form-horizontal">
										@csrf

										<input id="tahun" type="hidden" name="tahun" value="{{ $yearnow }}">
										<input id="wilayah" type="hidden" name="wilayah" value="{{ $wilnow }}">
										<input id="laporan" type="hidden" name="laporan" value="K03">

										<div class="form-group">
											<label for="periode" class="col-md-1 control-label"> Periode </label>
											<div class="col-md-5">
												<select class="form-control" name="periode" id="periode">
													<option value="2">Semester 1</option>
													<option value="1">Tahunan</option>
												</select>
											</div>
										</div>

										<hr>

										<div class="table-responsive" style="height:800px;overflow:auto;">
											@if(!(is_null($pds)))
											<table class="myTable table table-sm table-hover table-striped" > 
												<thead >
													<tr>
														<!-- <th><input id="kolokall" type="checkbox"></th> -->
														<th>Kolok SKPD</th>		
														<th>Kolok</th>	
														<th>Nalok</th>		
														<th class="col-md-2">Unduh</th>									
													</tr>
												</thead>
												<tbody id="bodykolok">
													@foreach($pds as $pd)
													<tr>
														<!-- <td><input class="kolokchoose" type="checkbox" name="kolok[]" value="{{ $pd['kolok'] }}"></td> -->
														<td style="vertical-align: middle;">{{ $pd['kolokskpd'] }}</td>
														<td style="vertical-align: middle;">{{ $pd['kolok'] }}</td>
														<td style="vertical-align: middle;">{{ $pd['nalok'] }}</td>
														<td>
															<button type="button" style="background-color: Transparent; border: none;" data-output="excel" data-kolok="{{ $pd['kolok'] }}" class="btnExcel"><i class="fa fa-file-excel-o fa-2x" style="color: green; cursor: pointer;"></i></button>
															<button type="button" style="background-color: Transparent; border: none;" data-output="pdf" data-kolok="{{ $pd['kolok'] }}" class="btnPdf"><i class="fa fa-file-pdf-o fa-2x" style="color: red; cursor: pointer;"></i></button>
															@if(strtolower($_SESSION['user_laporan']['idgroup']) == 'superuser')
																<button type="button" style="background-color: Transparent; border: none;" data-output="view" data-kolok="{{ $pd['kolok'] }}" class="btnView"><i class="fa fa-eye fa-2x" style="color: yellow; cursor: pointer;"></i></button>
															@endif
														</td>
													</tr>
													@endforeach
												</tbody>
											</table>
											@endif
										</div>

										<div class="form-group">
											<!-- <input type="submit" name="btnKirim" class="btn btn-info pull-right m-r-10 fa fa-file-excel-o" value="Kirim">
                                			<input type="submit" name="btnDraft" class="btn btn-warning pull-right m-r-10" value="Draft"> -->
                                			<!-- <button type="submit" name="output" value="excel"><img src="{ ('/laporanbmd/public/img/logo/excel.png') }}" alt="excel"></button>
                                			<button type="submit" name="output" value="pdf"><img src="{ ('/laporanbmd/public/img/logo/pdf.png') }}" alt="pdf"></button> -->
                                			<!-- <input class="pull-right col-md-1" type="image" name="output" src="{{ ('/laporanbmd/public/img/logo/excel.png') }}" height="40" width="40" alt="excel" value="excel"/>
                                			<input class="pull-right col-md-1" type="image" name="output" src="{{ ('/laporanbmd/public/img/logo/pdf.png') }}" height="40" width="40" alt="pdf" value="pdf"/> -->
										</div>

									</form>
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
		var ckbox2 = $('#kolokall');

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

			// $(".btnExcel").click(function() {
			$('.btnExcel').on('click', function () {
			    // alert(this.id); // or alert($(this).attr('id'));

   				var tahun = $("#tahun").val();
   				var periode = $( "#periode" ).val();
   				var laporan = $("#laporan").val();

   				var kolok = $(this).data('kolok');
   				var output = $(this).data('output');

   				var url = "/laporanbmd/laporan/persediaan/excel?"+
   							"tahun="+tahun+"&"+
   							"periode="+periode+"&"+
   							"laporan="+laporan+"&"+
							"kolok="+kolok+"&"+
							"output="+output;

   				window.location.href = url;
			});

			$('.btnPdf').on('click', function () {
			    // alert(this.id); // or alert($(this).attr('id'));

   				var tahun = $("#tahun").val();
   				var periode = $( "#periode" ).val();
   				var laporan = $("#laporan").val();

   				var kolok = $(this).data('kolok');
   				var output = $(this).data('output');

   				var url = "/laporanbmd/laporan/persediaan/pdf?"+
   							"tahun="+tahun+"&"+
   							"periode="+periode+"&"+
   							"laporan="+laporan+"&"+
							"kolok="+kolok+"&"+
							"output="+output;

   				window.location.href = url;
			});

			$('.btnView').on('click', function () {
			    // alert(this.id); // or alert($(this).attr('id'));

   				var tahun = $("#tahun").val();
   				var periode = $( "#periode" ).val();

   				var kolok = $(this).data('kolok');
   				var output = $(this).data('output');

   				var url = "/laporanbmd/laporan/persediaan/view?"+
   							"tahun="+tahun+"&"+
   							"periode="+periode+"&"+
							"kolok="+kolok+"&"+
							"output="+output;

				window.open(
					url,
					'_blank' // <- This is what makes it open in a new window.
				);
   				// window.location.href = url;
			});

			$(".select2").select2();
			$('.myTable').DataTable({
				"ordering" : false,
				// "searching": false,
				// "bPaginate": false,
				// "bInfo" : false,
				"lengthChange": true,
				// "pageLength": 20,
				// "scrollY": "200px",
			});
		});
	</script>
@endsection