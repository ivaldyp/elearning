<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>BPAD DKI Jakarta</title>
	<link rel="shortcut icon" type="image/x-icon" href="{{ ('/elearning/public/img/photo/bpad-logo-00.png') }}" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

	@yield('css')

</head>

<body>
	<!-- HEADER -->
	<header id="home" style="height: 100px">
		<!-- NAVGATION -->
		<nav id="main-navbar" style="top: 20px">
			<div class="container">
				<div class="navbar-header">
					<!-- Logo -->
					<div class="navbar-brand">
						<a  href="{{ url('/') }}"><img src="{{ ('/elearning/public/img/photo/bpad-logo-04b.png') }}" alt="logo" height="85"></a>
					</div>
					<!-- Logo -->

					<!-- Mobile toggle -->
					<button class="navbar-toggle-btn">
							<i class="fa fa-bars"></i>
						</button>
					<!-- Mobile toggle -->

					<!-- Mobile Search toggle -->
					<button class="search-toggle-btn">
							<i class="fa fa-search"></i>
						</button>
					<!-- Mobile Search toggle -->
				</div>

				<!-- Search -->
				<!-- <div class="navbar-search">
					<button class="search-btn"><i class="fa fa-search"></i></button>
					<div class="search-form">
						<form>
							<input class="input" type="text" name="search" placeholder="Search">
						</form>
					</div>
				</div> -->
				<!-- Search -->

				<!-- Nav menu -->
				<ul class="navbar-menu nav navbar-nav navbar-right">
					<li><a href="{{ url('/materi') }}">Materi</a></li>
					<li><a href="{{ url('/profil') }}">profil</a></li>
					@if(Auth::check() && Auth::user()->id_user)
						<li style="background: red;"><a style="color: white" href="{{ url('logout') }}">logout</a></li>
					@elseif(Auth::check() && Auth::user()->usname)
						<li style="background: #006cb8;"><a style="color: white" href="{{ url('login') }}">Masuk</a></li>
					@else
						<li style="background: #006cb8;"><a style="color: white" href="{{ url('login') }}">Login</a></li>
					@endif
					
				</ul>

				<!-- Nav menu -->
			</div>
		</nav>
		<!-- /NAVGATION -->
	</header>
	<!-- /HEADER -->

	@yield('content')

	<!-- FOOTER -->
	<footer id="footer" class="section">
		<!-- container -->
		<div class="container">

			<!-- footer copyright & nav -->
			<div id="footer-bottom" class="row">
				<div class="col-sm-12">
					<div class="col-sm-6">
						<div class="footer-copyright">
							<span><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
	<!-- Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a> -->
	<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></span>
							<span>&copy; Copyright <?php echo date('Y'); ?> BPAD DKI Jakarta.</span><br>
							Powered by <a href="JavaScript:void(0);"><span style="cursor: default;">Sub Bidang Data & Informasi</span></a>
						</div>
					</div>
					<div class="col-sm-6" style="top: -20px">
						<div class="footer-copyright pull-right">
							
							<!-- <img src="{{ ('/elearning/public/img/photo/plusjakartalogo2.png') }}" alt="" height="100"> -->
						</div>
					</div>
				</div>
			</div>
			<!-- /footer copyright & nav -->
		</div>
		<!-- /container -->
	</footer>
	<!-- /FOOTER -->

	@yield('js')
</body>

</html>
