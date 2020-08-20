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
                        <div class="panel-heading">Hak Akses</div>
                    	<div class="panel-wrapper collapse in">
                            <div class="panel-body">
                            	<form action="{{ url('/cms/form/ubahaccess') }}" method="POST">
                            	@csrf
                            		<div class="row">
                            			<div class="col-md-12">
                            				<button class="btn btn-info pull-right">Simpan</button>
                            				<a href="{{ url('/cms/menu') }}"><button type="button" class="m-r-10 btn btn-default pull-right">Kembali</button></a>
                            			</div>
                            		</div>
                            		<div class="row">
                            			<div class="col-md-12">
                            				
											<div class="table-responsive">
												<table class="table table-hover">
													<thead>
														<tr>
															<th>Nama</th>
															<th>view</th>
															<th>insert</th>
															<th>update</th>
															<th>delete</th>
														</tr>
													</thead>
													<tbody>
														<input type="hidden" name="idtop" value="{{ $now_idtop }}"> 
														<input type="hidden" name="desk" value="{{ $now_desk }}"> 
													@foreach($accesses as $acc)
														<tr>
															<td>{{ $acc['idgroup'] }}</td>
															<td><input type="checkbox" name="zviw[]" <?php if ($acc['zviw'] == 'y'): ?> checked <?php endif ?> value="{{ $acc['idgroup'] }}" ></td>
															<td><input type="checkbox" name="zadd[]" <?php if ($acc['zadd'] == 'y'): ?> checked <?php endif ?> value="{{ $acc['idgroup'] }}" ></td>
															<td><input type="checkbox" name="zupd[]" <?php if ($acc['zupd'] == 'y'): ?> checked <?php endif ?> value="{{ $acc['idgroup'] }}" ></td>
															<td><input type="checkbox" name="zdel[]" <?php if ($acc['zdel'] == 'y'): ?> checked <?php endif ?> value="{{ $acc['idgroup'] }}" ></td>
														</tr>
													@endforeach
													</tbody>
												</table>
											</div>
                            			</div>
                            		</div>
                            		<div class="row">
                            			<div class="col-md-12">
                            				<button class="btn btn-info pull-right">Simpan</button>
                            				<a href="{{ url('/cms/menu') }}"><button type="button" class="m-r-10 btn btn-default pull-right">Kembali</button></a>
                            			</div>
                            		</div>
                            	</form>
							</div>
						</div>
					</div>
					<!-- </div> -->
				</div>
			</div>
		</div>
		<!-- /.container-fluid -->
		<footer class="footer text-center"> 
			<span>&copy; Copyright <?php echo date('Y'); ?> BPAD DKI Jakarta.</span></span></a>
		</footer>
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


	<script>
		function goBack() {
		  window.history.back();
		}
	</script>
@endsection