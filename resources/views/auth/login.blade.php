<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link href="{{ ('/elearning/public/landing/bootstrap400/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ ('/elearning/public/landing/css/custom.css') }}" rel="stylesheet">
    </head>
    <body style="background-color: #fbe6a5" class="login-body">
        <div class="content col-md-12" style="background-color: white">
            <!-- <nav class="navbar navbar-expand-sm bg-light"> -->
            <nav class="navbar navbar-expand-sm">
                <div class="container">
                    <img src="{{ ('/elearning/public/landing/img/bpad-logo-01.png') }}" alt="BPAD" width="10%" class="navbar-brand">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link cust-nav" href="http://aset.jakarta.go.id/fileapp/files/02_BM_eHarga.pdf">Manual Book</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cust-nav" href="http://aset.jakarta.go.id/fileapp/files/BP_02-eHarga.jpg">Bisnis Proses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cust-nav" href="https://youtu.be/RCXbZfmz0ZQ">Video Tutorial</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cust-nav" href="http://aset.jakarta.go.id/fileapp/files/04_SOP_eHarga.pdf">SOP</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cust-nav" href="http://aset.jakarta.go.id/appdoc/public/faq/eHARGA">FAQ</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container">
                <div class="row ">
                    <div class="col-md-6">
                        <div class="row">
                            <p style="font-family: 'Myriad Pro Bold'; color: #5793ce; font-size: 24px; ">Selamat datang di </p>
                        </div>
                        <div class="row">
                            <p style="font-family: 'Myriad Pro Regular'; color: #002853; font-size: 48px; ">E-<span style="font-family: 'Myriad Pro Bold'; color: #002853; font-size: 48px;">LEARNING</span></p>
                        </div>
                        <div class="row">
                            <!-- <p style="font-family: 'Myriad Pro Regular'; text-align: justify; font-size: 20px">Sistem pengamanan berdasarkan dokumen Barang Milik Daerah, pengamanan Barang Milik Daerah juga dilakukan dengan pengamanan fisik</p> -->
                        </div>
                        <div class="row">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                @if(Auth::check())
                                <button type="submit" class="btn btn-warning" style="color: white">MASUK</button>
                                @else
                                <div class="form-group">
                                    <label for="name" style="font-family: 'Myriad Pro Regular'; font-size: 18px; color: #5793ce;">Username</label>
                                    <input required="" autocomplete="off" type="text" name="name" class="form-control no-outline">  
                                </div>
                                <div class="form-group">
                                    <label for="password" style="font-family: 'Myriad Pro Regular'; font-size: 18px; color: #5793ce;">Password</label>
                                    <input required="" autocomplete="off" type="password" name="password" class="form-control no-outline">  
                                </div>
                                <button type="submit" class="btn btn-warning" style="color: white">LOGIN</button>
                                @endif
                                
                            </form>
                        </div>
                        <div class="row">
                            <footer class="page-footer">
                                <div class="footer-copyright text-center py-3" style="color: #002853; font-family: 'Myriad Pro Regular'; font-size: 18px ">&#169; 2020 BPAD Provinsi DKI Jakarta</div>
                            </footer>
                        </div>
                    </div>
                    <div class="col-md-6" align="center">
                        <img src="{{ ('/elearning/public/landing/img/E-PENGAMANAN.png') }}" width="90%">
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ ('/elearning/public/landing/bootstrap400/js/bootstrap.min.js') }}"></script>
    </body>
</html>