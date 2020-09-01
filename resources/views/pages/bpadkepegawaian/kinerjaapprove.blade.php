@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/laporanbmd/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
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
					<div class="panel panel-default">
						<div class="panel-heading"> Approve Kinerja </div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/laporanbmd/kepegawaian/approve kinerja">
										<div class="col-md-3">
											<select class="form-control select2" name="now_id_emp" id="now_id_emp" onchange="this.form.submit()">
												@forelse($pegawais as $pegawai)
												<option <?php if ($now_id_emp == $pegawai['id_emp']): ?> selected <?php endif ?> value="{{ $pegawai['id_emp'] }}">{{ ucwords(strtolower($pegawai['nm_emp'])) }}-{{ $pegawai['nrk_emp'] }}</option>
												@empty
												<option value="{{ $_SESSION['user_data']['id_emp'] }}">{{ ucwords(strtolower($_SESSION['user_data']['nm_emp'])) }}-{{ $_SESSION['user_data']['nrk_emp'] }}</option>
												@endforelse
											</select>
										</div>
									</form>
								</div>
								<div class="row ">
									<h3 class="text-center">tabel kinerja belum tervalidasi</h3>
									<form method="POST" action="/laporanbmd/kepegawaian/form/approvekinerja">
									@csrf
										<div class="table-responsive">
											<table class=" table table-hover color-table primary-table" >
												<thead>
													<tr>
														@if($_SESSION['user_data']['idunit'])
														@if(strlen($_SESSION['user_data']['idunit']) < 10)
														<th style="display: none;"></th>
														<th>Pilih</th>
														@endif
														@endif
														<th class="col-md-2">Nama</th>
														<th>Tanggal</th>
														<th class="col-md-6">Kehadiran</th>
														<th>Detail</th>
														<th>Status</th>
													</tr>
												</thead>
												<tbody>
												
													@foreach($laporans as $key => $laporan)
												
													<?php 
														if ($laporan['tipe_hadir'] == 1) {
															$tipe_hadir = 'Hadir';
														} elseif ($laporan['tipe_hadir'] == 2) {
															$tipe_hadir = 'Tidak Hadir';
														} elseif ($laporan['tipe_hadir'] == 3) {
															$tipe_hadir = 'DL Full';
														}

														if ($laporan['tipe_hadir_app']) {
															if ($laporan['tipe_hadir_app'] == 1) {
																$tipe_hadir_app = 'Hadir';
															} elseif ($laporan['tipe_hadir_app'] == 2) {
																$tipe_hadir_app = 'Tidak Hadir';
															} elseif ($laporan['tipe_hadir_app'] == 3) {
																$tipe_hadir_app = 'DL Full';
															}
														}
													?>

													<tr>
														@if($_SESSION['user_data']['idunit'])
														@if(strlen($_SESSION['user_data']['idunit']) < 10)
														<input type="hidden" name="idemp_{{$key}}" value="{{ $laporan['idemp'] }}">
														<input type="hidden" name="tgl_trans_{{$key}}" value="{{ $laporan['tgl_trans'] }}">
														<input type="hidden" name="tipe_hadir_{{$key}}" value="{{ $laporan['tipe_hadir'] }}">
														<input type="hidden" name="jns_hadir_{{$key}}" value="{{ $laporan['jns_hadir'] }}">
														<td style="display: none;">
															<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn_update_kinerja" data-toggle="modal" data-target="#modal-update" data-idemp="{{ $laporan['idemp'] }}" data-tgltrans="{{ date('d-m-Y', strtotime( $laporan['tgl_trans'])) }}" data-tipehadir="{{ $laporan['tipe_hadir'] }}" data-jnshadir="{{ $laporan['jns_hadir'] }}" data-lainnya="{{ $laporan['lainnya'] }}"><i class='fa fa-edit'></i></button>
														</td>
														<td style="vertical-align: middle;">
															<input class="myCheckBox" type="checkbox" name="laporan[]" id="checkbox-{{$key}}" value="{{ $key }}">
														</td>
														@endif
														@endif
														<td>
															{{ ucwords(strtolower($laporan['nm_emp'])) }}
															<br>
															<span class="text-muted">{{ $laporan['idemp'] }}</span>
														</td>
														<td>{{ date('d-m-Y', strtotime( $laporan['tgl_trans'])) }}</td>
														<td>
															{{ $tipe_hadir }}
															<!-- @if($laporan['tipe_hadir_app'])
															-> {{ $tipe_hadir_app }}
															@endif -->
															<br>
															<span class="text-muted">
																{{ $laporan['jns_hadir'] }}
																@if( $laporan['lainnya'] != '' )
																-> {{ $laporan['lainnya'] }}
																@endif
																<!-- @if($laporan['jns_hadir_app'])
																-> {{ $laporan['jns_hadir_app'] }}
																@endif -->
															</span>
														</td>
														<td>
															<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-detail" data-toggle="modal" data-target="#modal-detail" data-idemp="{{$laporan['idemp']}}" data-tgl_trans="{{$laporan['tgl_trans']}}"><i class="fa fa-eye"></i></button>
														</td>

														<td>
															@if($laporan['stat'] == 'Y')
															Approved
															@else
															Not Approved
															@endif
														</td>
													</tr>
												
												@endforeach
												
												</tbody>
											</table>
										</div>
									@if($_SESSION['user_data']['idunit'])
									@if(strlen($_SESSION['user_data']['idunit']) < 10)
									@if(count($laporans) != 0)
									<button id="confirmButton" type="submit" class="btn btn-warning">Setujui & Proses</button>
									@endif
									@endif
									@endif
									</form>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="modal-detail" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content" style="padding: 20px">
						<span style="color: red">* Refresh dan hapus cache apabila detail kinerja tidak muncul</span>
						<table class="table table-hover color-table primary-table">
							<thead>
								<tr>
									<th>Awal</th>
									<th>Akhir</th>
									<th>Uraian</th>
									<th>Kegiatan</th>
								</tr>
							</thead>
							<tbody id="detail-kinerja">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="modal-update" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/kepegawaian/form/approvekinerjasingle" class="form-horizontal" data-toggle="validator">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Kinerja</b></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="idemp" id="modal_update_idemp" value="">

								<div class="form-group">
									<label for="tgl_masuk" class="col-md-2 control-label"> Tanggal </label>
									<div class="col-md-8">
										<input type="text" class="form-control datepicker-autoclose" id="modal_update_tgl_trans" name="tgl_trans" autocomplete="off" value="">
									</div>
								</div>
								
								<div class="form-group">
									<label for="tipe" class="col-md-2 control-label"> Kehadiran </label>
									<div class="col-md-4">
										<select class="form-control tipe_hadir" name="tipe_hadir" id="modal_update_tipe_hadir" onchange="getval(this);">
											<option value="1"> Hadir </option>
											<option value="2"> Tidak Hadir </option>
											<option value="3"> DL Full </option>
										</select>
									</div>
									<div class="col-md-4">
										<select class="form-control jns_hadir" name="jns_hadir" id="modal_update_jns_hadir">
											<option class="select_jns_hadir tipe-1" id="1-first" value="Tepat Waktu (8,5 jam/hari)"> Tepat Waktu (8,5 jam/hari) </option>
											<option class="select_jns_hadir tipe-1" value="Dinas Luar Awal"> Dinas Luar Awal </option>
											<option class="select_jns_hadir tipe-1" value="Dinas Luar Akhir"> Dinas Luar Akhir </option>
											<option class="select_jns_hadir tipe-1" value="Terlambat"> Terlambat </option>
											<option class="select_jns_hadir tipe-1" value="Pulang Cepat"> Pulang Cepat </option>
											<option class="select_jns_hadir tipe-2" id="2-first" value="Sakit"> Sakit </option>
											<option class="select_jns_hadir tipe-2" value="Izin"> Izin </option>
											<option class="select_jns_hadir tipe-2" value="Cuti"> Cuti </option>
											<option class="select_jns_hadir tipe-2" value="Alpa"> Alpa </option>
											<option class="select_jns_hadir tipe-2 lainnya" value="Lainnya (sebutkan)"> Lainnya (sebutkan) </option>
											<option class="select_jns_hadir tipe-3" id="3-first" value="Rapat"> Rapat </option>
											<option class="select_jns_hadir tipe-3" value="Peninjauan Lapangan"> Peninjauan Lapangan </option>
											<option class="select_jns_hadir tipe-3 lainnya" value="Lainnya (sebutkan)"> Lainnya (sebutkan) </option>
										</select>
									</div>
								</div>

								<div class="form-group" id="input_lainnya">
									<label for="lainnya" class="col-md-2 control-label"> Lainnya </label>
									<div class="col-md-8">
										<textarea class="form-control" name="lainnya" id="modal_update_lainnya" disabled></textarea>
									</div>
								</div>

								<div class="form-group">
									<label for="lainnya" class="col-md-2 control-label"> Catatan </label>
									<div class="col-md-8">
										<textarea class="form-control" name="catatan_app"></textarea>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success pull-right">Simpan & setujui</button>
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
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		var checkBoxes = $('tbody .myCheckBox');
		checkBoxes.change(function () {
			$('#confirmButton').prop('disabled', checkBoxes.filter(':checked').length < 1);
		});
		$('tbody .myCheckBox').change();

		$('.select_jns_hadir').hide();
		$('.tipe-1').show();
		// $('#input_lainnya').hide();

		$('#modal_update_tipe_hadir').on('change', function() {
			$('.select_jns_hadir').hide();
			var value = this.value;
			if (this.value == 2) {
				$('#form_aktivitas').hide();
			} else {
				$('#form_aktivitas').show();
			}
			$('.tipe-'+value).show();
			$("#modal_update_jns_hadir > #"+value+"-first").attr("selected", true);
			
		});

		$('#modal_update_jns_hadir').on('change', function() {
			$('#modal_update_lainnya').val("");
			if($('select[id="modal_update_jns_hadir"] :selected').hasClass('lainnya')){
				$("#modal_update_lainnya").prop("disabled", false);
			} else {
				$("#modal_update_lainnya").prop("disabled", true);
			}
		});

		$(function () {
			$(".select2").select2();

			jQuery('.datepicker-autoclose').datepicker({
				autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
			});

			$("#modal-detail").on("hidden.bs.modal", function () {
				$("#detail-kinerja").html("");
			});

			$('.btn-detail').on('click', function () {
				var $el = $(this);

				var tgltrans = $el.data('tgl_trans').split(" ");
				var vartgltrans = tgltrans[0];

				var varidemp = $el.data('idemp');

				$.ajax({ 
				method: "GET", 
				url: "/laporanbmd/kepegawaian/getdetailaktivitas",
				data: { tgl_trans : vartgltrans, idemp : varidemp,  },
				}).done(function( data ) { 
					console.log(data);
					if (data[0] == null) {
						$('#detail-kinerja').append("<tr>"+
													"<td colspan='4' class='text-center'>Tidak ada Kinerja</td>"+
													"</tr>");
					} else {
						for (var i = 0; i < data.length; i++) {

							var splittime1 = (data[i].time1).split(":");
							var time1 = splittime1[0] + ":" + splittime1[1];

							var splittime2 = (data[i].time2).split(":");
							var time2 = splittime2[0] + ":" + splittime2[1];

							$('#detail-kinerja').append("<tr>"+
														"<td>"+time1+"</td>"+
														"<td>"+time2+"</td>"+
														"<td>"+(data[i].uraian ? data[i].uraian : '-')+"</td>"+
														"<td>"+(data[i].keterangan ? data[i].keterangan : '-')+"</td>"+
														"</tr>");
						}
					}
				});
			});

			

			$('.btn_update_kinerja').on('click', function () {
				var $el = $(this);

				$("#modal_update_idemp").val($el.data('idemp'));
				$("#modal_update_tgl_trans").val($el.data('tgltrans'));
				$("#modal_update_tipe_hadir").val($el.data('tipehadir'));
				$("#modal_update_jns_hadir").val($el.data('jnshadir'));
				$("#modal_update_lainnya").val($el.data('lainnya'));

				if ($el.data('lainnya') != '' && $el.data('lainnya') != null) {
					$("#modal_update_lainnya").prop("disabled", false);
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