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
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">Disposisi</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="row" style="margin-bottom: 10px">
									<form method="GET" action="/laporanbmd/profil/disposisi">
										<div class=" col-md-2">
											<select class="form-control" name="signnow" id="signnow" onchange="this.form.submit()">
												<option <?php if ($signnow == "="): ?> selected <?php endif ?> value="=">=</option>
												<option <?php if ($signnow == ">="): ?> selected <?php endif ?> value=">=">>=</option>
												<option <?php if ($signnow == "<="): ?> selected <?php endif ?> value="<="><=</option>
											</select>
										</div>
										<div class=" col-md-2">
											<select class="form-control" name="yearnow" id="yearnow" onchange="this.form.submit()">
												<option <?php if ($yearnow == (int)date('Y')): ?> selected <?php endif ?> value="{{ (int)date('Y') }}">{{ (int)date('Y') }}</option>
												<option <?php if ($yearnow == (int)date('Y') - 1): ?> selected <?php endif ?> value="{{ (int)date('Y') - 1 }}">{{ (int)date('Y') - 1 }}</option>
												<option <?php if ($yearnow == (int)date('Y') - 2): ?> selected <?php endif ?> value="{{ (int)date('Y') - 2 }}">{{ (int)date('Y') - 2 }}</option>
											</select>
										</div>
									</form>
								</div>
								@if($access['zadd'] == 'y' && isset(Auth::user()->usname))
								<a href="/laporanbmd/profil/tambah disposisi"><button class="btn btn-info" style="margin-bottom: 10px">Tambah </button></a> 
								@endif
								@if($isEmployee == 1)
									<ul class="nav customtab nav-tabs" role="tablist">
										<li role="presentation" class="active"><a href="#inbox" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Inbox</span></a></li>
										<li role="presentation" class=""><a href="#sent" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs"> Sudah Ditindaklanjuti</span></a></li>
									</ul>
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane fade active in" id="inbox">
											<div class="table-responsive" style="overflow: visible;">
												<table id="myTable" class="table table-hover table-striped" style="z-index: 99999;">
													<thead>
														<tr>
															<th>No. Form</th>
															<th class="col-md-1">Tanggal</th>
															<th>Sifat</th>
															<th>Isi</th>
															<th>Dari</th>
															<!-- <th>Untuk</th> -->
															<th>Penanganan</th>
															<th>Status</th>
															@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
															<th class="col-md-1">Action</th>
															@endif
														</tr>
													</thead>
													<tbody>
														<?php foreach ($disposisiinboxs as $key => $disp) {
															if ($disp['kode_disposisi']) {
																$thisnoform = $disp['no_form'];
																$thistanggal = $disp['tgl_masuk'];
																$thiskode = $disp['kode_disposisi'];
																$thisnosurat = $disp['no_surat'];
																$thisperihal = $disp['perihal'];
																$thisasal = $disp['asal_surat'];
																$thissifat1 = $disp['sifat1_surat'];
																$thissifat2 = $disp['sifat2_surat'];
																$thisfile = $disp['nm_file'];
															}

															if ($disp['to_id'] == $_SESSION['user_laporan']['id_emp'] && ($disp['rd'] == 'N' || $disp['rd'] == 'Y')) { ?>
																<tr>
																	<td>{{ $thisnoform ?? '-' }}</td>
																	<td class="col-md-2">{{ date('d-M-Y',strtotime($thistanggal ?? '-')) }}</td>
																	<td>
																		@if($thissifat1)
																		<span class="label label-info">{{ $thissifat1 ?? '-' }}</span>
																		<br>
																		@endif
																		<span class="label label-warning">{{ $thissifat2 ?? '-' }}</span>
																	</td>
																	<td>
																		<span class="mytooltip tooltip-effect-1"> 
																			<span class="tooltip-item">Lihat Isi</span> 
																			<span class="tooltip-content clearfix"> 
																				<table class="table table-bordered">
																					<tbody>
																						<tr>
																							<td><strong>Kode</strong></td>
																							<td>{{ $thiskode ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>No Surat</strong></td>
																							<td>{{ $thisnosurat ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>Perihal</strong></td>
																							<td>{{ $thisperihal ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>Asal Surat</strong></td>
																							<td>{{ $thisasal ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>Download</strong></td>
																							<td>
																							<?php 
																								$splitfile = explode("::", $thisfile);
																								foreach ($splitfile as $key => $file) { 
																									if (file_exists('public/publicfile/disp/'.$file)) {
																										$namafolder = '';
																									} else {
																										$namafolder = '/' . $thisnoform;
																									}
																									?>
																									<a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $file }}">{{ $file }}</a>
																									<br>
																								<?php } 
																							?>
																							</td>
																						</tr>
																					</tbody>
																				</table> 
																			</span> 
																		</span>
																	</td>

																	<td>
																		<?php if (substr($disp['from_id'], 0, 4) == '1.20'): ?>
																			{{ $disp['from_pm'] }}
																		<?php else : ?>
																			{{ $disp['from_id'] }}
																		<?php endif ?>
																	</td>
																	<!-- <td>{{ $disp['to_pm'] }}</td> -->
																	<td>{{ $disp['penanganan'] }}</td>
																	<td>
																		@if($disp['rd'] == 'N')
																		<span class="label label-danger">Belum Dibaca</span>
																		<br>
																		<span class="label label-danger">Belum Dibalas</span>
																		@elseif($disp['rd'] == 'Y')
																		<span class="label label-info">Sudah Dibaca</span>
																		<br>
																		<span class="label label-danger">Belum Dibalas</span>
																		@endif
																	</td>
																	@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
																	<td style="vertical-align: middle;">
																		@if ($access['zupd'] == 'y')
																		<form method="POST" action="/laporanbmd/profil/lihat disposisi">
																			@csrf
																			<input type="hidden" name="ids" value="{{ $disp['ids'] }}">
																			<input type="hidden" name="no_form" value="{{ $disp['no_form'] }}">
																			<input type="hidden" name="to_id" value="{{ $disp['to_id'] }}">
																			<input type="hidden" name="asal_form" value="inbox">
																			<input type="hidden" name="idtop" value="{{ $disp['idtop'] }}">
																			<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																		</form>
																		
																		@endif
																		@if ($access['zdel'] == 'y')
																		<button type="button" class="btn btn-danger btn-delete btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-{{ $disp['ids'] }}"
																		><i class="ti-trash" data-ids="{{ $disp['ids'] }}" data-no_form="{{ $disp['no_form'] }}"></i></button>
																		@endif
																	</td>
																	@endif
																</tr>
																<div id="modal-delete-{{ $disp['ids'] }}" class="modal fade" role="dialog">
																	<div class="modal-dialog">
																		<div class="modal-content">
																			<form method="POST" action="/laporanbmd/profil/form/hapusdisposisi" class="form-horizontal">
																			@csrf
																				<div class="modal-header">
																					<h4 class="modal-title"><b>Hapus Disposisi</b></h4>
																				</div>
																				<div class="modal-body">
																					<h4 id="">Apakah anda yakin ingin menghapus form nomor <b>{{ $disp['no_form'] }}</b>?</h4>
																					<input type="hidden" name="ids" value="{{ $disp['ids'] }}">
																					<input type="hidden" name="idtop" value="{{ $disp['idtop'] }}">
																					<input type="hidden" name="no_form" value="{{ $disp['no_form'] }}">
																				</div>
																				<div class="modal-footer">
																					<button type="submit" class="btn btn-danger pull-right">Hapus</button>
																					<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
																<div class="clearfix"></div>
															<?php }
														} ?>
													</tbody>
												</table>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane fade" id="sent">
											<div class="table-responsive" style="overflow: visible;">
												<table id="myTable2" class="table table-hover table-striped">
													<thead>
														<tr>
															<th>No. Form</th>
															<th class="col-md-1">Tanggal</th>
															<th>Sifat</th>
															<th>Isi</th>
															<th>Dari</th>
															<th>Untuk</th>
															<th>Penanganan</th>
															@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
															<th class="col-md-1">Action</th>
															@endif
														</tr>
													</thead>
													<tbody>
														<?php foreach ($disposisiinboxs as $key => $disp) {
															if ($disp['kode_disposisi']) {
																$thisnoform = $disp['no_form'];
																$thistanggal = $disp['tgl_masuk'];
																$thiskode = $disp['kode_disposisi'];
																$thisnosurat = $disp['no_surat'];
																$thisperihal = $disp['perihal'];
																$thisasal = $disp['asal_surat'];
																$thissifat1 = $disp['sifat1_surat'];
																$thissifat2 = $disp['sifat2_surat'];
																$thisfile = $disp['nm_file'];
															}

															if ($disp['from_id'] == $_SESSION['user_laporan']['id_emp'] || ($disp['to_id'] == $_SESSION['user_laporan']['id_emp'] && $disp['selesai'] == 'Y' && $disp['rd'] == 'S')) { ?>
																<tr>
																	<td>{{ $thisnoform }}</td>
																	<td class="col-md-2">{{ date('d-M-Y',strtotime($thistanggal)) }}</td>
																	<td>
																		@if($thissifat1)
																		<span class="label label-info">{{ $thissifat1 }}</span>
																		<br>
																		@endif
																		<span class="label label-warning">{{ $thissifat2 ?? '-' }}</span>
																	</td>
																	<td>
																		<span class="mytooltip tooltip-effect-1"> 
																			<span class="tooltip-item">Lihat Isi</span> 
																			<span class="tooltip-content clearfix"> 
																				<table class="table table-bordered">
																					<tbody>
																						<tr>
																							<td><strong>Kode</strong></td>
																							<td>{{ $thiskode ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>No Surat</strong></td>
																							<td>{{ $thisnosurat ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>Perihal</strong></td>
																							<td>{{ $thisperihal ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>Asal Surat</strong></td>
																							<td>{{ $thisasal ?? '-' }}</td>
																						</tr>
																						<tr>
																							<td><strong>Download</strong></td>
																							<td>
																							<?php 
																								$splitfile = explode("::", $thisfile);
																								foreach ($splitfile as $key => $file) { 
																									if (file_exists('public/publicfile/disp/'.$file)) {
																										$namafolder = '';
																									} else {
																										$namafolder = '/' . $thisnoform;
																									}
																									?>
																									<a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $file }}">{{ $file }}</a>
																									<br>
																								<?php } 
																							?>
																							</td>
																							<!-- <td><a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $thisfile }}">{{ $thisfile }}</a></td> -->
																						</tr>
																					</tbody>
																				</table> 
																			</span> 
																		</span>
																	</td>

																	<td>
																		<?php if (substr($disp['from_id'], 0, 4) == '1.20'): ?>
																			{{ $disp['from_pm'] }}
																		<?php else : ?>
																			{{ $disp['from_id'] }}
																		<?php endif ?>
																	</td>
																	<td>{{ $disp['to_pm'] }}</td>
																	<td>{{ $disp['penanganan'] }}</td>

																	@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
																	<td style="vertical-align: middle;">
																		@if ($access['zupd'] == 'y')
																		<form method="POST" action="/laporanbmd/profil/lihat disposisi">
																			@csrf
																			<input type="hidden" name="ids" value="{{ $disp['ids'] }}">
																			<input type="hidden" name="no_form" value="{{ $disp['no_form'] }}">
																			<input type="hidden" name="to_id" value="{{ $disp['to_id'] }}">
																			<input type="hidden" name="asal_form" value="sent">
																			<input type="hidden" name="idtop" value="{{ $disp['idtop'] }}">
																			<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																		</form>
																		@endif
																		@if ($access['zdel'] == 'y')
																		<button type="button" class="btn btn-danger btn-delete btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-{{ $disp['ids'] }}"
																		><i class="ti-trash" data-ids="{{ $disp['ids'] }}" data-no_form="{{ $disp['no_form'] }}"></i></button>
																		@endif
																	</td>
																	@endif
																</tr>
																<div id="modal-delete-{{ $disp['ids'] }}" class="modal fade" role="dialog">
																	<div class="modal-dialog">
																		<div class="modal-content">
																			<form method="POST" action="/laporanbmd/profil/form/hapusdisposisi" class="form-horizontal">
																			@csrf
																				<div class="modal-header">
																					<h4 class="modal-title"><b>Hapus Disposisi</b></h4>
																				</div>
																				<div class="modal-body">
																					<h4 id="">Apakah anda yakin ingin menghapus form nomor <b>{{ $disp['no_form'] }}</b>?</h4>
																					<input type="hidden" name="ids" value="{{ $disp['ids'] }}">
																					<input type="hidden" name="idtop" value="{{ $disp['idtop'] }}">
																					<input type="hidden" name="no_form" value="{{ $disp['no_form'] }}">
																				</div>
																				<div class="modal-footer">
																					<button type="submit" class="btn btn-danger pull-right">Hapus</button>
																					<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
															<?php }
														} ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								@endif
								@if($isEmployee == 0)
								<ul class="nav customtab nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#inbox2" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Terkirim</span></a></li>
									<li role="presentation" class=""><a href="#sent2" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs"> Draft</span></a></li>
								</ul>
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane fade active in" id="inbox2">
										<div class="table-responsive">
											<table id="myTable3" class="table table-hover table-striped">
												<thead>
													<tr>
														<th>No. Form</th>
														<th class="col-md-1">Tanggal</th>
														<th>Kode</th>
														<th>No. Surat</th>
														<th>Perihal</th>
														<th>Asal</th>
														<th>File</th>
														<th>Sifat</th>
														@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<th class="col-md-1">Action</th>
														@endif
													</tr>
												</thead>
												<tbody>
													<?php foreach ($disposisis as $key => $disp) { ?>
														<tr>
															<td>{{ $disp['no_form'] ?? '-' }}</td>
															<td>{{ date('d-M-Y',strtotime($disp['tgl_masuk'] ?? '-')) }}</td>
															<td>{{ $disp['kode_disposisi'] ?? '-' }}</td>
															<td>{{ $disp['no_surat'] ?? '-' }}</td>
															<td>{{ $disp['perihal'] ?? '-' }}</td>
															<td>{{ $disp['asal_surat'] ?? '-' }}</td>
															<td>
																<?php 
																	$splitfile = explode("::", $disp['nm_file']);
																	foreach ($splitfile as $key => $file) { 
																		if (file_exists('public/publicfile/disp/'.$file)) {
																			$namafolder = '';
																		} else {
																			$namafolder = '/' . $disp['no_form'];
																		}
																		?>
																		<a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $file }}">{{ $file }}</a>
																		<br>
																	<?php } 
																?>
															</td>
															<!-- <td><a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $disp['nm_file'] }}">{{ $disp['nm_file'] }}</a></td> -->
															<td>
																<span class="label label-info">{{ $disp['sifat1_surat'] ?? '-' }}</span>
																<br>
																<span class="label label-warning">{{ $disp['sifat2_surat'] ?? '-' }}</span>
															</td>

															@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
															<td style="vertical-align: middle;">
																@if ($access['zupd'] == 'y')
																<form method="POST" action="/laporanbmd/profil/lihat disposisi">
																	@csrf
																	<input type="hidden" name="ids" value="{{ $disp['ids'] }}">
																	<input type="hidden" name="no_form" value="{{ $disp['no_form'] }}">
																	<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																</form>
																@endif
																@if ($access['zdel'] == 'y')
																<button type="button" class="btn btn-danger btn-delete btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-{{ $disp['ids'] }}"
																><i class="ti-trash" data-ids="{{ $disp['ids'] }}" data-no_form="{{ $disp['no_form'] }}"></i></button>
																@endif
															</td>
															@endif
														</tr>
														<div id="modal-delete-{{ $disp['ids'] }}" class="modal fade" role="dialog">
															<div class="modal-dialog">
																<div class="modal-content">
																	<form method="POST" action="/laporanbmd/profil/form/hapusdisposisi" class="form-horizontal">
																	@csrf
																		<div class="modal-header">
																			<h4 class="modal-title"><b>Hapus Disposisi</b></h4>
																		</div>
																		<div class="modal-body">
																			<h4 id="">Apakah anda yakin ingin menghapus form nomor <b>{{ $disp['no_form'] }}</b>?</h4>
																			<input type="hidden" name="ids" value="{{ $disp['ids'] }}">
																			<input type="hidden" name="idtop" value="{{ $disp['idtop'] }}">
																			<input type="hidden" name="no_form" value="{{ $disp['no_form'] }}">
																			
																		</div>
																		<div class="modal-footer">
																			<button type="submit" class="btn btn-danger pull-right">Hapus</button>
																			<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
																		</div>
																	</form>
																</div>
															</div>
														</div>
														<div class="clearfix"></div>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade active in" id="sent2">
										<div class="table-responsive">
											<table id="myTable4" class="table table-hover table-striped">
												<thead>
													<tr>
														<th>No. Form</th>
														<th class="col-md-1">Tanggal</th>
														<th>Kode</th>
														<th>No. Surat</th>
														<th>Perihal</th>
														<th>Asal</th>
														<th>File</th>
														<th>Sifat</th>
														@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<th class="col-md-1">Action</th>
														@endif
													</tr>
												</thead>
												<tbody>
													<?php foreach ($disposisisdraft as $key => $draft) { ?>
														<tr>
															<td>{{ $draft['no_form'] ?? '-' }}</td>
															<td>{{ date('d-M-Y',strtotime($draft['tgl_masuk'] ?? '-')) }}</td>
															<td>{{ $draft['kode_disposisi'] ?? '-' }}</td>
															<td>{{ $draft['no_surat'] ?? '-' }}</td>
															<td>{{ $draft['perihal'] ?? '-' }}</td>
															<td>{{ $draft['asal_surat'] ?? '-' }}</td>
															<td>
																<?php 
																	$splitfile = explode("::", $draft['nm_file']);
																	foreach ($splitfile as $key => $file) { 
																		if (file_exists('public/publicfile/disp/'.$file)) {
																			$namafolder = '';
																		} else {
																			$namafolder = '/' . $draft['no_form'];
																		}
																		?>
																		<a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $file }}">{{ $file }}</a>
																		<br>
																	<?php } 
																?>
															</td>
															<!-- <td><a target="_blank" href="{{ config('app.openfiledisposisi') }}{{$namafolder}}/{{ $draft['nm_file'] }}">{{ $draft['nm_file'] }}</a></td> -->
															<td>
																<span class="label label-info">{{ $draft['sifat1_surat'] ?? '-' }}</span>
																<br>
																<span class="label label-warning">{{ $draft['sifat2_surat'] ?? '-' }}</span>
															</td>

															@if ($access['zupd'] == 'y' || $access['zdel'] == 'y')
															<td style="vertical-align: middle;">
																@if ($access['zupd'] == 'y')
																<form method="POST" action="/laporanbmd/profil/lihat disposisi">
																	@csrf
																	<input type="hidden" name="ids" value="{{ $draft['ids'] }}">
																	<input type="hidden" name="no_form" value="{{ $draft['no_form'] }}">
																	<button type="submit" class="btn btn-info btn-outline btn-circle m-r-5 btn-update"><i class="ti-pencil-alt"></i></button>
																</form>
																@endif
																@if ($access['zdel'] == 'y')
																<button type="button" class="btn btn-danger btn-delete btn-outline btn-circle m-r-5" data-toggle="modal" data-target="#modal-delete-{{ $draft['ids'] }}"
																><i class="ti-trash" data-ids="{{ $draft['ids'] }}" data-no_form="{{ $draft['no_form'] }}"></i></button>
																@endif
															</td>
															@endif
														</tr>
														<div id="modal-delete-{{ $draft['ids'] }}" class="modal fade" role="dialog">
															<div class="modal-dialog">
																<div class="modal-content">
																	<form method="POST" action="/laporanbmd/profil/form/hapusdisposisi" class="form-horizontal">
																	@csrf
																		<div class="modal-header">
																			<h4 class="modal-title"><b>Hapus Disposisi</b></h4>
																		</div>
																		<div class="modal-body">
																			<h4 id="">Apakah anda yakin ingin menghapus form nomor <b>{{ $draft['no_form'] }}</b>?</h4>
																			<input type="hidden" name="ids" value="{{ $draft['ids'] }}">
																			<input type="hidden" name="idtop" value="{{ $draft['idtop'] }}">
																			<input type="hidden" name="no_form" value="{{ $draft['no_form'] }}">
																			
																		</div>
																		<div class="modal-footer">
																			<button type="submit" class="btn btn-danger pull-right">Hapus</button>
																			<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
																		</div>
																	</form>
																</div>
															</div>
														</div>
														<div class="clearfix"></div>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								@endif
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
	<script src="{{ ('/laporanbmd/public/ample/js/validator.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
	<!-- Date Picker Plugin JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

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

			// $('.btn-delete').on('click', function () {
			// 	var $el = $(this);

			// 	alert($el.data('ids'));

			// 	$("#label_delete").append('Apakah anda yakin ingin menghapus form nomor <b>' + $el.data('no_form') + '</b>?');
			// 	$("#modal_delete_ids").val($el.data('ids'));
			// 	$("#modal_delete_no_form").val($el.data('no_form'));
			// });

			// $("#modal-delete").on("hidden.bs.modal", function () {
			// 	$("#label_delete").empty();
			// });

			$('#myTable').DataTable({
				"order": [ 0, "desc" ]
			});

			$('#myTable2').DataTable({
				"order": [ 0, "desc" ]
			});

			$('#myTable3').DataTable({
				"order": [ 0, "desc" ]
			});

			$('#myTable4').DataTable({
				"order": [ 0, "desc" ]
			});
		});
	</script>
@endsection