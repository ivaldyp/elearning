@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/elearning/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- animation CSS -->
	<link href="{{ ('/elearning/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/elearning/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/elearning/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<!-- page CSS -->
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />


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
					<form class="form-horizontal" method="POST" action="/elearning/kepegawaian/form/ubahsuratkeluar" data-toggle="validator" enctype="multipart/form-data">
					@csrf
						<div class="panel panel-info">
							<div class="panel-heading"> Ubah Surat Keluar </div>
							<div class="panel-wrapper collapse in" aria-expanded="true">
								<div class="panel-body">

									<input type="hidden" name="ids" value="{{ $surat['ids'] }}">

									<div class="form-group">
										<label for="tgl_masuk" class="col-md-2 control-label"> SKPD </label>
										<div class="col-md-8">
											<p class="form-control-static">1.20.512 - Badan Pengelola Aset Daerah</p>
										</div>
									</div>

									<div class="form-group">
										<label for="kode_disposisi" class="col-md-2 control-label"> No Disposisi </label>
										<div class="col-md-8">
											<select class="form-control select2" name="no_form_in" id="no_form_in">
												@foreach($disposisis as $disp)
													<option value="{{ $disp['no_form'] }}" <?php if ($surat['no_form_in'] == $disp['no_form'] ): ?> selected <?php endif ?>> [{{ $disp['no_form'] }}] - [{{ date('d-M-Y', strtotime($disp['tgl'])) }}] - [{{ $disp['perihal'] }}] </option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="tgl_masuk" class="col-md-2 control-label"> Tgl Terima </label>
										<div class="col-md-8">
											<input type="text" name="tgl_terima" class="form-control" id="datepicker-autoclose" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ date('d/m/Y', strtotime(str_replace('/', '-', $surat['tgl_terima']))) }}">
										</div>
									</div>

									<div class="form-group">
										<label for="kode_disposisi" class="col-md-2 control-label"> Kode Disposisi </label>
										<div class="col-md-8">
											<select class="form-control select2" name="kode_disposisi" id="kode_disposisi">
												@foreach($dispkodes as $kode)
													<option value="{{ $kode['kd_jnssurat'] }}" <?php if ($surat['kode_disposisi'] == $kode['kd_jnssurat'] ): ?> selected <?php endif ?> > [{{ $kode['kd_jnssurat'] }}] - [{{ $kode['nm_jnssurat'] }}] </option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="asal_surat" class="col-md-2 control-label"> Dari </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="asal_surat" class="form-control" id="asal_surat" value="{{ $surat['asal_surat'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="asal_surat" class="col-md-2 control-label"> Kepada </label>
										<div class="col-md-8">
											<input autocomplete="off" type="text" name="kepada" class="form-control" id="kepada" value="{{ $surat['kepada'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="tgl_masuk" class="col-md-2 control-label"> Tgl & No Surat </label>
										<div class="col-md-4">
											<input type="text" name="tgl_surat" class="form-control" id="datepicker-autoclose2" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ date('d/m/Y', strtotime(str_replace('/', '-', $surat['tgl_surat']))) }}">
										</div>
										<div class="col-md-4">
											<input type="text" name="no_surat" class="form-control" autocomplete="off" placeholder="No Surat" value="{{ $surat['no_surat'] }}">
										</div>
									</div>

									<div class="form-group">
										<label for="perihal" class="col-md-2 control-label"> Perihal </label>
										<div class="col-md-8">
											<textarea name="perihal" class="form-control" rows="3">{{ $surat['perihal'] }}</textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="perihal" class="col-md-2 control-label"> Keterangan </label>
										<div class="col-md-8">
											<textarea name="ket_lain" class="form-control" rows="3">{{ $surat['ket_lain'] }}</textarea>
										</div>
									</div>

                                    <div class="form-group">
                                        <label for="nm_file" class="col-lg-2 control-label"> Unduh File </label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static">
												<a target="_blank" href="{{ config('app.openfilesuratkeluar') }}/{{ $surat['nm_file'] }}"><i class="fa fa-download"></i> {{ $surat['nm_file'] }}</a>
											</p>
                                        </div>
                                    </div>

									<div class="form-group">
                                        <label for="nm_file" class="col-lg-2 control-label"> Ubah file <br> <span class="text-muted" style="font-size: 10px">Dapat dikosongkan</span> </label>
                                        <div class="col-lg-8">
                                            <input type="file" class="form-control" id="nm_file" name="nm_file">
                                        </div>
                                    </div>
								</div>
							</div>
							<div class="panel-footer">
                                <button type="submit" class="btn btn-success pull-right">Simpan</button>
                                <!-- <button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Kembali</button> -->
                                <button type="button" class="btn btn-default pull-right m-r-10" onclick="goBack()">Kembali</button>
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
	<script src="{{ ('/elearning/public/ample/js/validator.js') }}"></script>
	<script src="{{ ('/elearning/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/elearning/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

	<script>
		function goBack() {
		  window.history.back();
		}

		$(function () {
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
		});
	</script>
@endsection