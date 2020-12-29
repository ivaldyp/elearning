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
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('content')
	<style type="text/css">
		#li_portal a.active {
			background:white;
		}
	</style>
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
							$link = explode("/", url()->full());
							if (count($link) == 5) {
								?> 
									<li class="active"> {{ ucwords(explode("?", $link[4])[0]) }} </li>
								<?php
							} elseif (count($link) == 6) {
								?> 
									<li class="active"> {{ ucwords($link[4]) }} </li>
									<li class="active"> {{ ucwords($link[5]) }} </li>
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
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-info">
								<div class="panel-heading">WELCOME,
									@if(Auth::user()->usname_skpd)
										@if($_SESSION['user_laporan']['TLEVEL'] == 2)
										P3B
										@elseif($_SESSION['user_laporan']['TLEVEL'] == 3)
										Pengurus Barang
										@endif
									@elseif(Auth::user()->usname_admin)
									{{ $_SESSION['user_laporan']['idgroup'] }}
									@elseif(Auth::user()->id_emp)
									EMPLOYEE
									@endif
									 - 
									@if(Auth::user()->usname_skpd)
									{{ $_SESSION['user_laporan']['deskripsi_user'] }}
									@elseif(Auth::user()->usname_admin)
									{{ $_SESSION['user_laporan']['usname'] }}
									@elseif(Auth::user()->id_emp)
									{{ $_SESSION['user_laporan']['nm_emp'] }}
									@endif

									<div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> </div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div>
											<form method="GET" action="/laporanbmd/home">
												<div class="row col-md-12">
													<div class=" col-md-2">
														<select class="form-control" name="yearnow" id="yearnow" onchange="this.form.submit()">
															@foreach($years as $year)
															<option <?php if ($yearnow == $year['tahun']): ?> selected <?php endif ?> value="{{ $year['tahun'] }}">{{ $year['tahun'] }}</option>
															@endforeach
														</select>
													</div>
													<!-- <div class=" col-md-3">
														<input type="hidden" id="katnowcheck" value="{{ $katnow }}">
														<select class="form-control" name="katnow" id="katnow" onchange="this.form.submit()">
															<option value="intrakomptabel">Intrakomptabel</option>
															<option value="ekstrakomptabel">Ekstrakomptabel</option>
															<option value="persediaan">Persediaan</option>
														</select>
													</div> -->
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
			<div class="row">
				<div class="col-md-12">
					<div class="row">
                        <div class="col-lg-4 col-sm-6 col-xs-12" data-toggle="tooltip" title="Aset Tanah">
                            <div class="white-box">
                                <h3 class="box-title">JUMLAH KIB A</h3>
                                <ul class="expense-box">
                                    <li>
                                    	<i class="icon-flag text-info"></i>
                                    	<span>
                                    		<h2>Rp. {{ number_format($arraykib['KIBA_NILAI']) }}</h4>
                                    		<h4 class="text-muted">{{ number_format($arraykib['KIBA']) }} Unit</h2>
                                    	</span>
                                   	</li>
                                </ul>
                                <!-- <ul class="list-inline two-part">
                                    <li><i class=" icon-flag text-info"></i></li>
                                    <li class="text-right">
                                    	<span class="counter" style="font-size: 20px">Unit {{ number_format($arraykib['KIBA']) }}</span><br>
                                    	<span class="counter text-muted" style="font-size: 20px">Rp. {{ number_format($arraykib['KIBA_NILAI']) }}</span>
                                    </li>
                                </ul> -->
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-xs-12" data-toggle="tooltip" title="Aset Peralatan dan Mesin">
                            <div class="white-box">
                                <h3 class="box-title">JUMLAH KIB B</h3>
                                <ul class="expense-box">
                                    <li>
                                    	<i class="icon-chart text-purple"></i>
                                    	<span>
                                    		<h2>Rp. {{ number_format($arraykib['KIBB_NILAI']) }}</h4>
                                    		<h4 class="text-muted">{{ number_format($arraykib['KIBB']) }} Unit</h2>
                                    	</span>
                                   	</li>
                                </ul>

                                <!-- <ul class="list-inline two-part">
                                    <li><i class="icon-chart text-purple"></i></li>
                                    <li class="text-right">
                                    	<span class="counter" style="font-size: 20px">Unit {{ number_format($arraykib['KIBB']) }}</span><br>
                                    	<span class="counter" style="font-size: 20px">Rp. {{ number_format($arraykib['KIBB']) }}</span></li>
                                </ul> -->
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-xs-12" data-toggle="tooltip" title="Aset Gedung dan Bangunan">
                            <div class="white-box">
                                <h3 class="box-title">JUMLAH KIB C</h3>
                                <ul class="expense-box">
                                    <li>
                                    	<i class="icon-home text-danger"></i>
                                    	<span>
                                    		<h2>Rp. {{ number_format($arraykib['KIBC_NILAI']) }}</h4>
                                    		<h4 class="text-muted">{{ number_format($arraykib['KIBC']) }} Unit</h2>
                                    	</span>
                                   	</li>
                                </ul>
                                <!-- <ul class="list-inline two-part">
                                    <li><i class="icon-home text-danger"></i></li>
                                    <li class="text-right">
                                    	<span class="counter" style="font-size: 20px">Unit {{ number_format($arraykib['KIBC']) }}</span><br>
                                    	<span class="counter" style="font-size: 20px">Rp. {{ number_format($arraykib['KIBC']) }}</span></li>
                                </ul> -->
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-xs-12" data-toggle="tooltip" title="Aset Jalan, Irigasi, dan Jaringan">
                            <div class="white-box">
                                <h3 class="box-title">JUMLAH KIB D</h3>
                                <ul class="expense-box">
                                    <li>
                                    	<i class="icon-map text-default"></i>
                                    	<span>
                                    		<h2>Rp. {{ number_format($arraykib['KIBD_NILAI']) }}</h4>
                                    		<h4 class="text-muted">{{ number_format($arraykib['KIBD']) }} Unit</h2>
                                    	</span>
                                   	</li>
                                </ul>
                                <!-- <ul class="list-inline two-part">
                                    <li><i class="icon-map text-default"></i></li>
                                    <li class="text-right">
                                    	<span class="counter" style="font-size: 20px">Unit {{ number_format($arraykib['KIBD']) }}</span><br>
                                    	<span class="counter" style="font-size: 20px">Rp. {{ number_format($arraykib['KIBD']) }}</span></li>
                                </ul> -->
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-xs-12" data-toggle="tooltip" title="Aset Lainnya">
                            <div class="white-box">
                                <h3 class="box-title">JUMLAH KIB E</h3>
                                <ul class="expense-box">
                                    <li>
                                    	<i class="icon-pencil text-warning"></i>
                                    	<span>
                                    		<h2>Rp. {{ number_format($arraykib['KIBE_NILAI']) }}</h4>
                                    		<h4 class="text-muted">{{ number_format($arraykib['KIBE']) }} Unit</h2>
                                    	</span>
                                   	</li>
                                </ul>
                                <!-- <ul class="list-inline two-part">
                                    <li><i class="icon-pencil text-warning"></i></li>
                                    <li class="text-right">
                                    	<span class="counter" style="font-size: 20px">Unit {{ number_format($arraykib['KIBE']) }}</span><br>
                                    	<span class="counter" style="font-size: 20px">Rp. {{ number_format($arraykib['KIBE']) }}</span></li>
                                </ul> -->
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-xs-12" data-toggle="tooltip" title="Aset Konstruksi Dalam Pengerjaan">
                            <div class="white-box">
                                <h3 class="box-title">JUMLAH KIB F</h3>
                                <ul class="expense-box">
                                    <li>
                                    	<i class="icon-wrench text-success"></i>
                                    	<span>
                                    		<h2>Rp. {{ number_format($arraykib['KIBF_NILAI']) }}</h4>
                                    		<h4 class="text-muted">{{ number_format($arraykib['KIBF']) }} Unit</h2>
                                    	</span>
                                   	</li>
                                </ul>
                                <!-- <ul class="list-inline two-part">
                                    <li><i class="icon-wrench text-success"></i></li>
                                    <li class="text-right">
                                    	<span class="counter" style="font-size: 20px">Unit {{ number_format($arraykib['KIBF']) }}</span><br>
                                    	<span class="counter" style="font-size: 20px">Rp. {{ number_format($arraykib['KIBF']) }}</span></li>
                                </ul> -->
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<!-- /.container-fluid -->
		<footer class="footer text-center"> 2017 &copy; Ample Admin brought to you by themedesigner.in </footer>
	</div>
	
@endsection

<!-- /////////////////////////////////////////////////////////////// -->

@section('js')
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
	<script src="{{ ('/laporanbmd/public/ample/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<!-- Menu Plugin JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
	<!--slimscroll JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/js/jquery.slimscroll.js') }}"></script>
	<!--Wave Effects -->
	<script src="{{ ('/laporanbmd/public/ample/js/waves.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ ('/laporanbmd/public/ample/js/custom.min.js') }}"></script>
	<!--Style Switcher -->
	<script src="{{ ('/laporanbmd/public/ample/plugins/bower_components/styleswitcher/jQuery.style.switcher.js') }}"></script>

	<script type="text/javascript">
		$(function () {
			var kat = $("#katnowcheck").val();
            $("#katnow").val(kat);
        });
	</script>
@endsection
