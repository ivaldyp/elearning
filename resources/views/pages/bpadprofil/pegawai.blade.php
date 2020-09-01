@extends('layouts.masterhome')

@section('css')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/laporanbmd/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Menu CSS -->
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<!-- xeditable css -->
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css') }}" rel="stylesheet" />
	<!-- animation CSS -->
	<link href="{{ ('/laporanbmd/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/laporanbmd/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/laporanbmd/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
	<!-- Date picker plugins css -->
	<link href="{{ ('/laporanbmd/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />

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
				<div class="col-md-4">
					<div class="white-box">
						<div class="user-bg bg-default"> 
							<div class="overlay-box">
								<div class="user-content">
									<!-- <?php if ($emp_data['foto'] && $emp_data['foto'] != '') : ?>
										<?php if ($emp_data['tampilnew'] == 1) : ?>
											<img src="/laporanbmd/public/publicimg/{{ $emp_data['foto'] }}" style="height: 150%" class="thumb-lg img-circle" alt="img">
										<?php else : ?>
											<img src="http://bpad.jakarta.go.id/images/emp/{{ $emp_data['foto'] }}" style="height: 150%" class="thumb-lg img-circle" alt="img">
										<?php endif ?>
									<?php endif ?> -->
									<?php if ($emp_data	['foto'] && $emp_data['foto'] != '') : ?>
										<img src="{{ config('app.openfileimg') }}/{{ $emp_data['foto'] }}" style="height: 100%; width: 20%" class="thumb-lg img-circle" alt="img">
									<?php else : ?>
										<img src="{{ config('app.openfileimgdefault') }}" style="height: 100%; width: 30%" class="thumb-lg img-circle" alt="img">
									<?php endif ?>
								</div>
							</div>
						</div>
						<div class="user-btm-box" style="text-align: center;">
							<h1>
								{{ ucwords(strtolower($emp_data['nm_emp'])) }}
							</h1>
							<h3><strong>
								{{ ucwords(strtolower($emp_jab[0]['unit']['nm_unit'])) }}
							</strong></h3>
						</div>
						<form method="POST" action="/laporanbmd/profil/form/ubahidpegawai" data-toggle="validator" enctype="multipart/form-data">
						@csrf
						<div class="user-btm-box" style="text-align: center;">
							<div class="col-md-6 text-center row-in-br">
								<p class="text-blue"><i style="font-size: 30px;" class="mdi mdi-phone"></i></p>
								<h5 class="data-show">{{ $emp_data['tlp_emp'] }}</h5> 
								<input class="form-control data-input" type="text" name="tlp_emp" value="{{ $emp_data['tlp_emp'] }}" placeholder="email" autocomplete="off">
							</div>
							<div class="col-md-6 text-center">
								<p class="text-blue"><i style="font-size: 30px;" class="mdi mdi-email-outline"></i></p>
								<h5 class="data-show">{{ $emp_data['email_emp'] }}</h5> 
								<input class="form-control data-input" type="text" name="email_emp" value="{{ $emp_data['email_emp'] }}" placeholder="email" autocomplete="off">
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="white-box">
						<ul class="nav customtab nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#tabs1" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs">Id</span><span class="hidden-xs"> Identitas </span></a></li>
							<li role="presentation" class=""><a href="#tabs2" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">Dik</span> <span class="hidden-xs"> Pendidikan </span></a></li>
							<li role="presentation" class=""><a href="#tabs3" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">Gol</i></span> <span class="hidden-xs">Golongan</span></a></li>
							<li role="presentation" class=""><a href="#tabs4" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs">Jab</span> <span class="hidden-xs">Jabatan</span></a></li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade active in" id="tabs1">
									<div class="panel-group" id="exampleAccordionDefault" aria-multiselectable="true" role="tablist">
										<div class="panel">
											<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultOne" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultOne" data-parent="#exampleAccordionDefault" aria-expanded="true" aria-controls="exampleCollapseDefaultOne"> Nomor ID </a> </div>
											<div class="panel-collapse collapse in" id="exampleCollapseDefaultOne" aria-labelledby="exampleHeadingDefaultOne" role="tabpanel">
												<div class="table-responsive">
													<table class="table table-hover">
														<tr>
															<td class="col-md-6 p-l-30"><h4>ID</h4></td>
															<td class="col-md-6" style="vertical-align: middle;">
															<h4 class="text-muted">{{ $emp_data['id_emp'] }}</h4></td>
															<input class="form-control" type="hidden" name="id_emp" value="{{ $emp_data['id_emp'] }}">
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>NIP</h4></td>
															<td class="col-md-6" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['nip_emp'] }}</h4></td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>NRK</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['nrk_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="nrk_emp" value="{{ $emp_data['nrk_emp'] }}" placeholder="NRK" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>TMT</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ date('d-M-Y',strtotime($emp_data['tgl_join'])) }}</h4></td>
															<td class="col-md-6 data-input">
																<input id="datepicker-autoclose2" class="form-control" type="text" name="tgl_join" value="{{ date('d/m/Y', strtotime($emp_data['tgl_join'])) }}" placeholder="Tanggal Lahir" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Status</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['status_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="status_emp" id="status_emp">
																	@foreach($statuses as $status)
																		<option value="{{ $status['status_emp'] }}"  
																			<?php if ($emp_data['status_emp'] == $status['status_emp']): ?>
																				selected
																			<?php endif ?>
																		> {{ $status['status_emp'] }} </option>
																	@endforeach
																</select>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<div class="panel">
											<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultTwo" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultTwo" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultTwo"> Data Diri </a> </div>
											<div class="panel-collapse collapse" id="exampleCollapseDefaultTwo" aria-labelledby="exampleHeadingDefaultTwo" role="tabpanel">
												<div class="table-responsive">
													<table class="table table-hover">
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nama</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['gelar_dpn']) : ?>
																	{{ $emp_data['gelar_dpn'] }}
																<?php endif ?>
																<!-- <span class="inline_edit_id" id="inline-nm_emp" data-type="text" data-id="{{ $emp_data['id_emp'] }}" data-title="Enter username">{{ ucwords(strtolower($emp_data['nm_emp'])) }}</span> -->
																{{ ucwords(strtolower($emp_data['nm_emp'])) }}

																<?php if ($emp_data['gelar_blk']) : ?>
																	<!-- <span class="inline_edit_id" id="inline-gelar_blk" data-type="text" data-id="{{ $emp_data['id_emp'] }}" data-title="Enter username">{{ $emp_data['gelar_blk'] }}</span> -->
																	{{ $emp_data['gelar_blk'] }}
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<div class="col-md-3">
																	<input class="form-control" type="text" name="gelar_dpn" value="{{ $emp_data['gelar_dpn'] }}" placeholder="Depan" autocomplete="off">
																</div>
																<div class="col-md-6">
																	<input class="form-control" type="text" name="nm_emp" value="{{ $emp_data['nm_emp'] }}" placeholder="Nama" autocomplete="off">
																</div>
																<div class="col-md-3">
																	<input class="form-control" type="text" name="gelar_blk" value="{{ $emp_data['gelar_blk'] }}" placeholder="Belakang" autocomplete="off">
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Jenis Kelamin</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['jnkel_emp'] == 'L') : ?>
																	Laki-Laki
																<?php else : ?>
																	Perempuan
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<div class="radio-list col-md-8">
																	<label class="radio-inline">
																		<div class="radio radio-info">
																			<input type="radio" name="jnkel_emp" id="kel1" value="L" data-error="Pilih salah satu" required checked>
																			<label for="kel1">Laki-laki</label> 
																		</div>
																	</label>
																	<label class="radio-inline">
																		<div class="radio radio-info">
																			<input type="radio" name="jnkel_emp" id="kel2" value="P" 
																				<?php if ($emp_data['jnkel_emp'] == "P"): ?>
																					checked
																				<?php endif ?>
																			>
																			<label for="kel2">Perempuan</label>
																		</div>
																	</label>
																	<div class="help-block with-errors"></div>  
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Tempat, Tgl Lahir</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['tempat_lahir'] }}, {{ date('d-M-Y',strtotime($emp_data['tgl_lahir'])) }}</h4></td>
															<td class="col-md-6 data-input">
																<div class="col-md-6">
																	<input class="form-control" type="text" name="tempat_lahir" value="{{ $emp_data['tempat_lahir'] }}" placeholder="Tempat" autocomplete="off">
																</div>
																<div class="col-md-6">
																	<input id="datepicker-autoclose" class="form-control" type="text" name="tgl_lahir" value="{{ date('d/m/Y', strtotime($emp_data['tgl_lahir'])) }}" placeholder="Tanggal Lahir" autocomplete="off">
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Agama</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['idagama'] == 'A') : ?>
																	Islam
																<?php elseif ($emp_data['idagama'] == 'B') : ?>
																	Katolik
																<?php elseif ($emp_data['idagama'] == 'C') : ?>
																	Protestan
																<?php elseif ($emp_data['idagama'] == 'D') : ?>
																	Budha
																<?php elseif ($emp_data['idagama'] == 'E') : ?>
																	Hindu
																<?php elseif ($emp_data['idagama'] == 'F') : ?>
																	Lainnya
																<?php elseif ($emp_data['idagama'] == 'G') : ?>
																	Konghucu
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="idagama" id="idagama">
																	<option value="A" <?php if ($emp_data['idagama'] == "A"): ?> selected <?php endif ?> >Islam </option>
																	<option value="B" <?php if ($emp_data['idagama'] == "B"): ?> selected <?php endif ?> > Katolik </option>
																	<option value="C" <?php if ($emp_data['idagama'] == "C"): ?> selected <?php endif ?> > Protestan </option>
																	<option value="D" <?php if ($emp_data['idagama'] == "D"): ?> selected <?php endif ?> > Budha </option>
																	<option value="E" <?php if ($emp_data['idagama'] == "E"): ?> selected <?php endif ?> > Hindu </option>
																	<option value="F" <?php if ($emp_data['idagama'] == "F"): ?> selected <?php endif ?> > Lainnya </option>
																	<option value="G" <?php if ($emp_data['idagama'] == "G"): ?> selected <?php endif ?> > Konghucu </option>
																</select>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Alamat</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['alamat_emp'] }}</h4></td>
															<td class="col-md-6 data-input">
																<textarea class="form-control" name="alamat_emp" placeholder="Alamat" autocomplete="off">{{ $emp_data['alamat_emp'] }}</textarea>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Status Perkawinan</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['status_nikah'] }}</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="status_nikah" id="status_nikah">
																	<option value="Belum Kawin" <?php if ($emp_data['status_nikah'] == "Belum Kawin"): ?> selected <?php endif ?> > Belum Kawin </option>
																	<option value="Kawin" <?php if ($emp_data['status_nikah'] == "Kawin"): ?> selected <?php endif ?> > Kawin </option>
																	<option value="Cerai Hidup" <?php if ($emp_data['status_nikah'] == "Cerai Hidup"): ?> selected <?php endif ?> > Cerai Hidup </option>
																	<option value="Cerai Mati" <?php if ($emp_data['status_nikah'] == "Cerai Mati"): ?> selected <?php endif ?> > Cerai Mati </option>
																</select>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Golongan Darah</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">{{ $emp_data['gol_darah'] }}</h4></td>
															<td class="col-md-6 data-input">
																<select class="form-control" name="gol_darah" id="gol_darah">
																	<option value="A" <?php if ($emp_data['gol_darah'] == "A"): ?> selected <?php endif ?> > A </option>
																	<option value="B" <?php if ($emp_data['gol_darah'] == "B"): ?> selected <?php endif ?> > B </option>
																	<option value="AB" <?php if ($emp_data['gol_darah'] == "AB"): ?> selected <?php endif ?> > AB </option>
																	<option value="O" <?php if ($emp_data['gol_darah'] == "O"): ?> selected <?php endif ?> > O </option>
																</select>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<div class="panel">
											<div class="panel-heading" style="background-color: #edf1f5" id="exampleHeadingDefaultThree" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultThree" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultThree"> Nomor Penting </a> </div>
											<div class="panel-collapse collapse" id="exampleCollapseDefaultThree" aria-labelledby="exampleHeadingDefaultThree" role="tabpanel">
												<div class="table-responsive">
													<table class="table table-hover">
														<tr>
															<td class="col-md-6 p-l-30"><h4>Bank</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if (($emp_data['nm_bank'] && $emp_data['nm_bank'] != '') || ($emp_data['cb_bank'] && $emp_data['cb_bank'] != '')) : ?>
																	{{ $emp_data['nm_bank'] }} {{ $emp_data['cb_bank'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<div class="col-md-6">
																	<input class="form-control" type="text" name="nm_bank" value="{{ $emp_data['nm_bank'] }}" placeholder="Nama Bank" autocomplete="off">
																</div>
																<div class="col-md-6">
																	<input class="form-control" type="text" name="cb_bank" value="{{ $emp_data['cb_bank'] }}" placeholder="Cabang Bank" autocomplete="off">
																</div>
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nama Rekening</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['an_bank'] && $emp_data['an_bank'] != '') : ?>
																	{{ $emp_data['an_bank'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="an_bank" value="{{ $emp_data['an_bank'] }}" placeholder="Nama Rekening" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nomor Rekening</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['nr_bank'] && $emp_data['nr_bank'] != '') : ?>
																	{{ $emp_data['nr_bank'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="nr_bank" value="{{ $emp_data['nr_bank'] }}" placeholder="Nomor Rekening" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nomor Taspen</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['no_taspen'] && $emp_data['no_taspen'] != '') : ?>
																	{{ $emp_data['no_taspen'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="no_taspen" value="{{ $emp_data['no_taspen'] }}" placeholder="Nomor Taspen" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>NPWP</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['npwp'] && $emp_data['npwp'] != '') : ?>
																	{{ $emp_data['npwp'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="npwp" value="{{ $emp_data['npwp'] }}" placeholder="NPWP" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>Nomor Askes</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['no_askes'] && $emp_data['no_askes'] != '') : ?>
																	{{ $emp_data['no_askes'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="no_askes" value="{{ $emp_data['no_askes'] }}" placeholder="Nomor Askes" autocomplete="off">
															</td>
														</tr>
														<tr>
															<td class="col-md-6 p-l-30"><h4>BPJS</h4></td>
															<td class="col-md-6 data-show" style="vertical-align: middle;"><h4 class="text-muted">
																<?php if ($emp_data['no_jamsos'] && $emp_data['no_jamsos'] != '') : ?>
																	{{ $emp_data['no_jamsos'] }}
																<?php else : ?>
																	-
																<?php endif ?>
															</h4></td>
															<td class="col-md-6 data-input">
																<input class="form-control" type="text" name="no_jamsos" value="{{ $emp_data['no_jamsos'] }}" placeholder="Nomor Jamsostek" autocomplete="off">
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
									</div>
									<div class="data-input">
										<h4>Ubah Foto <span class="text-danger" style="font-size: 12px">Hanya berupa JPG, JPEG, dan PNG</span> </h4>
										<input type="file" name="filefoto">
									</div>
									<button class="btn btn-success pull-right data-input" type="submit">Simpan</button>
									<button class="btn btn-info pull-right btn-edit-id m-r-10" type="button">Ubah</button>
									<div class="clearfix"></div>
								</form>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs2">
								@if ($accessdik['zadd'] == 'y')
								<button class="btn btn-info m-b-20 btn-insert-dik" type="button" data-toggle="modal" data-target="#modal-insert-dik">Tambah</button>
								@endif
								<div class="table-responsive">
									<table class="table table-hover manage-u-table">
										<tbody>
											@foreach($emp_dik as $key => $dik)
												@if ($dik['iddik'] != 'NA')
												<tr>
													<td>
														<h1>{{ $dik['iddik'] }}</h1>
													</td>

													<td style="vertical-align: middle;">
														<strong>{{ $dik['prog_sek'] }} {{ $dik['th_sek'] }}</strong>
														<br>{{ $dik['nm_sek'] }}
													</td>

													<td style="vertical-align: middle;">
														<?php if ($dik['no_sek']) : ?>
															<strong>No. {{ $dik['no_sek'] }}</strong>
														<?php endif ?>
														
														<?php if ($dik['gambar'] && $dik['gambar'] != '') : ?> 
															<br><a target="_blank" href="{{ config('app.openfileimg') }}/{{ Auth::user()->id_emp }}/{{ $dik['gambar'] }}">[File Ijazah]</a>
														<?php else : ?>
															<br>[Tidak ada ijazah]
														<?php endif ?>
													</td>

													@if ($accessdik['zupd'] == 'y' || $accessdik['zdel'] == 'y')
													<td style="vertical-align: middle;">
														@if ($accessdik['zupd'] == 'y')
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5 btn-update-dik" data-toggle="modal" data-target="#modal-update-dik" 
															data-ids="{{$dik['ids']}}"
															data-noid="{{$dik['noid']}}"
															data-iddik="{{$dik['iddik']}}"
															data-prog_sek="{{$dik['prog_sek']}}"
															data-no_sek="{{$dik['no_sek']}}"
															data-th_sek="{{$dik['th_sek']}}"
															data-nm_sek="{{$dik['nm_sek']}}"
															data-gelar_dpn_sek="{{$dik['gelar_dpn_sek']}}"
															data-gelar_blk_sek="{{$dik['gelar_blk_sek']}}"
															data-ijz_cpns="{{$dik['ijz_cpns']}}"
														><i class="ti-pencil-alt"></i></button>
														@endif
														@if ($accessdik['zdel'] == 'y')
														<button type="button" class="btn btn-danger btn-delete-dik btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-dik"
															data-ids="{{$dik['ids']}}"
															data-noid="{{$dik['noid']}}"
															data-iddik="{{$dik['iddik']}}"
														><i class="ti-trash"></i></button>
														@endif
													</td>
													@endif
												</tr>
												@endif
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="clearfix"></div>	
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs3">
								@if ($accessgol['zadd'] == 'y')
								<button class="btn btn-info m-b-20" type="button" data-toggle="modal" data-target="#modal-insert-gol">Tambah</button>
								@endif
								<div class="table-responsive">
									<table class="table table-hover manage-u-table">
										<tbody>
											@foreach($emp_gol as $key => $gol)
												<tr>
													@if (count($emp_gol) > 1)
													<td>
														<h1>{{ $key + 1 }}</h1>
													</td>
													@endif
													<td style="vertical-align: middle;">
														<strong>{{ $gol['idgol'] }}</strong>
														<br>{{ $gol['gol']['nm_pangkat'] }}
													</td>

													<?php if ($gol['tmt_gol']) : ?>
														<td style="vertical-align: middle;">
															<strong>TMT</strong>
															<br>{{ date('d-M-Y',strtotime($gol['tmt_gol'])) }}
														</td>
													<?php endif ?>

													<!-- <?php if ($gol['gambar'] && $gol['gambar'] != '') : ?> 
														<?php if ($gol['gambar'] == 1) : ?>
															<td style="vertical-align: middle;">
																<strong>File</strong>
																<br><a target="_blank" href="/laporanbmd/public/publicimg/{{ $gol['gambar'] }}"></a>
															</td>
														<?php else : ?>
															<td style="vertical-align: middle;">
																<strong>File</strong>
																<br><a target="_blank" href="http://bpad.jakarta.go.id/images/emp/{{ Auth::user()->id_emp }}/{{ $gol['gambar'] }}">Link SK Golongan</a>
															</td>
														<?php endif ?>
													<?php endif ?> -->

													<?php if ($gol['gambar'] && $gol['gambar'] != '' && $gol['tampilnew'] == 1) : ?> 
														<td style="vertical-align: middle;">
															<strong>File</strong>
															<br><a target="_blank" href="/laporanbmd/public/publicimg/gol/{{ $gol['gambar'] }}"></a>
														</td>
													<?php else : ?>
														<td style="vertical-align: middle;">
															<strong>File</strong>
															<br>[Tidak ada SK Gol]
														</td>
													<?php endif ?>

													@if ($accessgol['zupd'] == 'y' || $accessgol['zdel'] == 'y')
													<td style="vertical-align: middle;">
														@if ($accessgol['zupd'] == 'y')
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5"><i class="ti-pencil-alt"></i></button>
														@endif
														@if ($accessgol['zdel'] == 'y')
														<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5"><i class="ti-trash"></i></button>
														@endif
													</td>
													@endif
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="clearfix"></div>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tabs4">
								@if ($accessjab['zadd'] == 'y')
								<button class="btn btn-info m-b-20" type="button" data-toggle="modal" data-target="#modal-insert-jab">Tambah</button>
								@endif
								<div class="table-responsive">
									<table class="table table-hover manage-u-table">
										<tbody>
											@foreach($emp_jab as $key => $jab)
												<tr>
													@if (count($emp_jab) > 1)
													<td>
														<h1>{{ $key + 1 }}</h1>
													</td>
													@endif
													<td style="vertical-align: middle;">
														<strong>{{ ucwords(strtolower($jab['unit']['nm_unit'])) }}</strong>
														<br>{!! wordwrap($jab['idjab'], 50, "<br>\n", TRUE) !!}
													</td>

													<td style="vertical-align: middle;">
														<strong>Lokasi</strong>
														<br>{{ $jab['lokasi']['nm_lok'] }}
													</td>

													<?php if ($jab['tmt_jab']) : ?>
														<td style="vertical-align: middle;">
															<strong>TMT</strong>
															<br>{{ date('d-M-Y',strtotime($jab['tmt_jab'])) }}
														</td>
													<?php endif ?>

													<!-- <?php if ($jab['gambar'] && $jab['gambar'] != '') : ?> 
														<?php if ($jab['gambar'] == 1) : ?>
															<td style="vertical-align: middle;">
																<strong>File</strong>
																<br><a target="_blank" href="/laporanbmd/public/publicimg/{{ $jab['gambar'] }}"></a>
															</td>
														<?php else : ?>
															<td style="vertical-align: middle;">
																<strong>File</strong>
																<br><a target="_blank" href="http://bpad.jakarta.go.id/images/emp/{{ Auth::user()->id_emp }}/{{ $jab['gambar'] }}">Link SK Jabatan</a>
															</td>
														<?php endif ?>
													<?php endif ?> -->

													<?php if ($jab['gambar'] && $jab['gambar'] != '' && $jab['tampilnew'] == 1) : ?> 
														<td style="vertical-align: middle;">
															<strong>File</strong>
															<br><a target="_blank" href="/laporanbmd/public/publicimg/jab/{{ $jab['gambar'] }}"></a>
														</td>
													<?php else : ?>
														<td style="vertical-align: middle;">
															<strong>File</strong>
															<br>[Tidak ada SK Jab]
														</td>
													<?php endif ?>

													@if ($accessjab['zupd'] == 'y' || $accessjab['zdel'] == 'y')
													<td style="vertical-align: middle;">
														@if ($accessjab['zupd'] == 'y')
														<button type="button" class="btn btn-info btn-outline btn-circle m-r-5"><i class="ti-pencil-alt"></i></button>
														@endif
														@if ($accessjab['zdel'] == 'y')
														<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5"><i class="ti-trash"></i></button>
														@endif
													</td>
													@endif
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="modal-insert-dik" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/profil/form/tambahdikpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Pendidikan</b></h4>
							</div>
							<div class="modal-body">

								<div class="form-group">
									<label for="iddik" class="col-md-3 control-label"> Pendidikan Terakhir </label>
									<div class="col-md-9">
										<select class="form-control" name="iddik" id="modal_insert_dik_iddik">
											@foreach($pendidikans as $pendidikan)
												<option value="{{ $pendidikan['dik'] }}"> {{ $pendidikan['nm_dik'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="prog_sek" class="col-md-3 control-label"> Program Studi </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="prog_sek" class="form-control" id="modal_insert_dik_prog_sek">
									</div>
								</div>

								<div class="form-group">
									<label for="nm_sek" class="col-md-3 control-label"> Nama Lembaga </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_sek" class="form-control" id="modal_insert_dik_nm_sek">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label"> Nomor / Tahun Ijazah </label>
									<div class="col-md-6">
										<input autocomplete="off" type="text" name="no_sek" class="form-control" id="modal_insert_dik_no_sek" placeholder="Nomor Ijazah">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="th_sek" class="form-control" id="modal_insert_dik_th_sek" placeholder="Tahun">
									</div>
								</div>

								<div class="form-group">
									<label for="gelar" class="col-md-3 control-label"> Gelar </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_dpn_sek" class="form-control" id="modal_insert_dik_gelar_dpn_sek" placeholder="Depan">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_blk_sek" class="form-control" id="modal_insert_dik_gelar_blk_sek" placeholder="Belakang">
									</div>
								</div>

								<div class="form-group">
									<label for="ijz_cpns" class="col-md-3 control-label"> Ijazah </label>
									<div class="col-md-9">
										<select class="form-control" name="ijz_cpns" id="modal_insert_dik_ijz_cpns">
											<option value="Y"> Ada </option>
											<option value="T"> Tidak </option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="fileijazah" class="col-lg-3 control-label"> Upload Ijazah <br> <span style="font-size: 10px">Hanya berupa JPG, JPEG, dan PNG</span> </label>
									<div class="col-lg-9">
										<input type="file" class="form-control" id="modal_insert_dik_fileijazah" name="fileijazah">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="modal-update-dik" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/laporanbmd/profil/form/ubahdikpegawai" class="form-horizontal" enctype="multipart/form-data">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Ubah Pendidikan</b></h4>
							</div>
							<div class="modal-body">
								
								<input type="hidden" name="ids" id="modal_update_dik_ids">
								<input type="hidden" name="noid" id="modal_update_dik_noid">

								<div class="form-group">
									<label for="iddik" class="col-md-3 control-label"> Pendidikan Terakhir </label>
									<div class="col-md-9">
										<select class="form-control" name="iddik" id="modal_update_dik_iddik">
											@foreach($pendidikans as $pendidikan)
												<option value="{{ $pendidikan['dik'] }}"> {{ $pendidikan['nm_dik'] }} </option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="prog_sek" class="col-md-3 control-label"> Program Studi </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="prog_sek" class="form-control" id="modal_update_dik_prog_sek">
									</div>
								</div>

								<div class="form-group">
									<label for="nm_sek" class="col-md-3 control-label"> Nama Lembaga </label>
									<div class="col-md-9">
										<input autocomplete="off" type="text" name="nm_sek" class="form-control" id="modal_update_dik_nm_sek">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label"> Nomor / Tahun Ijazah </label>
									<div class="col-md-6">
										<input autocomplete="off" type="text" name="no_sek" class="form-control" id="modal_update_dik_no_sek" placeholder="Nomor Ijazah">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="th_sek" class="form-control" id="modal_update_dik_th_sek" placeholder="Tahun">
									</div>
								</div>

								<div class="form-group">
									<label for="gelar" class="col-md-3 control-label"> Gelar </label>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_dpn_sek" class="form-control" id="modal_update_dik_gelar_dpn_sek" placeholder="Depan">
									</div>
									<div class="col-md-3">
										<input autocomplete="off" type="text" name="gelar_blk_sek" class="form-control" id="modal_update_dik_gelar_blk_sek" placeholder="Belakang">
									</div>
								</div>

								<div class="form-group">
									<label for="ijz_cpns" class="col-md-3 control-label"> Ijazah CPNS </label>
									<div class="col-md-9">
										<select class="form-control" name="ijz_cpns" id="modal_update_dik_ijz_cpns">
											<option value="Y"> Ada </option>
											<option value="T"> Tidak </option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="fileijazah" class="col-lg-3 control-label"> Upload Ijazah <br> <span style="font-size: 10px">Hanya berupa JPG, JPEG, dan PNG</span> </label>
									<div class="col-lg-9">
										<input type="file" class="form-control" id="modal_update_dik_fileijazah" name="fileijazah">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-danger pull-right">Simpan</button>
								<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade" id="modal-delete-dik">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="/laporanbmd/profil/form/hapusdikpegawai" class="form-horizontal">
                        @csrf
                            <div class="modal-header">
                                <h4 class="modal-title"><b>Hapus Pendidikan</b></h4>
                            </div>
                            <div class="modal-body">
                                <h4 id="label_delete"></h4>
                                <input type="hidden" name="ids" id="modal_delete_dik_ids" value="">
                                <input type="hidden" name="noid" id="modal_delete_dik_noid" value="">
                                <input type="hidden" name="iddik" id="modal_delete_dik_iddik" value="">
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
	<script src="{{ ('/laporanbmd/public/ample/js/validator.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
	<!-- jQuery x-editable -->
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/moment/moment.js') }}"></script>
	<script type="text/javascript" src="{{ ('/laporanbmd/public/ample/plugins/bower_components/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js') }}"></script>
	<script type="text/javascript">
		$('#inline-nm_emp ').editable({
			type: 'text'
			, name: 'nm_emp'
			, mode: 'inline'
		});

		$('#inline-gelar_blk').editable({
			type: 'text'
			, name: 'gelar_blk'
			, mode: 'inline'
			, url: '/post'
			, success: function(response, newValue) {
				$.ajax({
					type: "POST",
					url: "/laporanbmd/post",
					data: { somefield: "Some field value", another: "another", _token: '{{csrf_token()}}' },
					success: function(data){
						alert(data);
					}
				});
			}
		});
	</script>

	<script>
		$(function () {

			$(".data-input").hide();

			$('.btn-edit-id').on('click', function () {
				$(this).text(function(i, text){
					return text === "Ubah" ? "Kembali" : "Ubah";
				});
				$(".data-show").toggle();
				$(".data-input").toggle();
			});

			jQuery('#datepicker-autoclose').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
			});

			jQuery('#datepicker-autoclose2').datepicker({
				autoclose: true
				, todayHighlight: false
				, format: 'dd/mm/yyyy'
			});

			$('.btn-update-dik').on('click', function () {
				var $el = $(this);

				$("#modal_update_dik_ids").val($el.data('ids'));
				$("#modal_update_dik_noid").val($el.data('noid'));
				$("#modal_update_dik_iddik").val($el.data('iddik'));
				$("#modal_update_dik_prog_sek").val($el.data('prog_sek'));
				$("#modal_update_dik_no_sek").val($el.data('no_sek'));
				$("#modal_update_dik_th_sek").val($el.data('th_sek'));
				$("#modal_update_dik_nm_sek").val($el.data('nm_sek'));
				$("#modal_update_dik_gelar_blk_sek").val($el.data('gelar_blk_sek'));
				$("#modal_update_dik_gelar_dpn_sek").val($el.data('gelar_dpn_sek'));
				$("#modal_update_dik_ijz_cpns").val($el.data('ijz_cpns'));
			});

			$('.btn-delete-dik').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus <b>' + $el.data('iddik') + '</b>?');
				$("#modal_delete_dik_ids").val($el.data('ids'));
				$("#modal_delete_dik_noid").val($el.data('noid'));
				$("#modal_delete_dik_iddik").val($el.data('iddik'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});
		});
	</script>
@endsection