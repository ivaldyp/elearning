@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/elearning/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- Page plugins css -->
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
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
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<form class="form-horizontal" method="POST" action="/elearning/kepegawaian/form/tambahkinerja" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Input Kinerja </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">
									<input type="hidden" name="idemp" value="{{ Auth::user()->id_emp }}" id="idemp">

									<div class="form-group">
										<label for="tgl_masuk" class="col-md-2 control-label"> Tanggal </label>
										<div class="col-md-8">
											<input type="text" class="form-control datepicker-autoclose" id="tgl_trans" name="tgl_trans" autocomplete="off" value="{{ $now_tgl_trans }}">
										</div>
									</div>

									<div class="form-group">
										<label for="tipe" class="col-md-2 control-label"> Kehadiran </label>
										<div class="col-md-4">
											<select class="form-control" name="tipe_hadir" id="tipe_hadir" onchange="getval(this);">
												<option <?php if ($now_tipe_hadir == 1): ?> selected <?php endif ?> value="1"> Hadir </option>
												<option <?php if ($now_tipe_hadir == 2): ?> selected <?php endif ?> value="2"> Tidak Hadir </option>
												<option <?php if ($now_tipe_hadir == 3): ?> selected <?php endif ?> value="3"> DL Full </option>
											</select>
										</div>
										<div class="col-md-4">
											<select class="form-control" name="jns_hadir" id="jns_hadir">
												<option <?php if ($now_jns_hadir == 'Tepat Waktu (8,5 jam/hari)'): ?> selected <?php endif ?> class="select_jns_hadir tipe-1" id="1-first" value="Tepat Waktu (8,5 jam/hari)"> Tepat Waktu (8,5 jam/hari) </option>
												<option <?php if ($now_jns_hadir == 'Dinas Luar Awal'): ?> selected <?php endif ?> class="select_jns_hadir tipe-1" value="Dinas Luar Awal"> Dinas Luar Awal </option>
												<option <?php if ($now_jns_hadir == 'Dinas Luar Akhir'): ?> selected <?php endif ?> class="select_jns_hadir tipe-1" value="Dinas Luar Akhir"> Dinas Luar Akhir </option>
												<option <?php if ($now_jns_hadir == 'Terlambat'): ?> selected <?php endif ?> class="select_jns_hadir tipe-1" value="Terlambat"> Terlambat </option>
												<option <?php if ($now_jns_hadir == 'Pulang Cepat'): ?> selected <?php endif ?> class="select_jns_hadir tipe-1" value="Pulang Cepat"> Pulang Cepat </option>
												<option <?php if ($now_jns_hadir == 'Sakit'): ?> selected <?php endif ?> class="select_jns_hadir tipe-2" id="2-first" value="Sakit"> Sakit </option>
												<option <?php if ($now_jns_hadir == 'Izin'): ?> selected <?php endif ?> class="select_jns_hadir tipe-2" value="Izin"> Izin </option>
												<option <?php if ($now_jns_hadir == 'Cuti'): ?> selected <?php endif ?> class="select_jns_hadir tipe-2" value="Cuti"> Cuti </option>
												<option <?php if ($now_jns_hadir == 'Alpa'): ?> selected <?php endif ?> class="select_jns_hadir tipe-2" value="Alpa"> Alpa </option>
												<option <?php if ($now_jns_hadir == 'Lainnya (sebutkan)'): ?> selected <?php endif ?> class="select_jns_hadir tipe-2 lainnya" value="Lainnya (sebutkan)"> Lainnya (sebutkan) </option>
												<option <?php if ($now_jns_hadir == 'Rapat'): ?> selected <?php endif ?> class="select_jns_hadir tipe-3" id="3-first" value="Rapat"> Rapat </option>
												<option <?php if ($now_jns_hadir == 'Peninjauan Lapangan'): ?> selected <?php endif ?> class="select_jns_hadir tipe-3" value="Peninjauan Lapangan"> Peninjauan Lapangan </option>
												<option <?php if ($now_jns_hadir == 'Lainnya (sebutkan)'): ?> selected <?php endif ?> class="select_jns_hadir tipe-3 lainnya" value="Lainnya (sebutkan)"> Lainnya (sebutkan) </option>
											</select>
										</div>
									</div>

									<div class="form-group" id="input_lainnya">
										<label for="lainnya" class="col-md-2 control-label"> Lainnya </label>
										<div class="col-md-8">
											<textarea class="form-control" name="lainnya" id="lainnya">{{ $now_lainnya }}</textarea>
										</div>
									</div>

									<hr>

									<div id="form_aktivitas">
										<div class="form-group">
											<label for="tgl_masuk" class="col-md-2 control-label"> Awal </label>
											<div class="col-md-3">
												<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
													<input type="text" class="form-control" value="00:00" name="time1" id="time1"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
												</div>
											</div>
											<label for="tgl_masuk" class="col-md-2 control-label"> Akhir </label>
											<div class="col-md-3">
												<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
													<input type="text" class="form-control" value="00:00" name="time2" id="time2"> <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span>
												</div>
											</div>
										</div>

										<div class="form-group" id="input_lainnya">
											<label for="uraian" class="col-md-2 control-label"> Uraian </label>
											<div class="col-md-8">
												<textarea class="form-control" name="uraian" id="uraian"></textarea>
											</div>
										</div>

										<div class="form-group" id="input_lainnya">
											<label for="keterangan" class="col-md-2 control-label"> Keterangan </label>
											<div class="col-md-8">
												<textarea class="form-control" name="keterangan" id="keterangan"></textarea>
											</div>
										</div>

										<div class="col-md-10">	
											<button type="button" class="btn btn-info m-b-20 m-l-20 pull-right" id="btn_tambah_aktivitas">Tambah Aktivitas</button>
										</div>
									</div>

										

								</div>	
								<hr>
								<div class="table-responsive" style="padding: 10px">
									<span style="color: red">* Refresh dan hapus cache apabila detail kinerja tidak muncul</span>
									<table class="color-table primary-table table table-hover">
										<thead>
											<tr>
												<th>Awal</th>
												<th>Akhir</th>
												<th>Uraian</th>
												<th>Keterangan</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="body_tabel">
											
										</tbody>
									</table>
								</div>
								<br>
								<div class="col-md-12 col-sm-12 col-xs-12 m-t-30">		
									<button type="submit" class="btn btn-success m-b-20 m-l-20 pull-right simpan">Simpan Kinerja</button>
									<button type="button" class="btn btn-default pull-right" onclick="goBack()">Kembali</button>
								</div>
							</div>
							<div class="panel-footer">
								<!-- <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal-tambah">Simpan</button> -->
								<div class="clearfix"></div>
							</div>
						</div>	
						<!-- <div class="panel panel-info">
							<div class="panel-heading">  
								
							</div>
						</div> -->
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
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
	<script src="{{ ('/elearning/public/ample/js/validator.js') }}"></script>
	<!-- Clock Plugin JavaScript -->
	<script src="{{ ('/elearning/public/ample/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/elearning/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		// alert($("#jns_hadir option").hasClass("lainnya"));

		$('.select_jns_hadir').hide();
		$('.tipe-1').show();
		$('#input_lainnya').hide();

		var valuenow = $( "#tipe_hadir" ).val();
		$('.select_jns_hadir').hide();
		$('.tipe-'+valuenow).show();

		if (valuenow == 2) {
			$('#form_aktivitas').hide();
		} else {
			$('#form_aktivitas').show();
		}

		if($('select[id="jns_hadir"] :selected').hasClass('lainnya')){
			$('#input_lainnya').show();
		}

		$('#tipe_hadir').on('change', function() {
			$('.select_jns_hadir').hide();
			var value = this.value;
			if (this.value == 2) {
				$('#form_aktivitas').hide();
			} else {
				$('#form_aktivitas').show();
			}
			$('.tipe-'+value).show();
			$("#jns_hadir > #"+value+"-first").attr("selected", true);
			
		});

		$('#jns_hadir').on('change', function() {
			$('#lainnya').val("");
			if($('select[id="jns_hadir"] :selected').hasClass('lainnya')){
				$('#input_lainnya').show();
			} else {
				$('#input_lainnya').hide();
			}
		});

		$('.clockpicker').clockpicker({
			donetext: 'Done'
			, }).find('input').change(function () {
		});

		function goBack() {
		  window.history.back();
		}

		$(function () {
			jQuery('.datepicker-autoclose').datepicker({
				autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
			});

			$.ajax({ 
			method: "GET", 
			url: "/elearning/kepegawaian/getaktivitas",
			}).done(function( data ) { 
				var idemp = $('#idemp').val();
				var csrf_js_var = "{{ csrf_token() }}"
				$('#body_tabel').empty();
				var now_date = '';
				for (var i = 0; i < data.length; i++) {
					if (data[i].tipe_hadir != 2 && data[i].time1 != null && data[i].time2 != null) {
						var splittime1 = (data[i].time1).split(":");
						var time1 = splittime1[0] + ":" + splittime1[1];

						var splittime2 = (data[i].time2).split(":");
						var time2 = splittime2[0] + ":" + splittime2[1];

						var splitdate1 = (data[i].tgl_trans).split(" ");
						var splitdate2 = (splitdate1[0]).split("-");
						var date = splitdate2[2] + "-" + splitdate2[1] + "-" + splitdate2[0];

						if (now_date != date) {
							now_date = date;
							$('#body_tabel').append("<tr style='background-color: #f7fafc !important'>"+
														"<td colspan='5'><b>TANGGAL: "+date+"</b></td>"+
													"</tr>");
						}

						$('#body_tabel').append("<tr>"+
													"<td>"+time1+"</td>"+
													"<td>"+time2+"</td>"+
													"<td>"+(data[i].uraian ? data[i].uraian : '-')+"</td>"+
													"<td>"+(data[i].keterangan ? data[i].keterangan : '-')+"</td>"+
													"<td>"+
														"<input id='idemp-"+i+"' type='hidden' value='"+idemp+"'>"+
														"<input id='tgl_trans-"+i+"' type='hidden' value='"+data[i].tgl_trans+"'>"+
														"<input id='time1-"+i+"' type='hidden' value='"+data[i].time1+"'>"+
														"<button type='button' class='btn btn-danger btn-outline btn-circle m-r-5 btn_delete_aktivitas' id='"+i+"'><i class='fa fa-trash'></i></button></td>"+
													"</tr>");
					}
						
				}
			}); 

			$('#body_tabel').on('click', '.btn_delete_aktivitas', function() {

				var varidemp = $('#idemp-'+(this.id)).val();
				var vartgltrans = $('#tgl_trans-'+(this.id)).val();
				var vartime1 = $('#time1-'+(this.id)).val();

				$.ajax({ 
				type: "GET", 
				url: "/elearning/kepegawaian/form/hapusaktivitas",
				data: { idemp : varidemp, tgltrans : vartgltrans, time1 : vartime1 },
				dataType: "JSON",
				}).done(function( data ) { 
					$('#body_tabel').empty();
					$('#body_tabel').append(data);
					alert("Berhasil menghapus aktivitas");
				}); 
			});

			$('#btn_tambah_aktivitas').on('click', function () {
				var flag = 0;
				var vartipehadir = $('#tipe_hadir').val();
				var varjnshadir = $('#jns_hadir').val();
				var varlainnya = $('#lainnya').val();

				var tgltrans = $('#tgl_trans').val().split("/");
				var vartgltrans = tgltrans[2] + "-" + tgltrans[1] + "-" + tgltrans[0];
				
				var vartime1 = $('#time1').val();
				var vartime2 = $('#time2').val();
				var varuraian = $('#uraian').val();
				var varketerangan = $('#keterangan').val();

				var regexp = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
    			var correct = regexp.test(vartime1);
    			if (correct == false) {
    				alert("Masukkan format waktu HH:MM yang benar");
    				flag = 1;
    			}
    			var correct2 = regexp.test(vartime2);
    			if (correct2 == false) {
    				alert("Masukkan format waktu HH:MM yang benar");
    				flag = 1;
    			}
				
				if (vartime1 == '00:00' && vartime2 == '00:00' && varuraian == '') {
					alert("Mohon isi detail kegiatan");
					flag = 1;
				} else if (vartime1 == '00:00' && vartime2 == '00:00') {
					alert("Mohon isi waktu kegiatan");
					flag = 1;
				} else if (vartime1 == '00:00') {
					alert("Mohon isi waktu mulai kegiatan");
					flag = 1;
				} else if (vartime2 == '00:00') {
					alert("Mohon isi waktu berakhir kegiatan");
					flag = 1;
				} else if (varuraian == '') {
					alert("Mohon isi uraian kegiatan");
					flag = 1;
				}

				if (varketerangan == '') {
					varketerangan = '-';
				}

				var csrf_js_var = "{{ csrf_token() }}";

				if (flag == 0) {
					$.ajax({ 
					type: "POST", 
					url: "/elearning/kepegawaian/form/tambahaktivitas",
					data: { tgltrans : vartgltrans, time1 : vartime1, time2 : vartime2, uraian : varuraian, keterangan : varketerangan, _token : csrf_js_var, tipehadir : vartipehadir, jnshadir : varjnshadir, lainnya : varlainnya,  },
					dataType: "JSON",
					}).done(function( data ) { 
						$('#body_tabel').empty();
						$('#body_tabel').append(data);
						$('#time1').val("00:00");
						$('#time2').val("00:00");
						$('#uraian').val("");
						$('#keterangan').val("");
						alert("Berhasil menambahkan aktivitas baru");
					}); 
				}
			});

			$('.myTable').DataTable({
				"paging":   false,
				"ordering": false,
				"info":     false,
			});

		});
	</script>
@endsection