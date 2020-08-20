@extends('layouts.masterhome')

@section('css')
	<!-- Bootstrap Core CSS -->
	<link href="{{ ('/elearning/public/ample/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Menu CSS -->
	<link href="{{ ('/elearning/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ ('/elearning/public/ample/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') }}" />
	<!-- animation CSS -->
	<link href="{{ ('/elearning/public/ample/css/animate.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ ('/elearning/public/ample/css/style.css') }}" rel="stylesheet">
	<!-- color CSS -->
	<link href="{{ ('/elearning/public/ample/css/colors/purple-dark.css') }}" id="theme" rel="stylesheet">
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
				<div class="col-md-12">
					<!-- <div class="white-box"> -->
					<div class="panel panel-default">
                        <div class="panel-heading"> Pegawai </div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                            	<div class="row " style="margin-bottom: 10px">
                            		@if ($access['zadd'] == 'y')
                            		<div class="col-md-1">
				                      	<a href="/elearning/kepegawaian/tambah pegawai"><button class="btn btn-info col-md-12" type="button">Tambah</button></a>
                            		</div>
                            		@endif
                            		<div class="col-md-1">
										<a href="/elearning/kepegawaian/excelpegawai?unit={{ $idunit }}&ked={{ $kednow }}"><button class="btn btn-success col-md-12" type="button"> Excel </button></a>
									</div>
                            		<div class="col-md-10">
                            			<form method="GET" action="/elearning/kepegawaian/data pegawai">
					                      	<div class=" col-md-2">
					                        	<select class="form-control" name="kednow" id="kednow" required onchange="this.form.submit()">
					                          	<?php foreach ($kedudukans as $key => $kedudukan) { ?>
					                            	<option value="{{ $kedudukan['ked_emp'] }}" 
					                              	<?php 
					                                	if ($kednow == $kedudukan['ked_emp']) {
						                                 	echo "selected";
						                                }
					                              	?>
					                            	>{{ $kedudukan['ked_emp'] }}</option>
					                          	<?php } ?>
					                        	</select>
				                      		</div>
				                      		<div class=" col-md-6">
					                        	<select class="form-control select2" name="unit" id="unit" required onchange="this.form.submit()">
					                          	<?php foreach ($units as $key => $unit) { ?>
					                            	<option value="{{ $unit['kd_unit'] }}" 
					                              	<?php 
					                                	if ($idunit == $unit['kd_unit']) {
						                                 	echo "selected";
						                                }
					                              	?>
					                            	>[{{ $unit['kd_unit'] }}] - {{ ($unit['kd_unit'] == '01' ? 'SEMUA' : $unit['notes'])   }}</option>
					                          	<?php } ?>
					                        	</select>
				                      		</div>
				                      		
						                </form>
                            		</div>
                            		
                            		
                            	</div>
								<div class="row">
									<div class="table-responsive">
										<table class="myTable table table-hover">
											<thead>
												<tr>
													<th>No</th>
													<th>Id</th>
													<th>NIP / NRK</th>
													<th>Nama</th>
													<th>Unit</th>
													<th class="col-md-1">Tgl Lahir</th>
													<th class="col-md-1" >Jns Kel</th>
													<th class="col-md-1">TMT</th>
													<th>Status</th>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
													<th class="col-md-2">Action</th>
													@endif
												</tr>
											</thead>
											<tbody style="vertical-align: middle;">
												@foreach($employees as $key => $employee)
												<tr <?php if(strlen($employee['idunit']) < 10): ?> style="font-weight: bold;" <?php endif ?>  >
													<?php
														$tgl_lahir = explode(" ", $employee['tgl_lahir']);
														$tgl_join = explode(" ", $employee['tgl_join']);
													?>

													<td>{{ $key + 1 }}</td>
													<td>{{ $employee['id_emp'] }}</td>
													<td>{{ $employee['nip_emp'] ? $employee['nip_emp'] :' -' }} <br>
														<span class="text-muted">{{ $employee['nrk_emp'] ? $employee['nrk_emp'] :'' }}</span>
													</td>
													<td>{{ $employee['nm_emp'] }}</td>
													<td>{{ $employee['nm_unit'] }}<br>
														<span class="text-muted">{{ $employee['nm_lok'] }}</span>
													</td>
													<td>{{ date('d/M/Y', strtotime(str_replace('/', '-', $employee['tgl_lahir'] ))) }}</td>
													<td>{{ $employee['jnkel_emp'] }}</td>
													<td>{{ date('d/M/Y', strtotime(str_replace('/', '-', $employee['tgl_join'] ))) }}</td>
													<td>{{ $employee['status_emp'] }}</td>
													@if($access['zupd'] == 'y' || $access['zdel'] == 'y')
														<td>
															<form method="GET" action="/elearning/kepegawaian/ubah pegawai">
															@if($access['zupd'] == 'y')
																<input type="hidden" name="id_emp" value="{{ $employee['id_emp'] }}">
																<button type="submit" class="btn btn-info btn-update"><i class="fa fa-edit"></i></button>
																<button type="button" class="btn btn-warning btn-password" data-toggle="modal" data-target="#modal-password-{{$key}}"><i class="fa fa-key"></i></button>
															@endif
															@if($access['zdel'] == 'y')
																<button type="button" class="btn btn-danger btn-delete" data-toggle="modal" data-target="#modal-delete" data-id_emp="{{ $employee['id_emp'] }}" data-nm_emp="{{ $employee['nm_emp'] }}"><i class="fa fa-trash"></i></button>
															@endif
															</form>
															<div id="modal-password-{{$key}}" class="modal fade" role="dialog">
																<div class="modal-dialog">
																	<div class="modal-content">
																		<form method="POST" action="/elearning/kepegawaian/form/ubahpassuser" class="form-horizontal">
																		@csrf
																			<div class="modal-header">
																				<h4 class="modal-title"><b>Ubah Password</b></h4>
																			</div>
																			<div class="modal-body">
																				<h4>Masukkan password baru  </h4>

																				<input type="hidden" name="id_emp" value="{{ $employee['id_emp'] }}">
																				<input type="hidden" name="nm_emp" value="{{ $employee['nm_emp'] }}">

																				<div class="form-group col-md-12">
																					<label for="idunit" class="col-md-2 control-label"> Password </label>
																					<div class="col-md-8">
																						<input autocomplete="off" type="text" name="passmd5" class="form-control" required>
																					</div>
																				</div>

																				<div class="clearfix"></div>
																			</div>
																			<div class="modal-footer">
																				<button type="submit" class="btn btn-danger pull-right">Simpan</button>
																				<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
																			</div>
																		</form>
																	</div>
																</div>
															</div>
														</td>
													@endif
												</tr>
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
			<div id="modal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="POST" action="/elearning/kepegawaian/form/hapuspegawai" class="form-horizontal">
						@csrf
							<div class="modal-header">
								<h4 class="modal-title"><b>Hapus Pegawai</b></h4>
							</div>
							<div class="modal-body">
								<h4 id="label_delete"></h4>
								<input type="hidden" name="id_emp" id="modal_delete_id_emp" value="">
								<input type="hidden" name="nm_emp" id="modal_delete_nm_emp" value="">
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
	<script src="{{ ('/elearning/public/ample/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
	<!-- wysuhtml5 Plugin JavaScript -->
    <script src="{{ ('/elearning/public/ample/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') }}"></script>
    <script src="{{ ('/elearning/public/ample/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.textarea_editor').wysihtml5();
            $('.textarea_editor2').wysihtml5();
        });
    </script>


	<script>
		$(function () {

			$(".select2").select2();

			$('.btn-update').on('click', function () {
				var $el = $(this);

				if ($el.data('idkat') != 1) {
					$("#cekidkat").hide();
				} else {
					$("#cekidkat").show();
				}

				if ($el.data('sts') == 0) {
					$("#modal_update_sts1").attr('checked', true);
				} else {
					$("#modal_update_sts2").attr('checked', true);
				}

				$("#modal_update_ids").val($el.data('ids'));
				$("#modal_update_idkat").val($el.data('idkat'));
				$("#modal_update_subkat").val($el.data('subkat'));
				$("#modal_update_waktu").val($el.data('waktu'));
				$("#modal_update_judul").val($el.data('judul'));
				$("#modal_update_isi1").data("wysihtml5").editor.setValue($el.data('isi1'));
				$("#modal_update_isi2").data("wysihtml5").editor.setValue($el.data('isi2'));

				$('.textarea_editor').contents().find('.wysihtml5-editor').html($el.data('isi1'));
				$('.textarea_editor2').contents().find('.wysihtml5-editor').html($el.data('isi2'));

				var ids = $el.data('ids');
				var idkat = $el.data('idkat');
				var appr = $el.data('appr');
				var judul = $el.data('judul');
				
				if (appr == 'Y') {
					$("#btn_update_href").html('Batal Setuju');
				} else if (appr == 'N') {
					$("#btn_update_href").html('Setuju');
				}
				$("#modal_update_href").attr("href", "/elearning/cms/form/apprcontent?ids=" + ids + "&idkat=" + idkat + "&appr=" + appr + "&judul=" + judul );

			});

			$('.btn-delete').on('click', function () {
				var $el = $(this);

				$("#label_delete").append('Apakah anda yakin ingin menghapus pegawai <b>' + $el.data('nm_emp') + '</b>?');
				$("#modal_delete_id_emp").val($el.data('id_emp'));
				$("#modal_delete_nm_emp").val($el.data('nm_emp'));
			});

			$("#modal-delete").on("hidden.bs.modal", function () {
				$("#label_delete").empty();
			});

			$('.myTable').DataTable();
		});
	</script>
@endsection