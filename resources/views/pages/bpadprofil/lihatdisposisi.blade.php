@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/laporanbmd/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/laporanbmd/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/laporanbmd/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/laporanbmd/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
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
					<form class="form-horizontal" method="POST" action="/laporanbmd/profil/form/lihatdisposisi" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Disposisi </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">
									<div class="col-md-6">

										<input type="hidden" name="ids" value="{{ $ids }}">
										<input type="hidden" name="cekidtop" value="{{ $idtop }}">
										<input type="hidden" name="cekto_id" value="{{ $to_id }}">
										<input type="hidden" name="cekasal" value="{{ $asal_form }}">
										<input type="hidden" name="prevnoform" value="{{ $opendisposisi[0]['no_form'] }}">
										<input type="hidden" id="isEmployeeFlag" value="{{ $isEmployee }}">

										<div class="form-group">
											<label class="col-md-2 control-label"> No Form </label>
											<div class="col-md-8">
												<?php if (is_null($_SESSION['user_data']['id_emp'])) : ?>
												<input autocomplete="off" type="text" name="no_form" class="form-control" id="no_form" value="{{ $opendisposisi[0]['no_form'] }}">
												<?php else : ?>
												<p>{{ $opendisposisi[0]['no_form'] }}</p>
												<?php endif ?>
											</div>
										</div>

										<div class="form-group">
											<label for="tgl_masuk" class="col-md-2 control-label"> Tgl Masuk </label>
											<div class="col-md-8">
												<p class="form-control-static parDisp">{{ date('d-M-Y', strtotime($opendisposisi[0]['tgl_masuk'])) }}</p>
												<input type="text" name="tgl_masuk" class="form-control formDisp" id="datepicker-autoclose" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ date('d/m/Y', strtotime($opendisposisi[0]['tgl_masuk'])) }}">
											</div>
										</div>

										<div class="form-group">
											<label for="no_index" class="col-md-2 control-label"> No Index </label>
											<div class="col-md-8">
												<p class="form-control-static parDisp">
													<?php if ($opendisposisi[0]['no_index'] && $opendisposisi[0]['no_index'] != '') : ?>
														[{{ $opendisposisi[0]['no_index'] }}]
													<?php else : ?>
														[-]
													<?php endif ?>
												</p>
												<input autocomplete="off" type="text" name="no_index" class="form-control formDisp" id="no_index" value="{{ $opendisposisi[0]['no_index'] }}"> 
											</div>
										</div>

										<div class="form-group">
											<label for="kode_disposisi" class="col-md-2 control-label"> Kode Disposisi </label>
											<div class="col-md-8">
												<p class="form-control-static parDisp">[{{ $opendisposisi[0]['kd_jnssurat'] }}] - [{{ $opendisposisi[0]['nm_jnssurat'] }}]</p>
												<select class="form-control select2 formDisp" name="kode_disposisi" id="kode_disposisi">
													@foreach($kddispos as $kddispo)
														<option value="{{ $kddispo['kd_jnssurat'] }}" 
														<?php if ($opendisposisi[0]['kd_jnssurat'] == $kddispo['kd_jnssurat'] ): ?> selected <?php endif ?> > 
															[{{ $kddispo['kd_jnssurat'] }}] - [{{ $kddispo['nm_jnssurat'] }}] 
														</option>
													@endforeach
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="gelar" class="col-md-2 control-label"> Nomor & Tgl Surat </label>
											<div <?php if($isEmployee == 1){
												echo 'class="col-md-2"';
											} else {
												echo 'class="col-md-4"';
											} ?> >
												<p class="form-control-static parDisp">
													<?php if ($opendisposisi[0]['no_surat'] && $opendisposisi[0]['no_surat'] != '') : ?>
														[{{ $opendisposisi[0]['no_surat'] }}]
													<?php else : ?>
														[-]
													<?php endif ?>
												</p>
												<input autocomplete="off" type="text" name="no_surat" class="form-control formDisp" id="no_surat" placeholder="Nomor" value="{{ $opendisposisi[0]['no_surat'] }}">
											</div>
											<div class="col-md-4">
												<p class="form-control-static parDisp">
													<?php if ($opendisposisi[0]['tgl_surat'] && $opendisposisi[0]['tgl_surat'] != '') : ?>
														[{{ date('d-M-Y', strtotime(str_replace('/', '-', $opendisposisi[0]['tgl_surat']))) }}]
													<?php else : ?>
														[-]
													<?php endif ?>
												</p>
												<input type="text" name="tgl_surat" class="form-control formDisp" id="datepicker-autoclose2" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ date('d/m/Y', strtotime(str_replace('/', '-', $opendisposisi[0]['tgl_surat']))) }}">
											</div>
										</div>

										<div class="form-group">
											<label for="perihal" class="col-md-2 control-label"> Perihal </label>
											<div class="col-md-8">
												<p class="form-control-static parDisp">{{ $opendisposisi[0]['perihal'] }}</p>
												<textarea name="perihal" class="form-control formDisp" rows="3">{{ $opendisposisi[0]['perihal'] }}</textarea>

											</div>
										</div>

										<div class="form-group">
											<label for="asal_surat" class="col-md-2 control-label"> Dari </label>
											<div class="col-md-8">
												<p class="form-control-static parDisp">{{ $opendisposisi[0]['asal_surat'] }}</p>
												<input autocomplete="off" type="text" name="asal_surat" class="form-control formDisp" id="asal_surat" value="{{ $opendisposisi[0]['asal_surat'] }}">
											</div>
										</div>

										<div class="form-group">
											<label for="kepada_surat" class="col-md-2 control-label"> Kepada </label>
											<div class="col-md-8">
												<p class="form-control-static parDisp">{{ $opendisposisi[0]['kepada_surat'] }}</p>
												<input autocomplete="off" type="text" name="kepada_surat" class="form-control formDisp" id="kepada_surat" value="{{ $opendisposisi[0]['kepada_surat'] }}">
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-2 control-label"> Sifat Surat </label>
											<div <?php if($isEmployee == 1){
												echo 'class="col-md-2"';
											} else {
												echo 'class="col-md-4"';
											} ?>>
												<p class="form-control-static parDisp"><span class="label label-info">{{ $opendisposisi[0]['sifat1_surat'] }}</span></p>
												<select class="form-control formDisp" name="sifat1_surat" id="sifat1_surat">
													<option value="<?php echo NULL; ?>"> </option>
													<option value="Rahasia" <?php if ($opendisposisi[0]['sifat1_surat'] == 'Rahasia' ): ?> selected <?php endif ?>> Rahasia </option>
													<option value="Penting" <?php if ($opendisposisi[0]['sifat1_surat'] == 'Penting' ): ?> selected <?php endif ?>> Penting </option>
													<option value="Biasa" <?php if ($opendisposisi[0]['sifat1_surat'] == 'Biasa' ): ?> selected <?php endif ?>> Biasa </option>
												</select>
											</div>
											<div <?php if($isEmployee == 1){
												echo 'class="col-md-2"';
											} else {
												echo 'class="col-md-4"';
											} ?>>
												<p class="form-control-static parDisp"><span class="label label-warning">{{ $opendisposisi[0]['sifat2_surat'] }}</span></p>
												<select class="form-control formDisp" name="sifat2_surat" id="sifat2_surat">
													<option value="<?php echo NULL; ?>"> </option>
													<option value="Kilat" <?php if ($opendisposisi[0]['sifat2_surat'] == 'Kilat' ): ?> selected <?php endif ?>> Kilat </option>
													<option value="Hari Ini" <?php if ($opendisposisi[0]['sifat2_surat'] == 'Hari Ini' ): ?> selected <?php endif ?>> Hari Ini </option>
													<option value="Sangat Segera" <?php if ($opendisposisi[0]['sifat2_surat'] == 'Sangat Segera' ): ?> selected <?php endif ?>> Sangat Segera </option>
													<option value="Segera" <?php if ($opendisposisi[0]['sifat2_surat'] == 'Segera' ): ?> selected <?php endif ?>> Segera </option>
													<option value="Biasa" <?php if ($opendisposisi[0]['sifat2_surat'] == 'Biasa' ): ?> selected <?php endif ?>> Biasa </option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="ket_lain" class="col-md-2 control-label"> Keterangan </label>
											<div class="col-md-8">
												<p class="form-control-static parDisp">{{ $opendisposisi[0]['ket_lain'] }}</p>
												<textarea name="ket_lain" class="form-control formDisp" rows="3">{{ $opendisposisi[0]['ket_lain'] }}</textarea>
											</div>
										</div>

										<div class="form-group">
											<label for="ket_lain" class="col-md-2 control-label"> File Disposisi </label>
											<div class="col-md-8">
												<p class="form-control-static">
													<?php 
														$splitfile = explode("::", $opendisposisi[0]['nm_file']);
														foreach ($splitfile as $key => $file) { 
															if (file_exists('public/publicfile/disp/'.$file)) {
																$namafolder = '';
															} else {
																$namafolder = '/' . $opendisposisi[0]['no_form'];
															}
															?>
														 	<a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $file }}">{{ $file }}</a>
														 	<br>
														<?php } 
													?>
													<!-- <a target="_blank" href="{{ config('app.openfiledisposisi') }}/{{ $opendisposisi[0]['nm_file'] }}">{{ $opendisposisi[0]['nm_file'] }}</a> -->
												</p>
												<?php if ($_SESSION['user_data']['idgroup'] == 'SKPD INTERNAL'): ?>
												<span style="color: red">*untuk mengubah file, upload ulang semua file</span>
												<input type="file" class="form-control formDisp" id="nm_file" name="nm_file[]" multiple>
												<?php endif ?>
											</div>
										</div>
									</div>

									<div class="col-md-6">
										<?php if ($_SESSION['user_data']['child'] == 1 || is_null($_SESSION['user_data']['id_emp'])): ?>
											<div class="form-group">
												<label class="col-md-2 control-label"> Disposisi Ke </label>
												<div class="col-md-8">
													<select class="select2 m-b-10 select2-multiple" multiple="multiple" name="jabatans[]" id="jabatans">
														<?php if (isset(Auth::user()->usname)): ?> 
															<option  value="Kepala Badan Pengelolaan Aset Daerah" selected=""> Kepala Badan Pengelolaan Aset Daerah </option> 
														<?php else : ?>
															@foreach($jabatans as $jabatan)
																<option value="{{ $jabatan['notes'] }}||{{ $jabatan['id_emp'] }}" > {{ $jabatan['notes'] }} </option>
															@endforeach
														<?php endif ?>
														
													</select>
													@if(is_null($_SESSION['user_data']['id_emp']))
													<span style="color: red">
														*disposisi yang baru dibuat otomatis ditujukan kepada Kepala Badan 
													</span>
													@endif
												</div>
											</div>

											<?php if ($_SESSION['user_data']['child'] == 1): ?>
											<div class="form-group">
												<label for="nip_emp" class="col-md-2 control-label"> Staf </label>
												<div class="col-md-8">
													<select class="select2 m-b-10 select2-multiple" multiple="multiple" name="stafs[]" id="stafs" required>
														@foreach($stafs as $staf)
															<option value="{{ $staf['id_emp'] }}"> 
																{{ ucwords(strtolower($staf['nm_emp'])) }}
																<?php if ($staf['nrk_emp']): ?>
																	- [{{ $staf['nrk_emp'] }}]
																<?php endif ?>
															</option>
														@endforeach
													</select>
												</div>
											</div>
											<?php endif ?>
										<?php endif ?>

										<?php 
											$penanganan_final = ($openpenanganannow['child'] == 1 ? $openpenangananchild['penanganan'] : ($openpenanganannow['penanganan_final'] ? $openpenanganannow['penanganan_final'] : $openpenanganannow['penanganan'] ) );
											$catatan_final = ($openpenanganannow['child'] == 1 ? $openpenangananchild['catatan'] : ($openpenanganannow['catatan_final'] ? $openpenanganannow['catatan_final'] : $openpenanganannow['catatan'] ));
										?>

										<div class="form-group">
											<label for="tgl_join" class="col-md-2 control-label"> Penanganan </label>
											<div class="col-md-8">
												<select class="select2 form-control" name="penanganan" id="penanganan">
													@foreach($penanganans as $penanganan)
														<option value="{{ $penanganan['nm_penanganan'] }}" <?php if ($penanganan_final == $penanganan['nm_penanganan'] ): ?> selected <?php endif ?>> {{ $penanganan['nm_penanganan'] }} </option>
													@endforeach
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="catatan" class="col-md-2 control-label"> Catatan </label>
											<div class="col-md-8">
												<!-- <textarea name="-catatan-" class="form-control" rows="3">{{ $catatan_final }}</textarea> -->
												<textarea name="catatan" class="form-control" rows="3"></textarea>
											</div>
										</div>

										<!-- <?php if (!($_SESSION['user_data']['idgroup'] == 'SKPD INTERNAL')): ?>
										<div class="form-group">
											<label for="nm_tambahan" class="col-lg-2 control-label"> File Tambahan <br> </label>
											<div class="col-lg-8">
												<input type="file" class="form-control" id="nm_tambahan" name="nm_tambahan">
											</div>
										</div>
										<?php endif ?> -->

										<div class="form-group">
											<div class="col-md-2"></div>
											<div class="col-md-8">
												<div class="panel panel-info">
													<div class="panel-heading"> Penanganan Sebelumnya </div>
													<div class="panel-wrapper collapse in" aria-expanded="true">
														<div class="panel-body">
															<div class="table-responsive">
																<table>
																	<tr>
																		<td>
																			<b>{{ $openpenanganannow['penanganan'] ?? '-' }}</b><br>
																			<span class="text-muted">{{ $openpenanganannow['catatan'] ?? '-' }}</span>
																		</td>
																	</tr>
																</table>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
												

										<div class="form-group">
											<div class="col-md-2"></div>
											<div class="col-md-10">
												
												<label > Log <br> </label>
												<div class="table-responsive">
													<table>
														<tbody>
															{!! $treedisp !!}
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
									<!-- <div class="sttabs tabs-style-underline">
										<nav>
											<ul>
												<li><a href="#section-underline-1" class="sticon ti-book"><span>Identitas</span></a></li>
												<li><a href="#section-underline-2" class="sticon ti-camera"><span>Pendidikan</span></a></li>
											</ul>
										</nav>
										<div class="content-wrap">
											<section id="section-underline-1">
											</section>
											<section id="section-underline-2">
											</section>
										</div>
									</div> -->
									
								</div>
							</div>
							<div class="panel-footer">
								<?php if (is_null($_SESSION['user_data']['id_emp']) && $opendisposisi[0]['status_surat'] == 'd'): ?>
									<input type="submit" name="btnKirim" class="btn btn-info pull-right m-r-10" value="Kirim">
									<input type="submit" name="btnDraft" class="btn btn-warning pull-right m-r-10" value="Draft">

								<?php else : ?>
									<button type="submit" class="btn btn-success pull-right">Simpan</button>
								<?php endif ?>
								
								<button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button>
								<!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
								<div class="clearfix"></div>
							</div>
						</div>	
						<div class="panel panel-info">
							<div class="panel-heading">  
								
							</div>
						</div>
					</form>
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
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/js/cbpFWTabs.js') }}"></script>
	<script type="text/javascript">
		(function () {
				[].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
				new CBPFWTabs(el);
			});
		})();
	</script>
	<script src="{{ ('/laporanbmd/public/ample/js/custom.min.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/ample/js/validator.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		function goBack() {
		  window.history.back();
		}
		$(function () {
			if ($("#isEmployeeFlag").val() == 1) {
				$(".formDisp").hide();
			} else {
				$(".parDisp").hide();
			}
			$(".select2").select2();

			jQuery('#datepicker-autoclose').datepicker({
				autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
			});

			jQuery('#datepicker-autoclose2').datepicker({
				autoclose: true
				, todayHighlight: true
				, format: 'dd/mm/yyyy'
			});

			$("#no_form").on('change', function(){

				var varnoform = $("#no_form").val();

				$.ajax({ 
				type: "GET", 
				url: "/laporanbmd/profil/ceknoform",
				data: { noform : varnoform },
				dataType: "JSON",
				}).done(function( data ) { 
					console.log(data[0].total);
					if (data[0].total > 0) {
						alert("Nomor surat sudah terpakai");
					}
				}); 
			});

		});
	</script>
@endsection