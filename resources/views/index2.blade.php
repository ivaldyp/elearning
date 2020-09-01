@extends('layouts.master')

@section('css')
	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400%7CSource+Sans+Pro:700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="{{ ('/laporanbmd/public/css/bootstrap.min.css') }}" />

	<!-- Owl Carousel -->
	<link type="text/css" rel="stylesheet" href="{{ ('/laporanbmd/public/css/owl.carousel.css') }}" />
	<link type="text/css" rel="stylesheet" href="{{ ('/laporanbmd/public/css/owl.theme.default.css') }}" />

	<!-- Font Awesome Icon -->
	<link rel="stylesheet" href="{{ ('/laporanbmd/public/css/font-awesome.min.css') }}" />

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="{{ ('/laporanbmd/public/css/style.css') }}" />

	<link rel="stylesheet" href="{{ ('/laporanbmd/public/tree/mgaccordion.css') }}" />
@endsection

@section('content')



<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
			<h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px"><span style="color: #006cb8; font-weight: bold">{{ config('app.webname') }}</span></h1>
		</div>
	</div>
</div>
<!-- SECTION -->
<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			@if(Auth::check())
				<!-- MAIN -->
				<main id="main" class="col-md-9">
					<!-- article -->
					<div class="article">
						<ul class="example" style="width: auto;">
							<li><a href="#" title="">Pelatihan Pengelolaan BMD</a>
								<ul class="submenu closed">
									@foreach($materis as $key => $mat)

										<li><a href="javascript:void(0)">
											<form method="POST" action="/">
											@csrf
												<input type="hidden" name="ids" value="{{ $mat['ids'] }}">
												{{ ucwords(strtolower($mat['nm_materi'])) }}
												{!! $mat['sao'] != 0 ? '<button class="btn btn-info">Lihat</button>' : '' !!}
											</form>
										</a>
										@if($mat['child'] == 0)
										</li>
										@elseif($mat['child'] == 1)
										<ul>
										@endif
										
										@if(isset($materis[$key+1]))
										@if($materis[$key+1]['sao'] == 0 && $materis[$key]['sao'] != 0) 
										</ul>
										</li>
										@endif
										@endif
										
									@endforeach
								</ul>
							</li>
						</ul>
              
					</div>					
				</main>
				<!-- /MAIN -->

				<!-- ASIDE -->
				<aside id="aside" class="col-md-3" style="background-color: #f4f4f8">
					<!-- category widget -->
					<div class="widget">
						<h3 class="widget-title">Total Materi: {{ $countmateri }}</h3>
						
					</div>
					<!-- /category widget -->
				</aside>
				<!-- /ASIDE -->
			@else
				<main id="main" class="col-xs-12 text-center" style="background-color: #f4f4f8">
					<!-- article -->
					<div class="article">
						<h2>Tidak dapat mengakses halaman ini, silakan login</h2>
						<br>
						<a href="{{ url('login') }}"><button>Login</button></a>
					</div>					
				</main>
			@endif
				
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</div>
<!-- /SECTION -->

						


@endsection

@section('js')

	<!-- jQuery Plugins -->
	<script src="{{ ('/laporanbmd/public/js/jquery.min.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/js/bootstrap.min.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/js/owl.carousel.min.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/js/jquery.stellar.min.js') }}"></script>
	<script src="{{ ('/laporanbmd/public/js/main.js') }}"></script>

	<!-- <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script> -->
	<script src="{{ ('/laporanbmd/public/js/jquery.zoom.js') }}"></script>
	<script type="text/javascript">
		var main = function(){
			var ads = $('#ads')
			
			$(document).scroll(function(){
				if ( $(this).scrollTop() >= $(window).height() - ads.height() ){
				ads.removeClass('bottom').addClass('top')
				} else {
				ads.removeClass('top').addClass('bottom')
				}
			})
		}
		$(document).ready(main);
	</script>

	<!-- Accordion js -->
	<script src="{{ ('/laporanbmd/public/tree/mgaccordion.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.example').mgaccordion({});
		});
	</script>

	
@endsection