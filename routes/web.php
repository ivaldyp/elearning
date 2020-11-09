<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('index');
// });

// Route::get('/home', function () {
//     return view('index');
// });

Route::get('/uwow', 'LandingController@testes');

Route::get('/', 'LandingController@index');
Route::get('/materi', 'LandingController@materi');
Route::get('/home', 'HomeController@index');
Route::post('/home/password', 'HomeController@password');
Route::get('/logout', 'LandingController@logout');

Route::group(['prefix' => 'tanda tangan'], function () {
	Route::get('/', 'TtdController@index');
	Route::post('/form/tambahttd', 'TtdController@forminsertttd');
	Route::post('/form/hapusttd', 'TtdController@formdeletettd');
});

Route::group(['prefix' => 'setup'], function () {
	Route::get('/laporan', 'SetupController@laporanall');
	Route::post('/form/tambahlaporan', 'SetupController@forminsertlaporan');
	Route::post('/form/ubahlaporan', 'SetupController@formupdatelaporan');
	Route::post('/form/hapuslaporan', 'SetupController@formdeletelaporan');

	Route::get('/db', 'SetupController@dball');
	Route::post('/form/resetdb', 'SetupController@formresetdb');
});

// Route::group(['prefix' => 'lbkp'], function () {
// 	Route::get('/gabungan/rekap', 'LbkpGabunganController@rekap');
// 	Route::get('/lapgabunganrekap', 'LbkpGabunganController@excelrekap');
// 	Route::get('/gabungan/detail', 'LbkpGabunganController@detail');
// 	Route::get('/lapgabungandetail', 'LbkpGabunganController@exceldetail');
// });

Route::group(['prefix' => 'laporan'], function () {
	Route::get('/', 'LaporanController@index');
	Route::post('/excel', 'LaporanController@excel');

	Route::get('/intrakomptabel', 'LaporanIntrakomptabelController@index');
	Route::get('/intrakomptabel/excel', 'LaporanIntrakomptabelController@excel');
	Route::get('/intrakomptabel/pdf', 'LaporanIntrakomptabelController@pdf');

	Route::get('/ekstrakomptabel', 'LaporanEkstrakomptabelController@index');
	Route::get('/ekstrakomptabel/excel', 'LaporanEkstrakomptabelController@excel');
	Route::get('/ekstrakomptabel/pdf', 'LaporanEkstrakomptabelController@pdf');
	// Route::get('/intrakomptabel/pdf', function(){
	// 	// return view('pages.bpadlaporan.intraprev.preview');
	// 	$pdf = PDF::loadView('pages.bpadlaporan.intraprev.preview');
	// 	return $pdf->stream('preview.pdf');
	// });

});

Route::group(['prefix' => 'cms'], function () {
	Route::get('/menu', 'CmsController@menuall');
	Route::post('/form/tambahmenu', 'CmsController@forminsertmenu');
	Route::post('/form/ubahmenu', 'CmsController@formupdatemenu');
	Route::post('/form/hapusmenu', 'CmsController@formdeletemenu');
	Route::get('/menuakses', 'CmsController@menuakses');
	Route::post('/form/ubahaccess', 'CmsController@formupdateaccess');
});

Route::group(['prefix' => 'kepegawaian'], function () {
	Route::get('/excel', 'KepegawaianController@printexcel');
	Route::get('/excelpegawai', 'KepegawaianController@printexcelpegawai');

	Route::get('/data pegawai', 'KepegawaianController@pegawaiall');
	Route::get('/tambah pegawai', 'KepegawaianController@pegawaitambah');
	Route::get('/ubah pegawai', 'KepegawaianController@pegawaiubah');
	Route::post('/form/tambahpegawai', 'KepegawaianController@forminsertpegawai');
	Route::post('/form/ubahpegawai', 'KepegawaianController@formupdatepegawai');
	Route::post('/form/hapuspegawai', 'KepegawaianController@formdeletepegawai');
	Route::post('/form/ubahpassuser', 'KepegawaianController@formupdatepassuser');
	Route::post('/form/ubahstatuspegawai', 'KepegawaianController@formupdatestatuspegawai');
	Route::post('/form/tambahdikpegawai', 'KepegawaianController@forminsertdikpegawai');
	Route::post('/form/ubahdikpegawai', 'KepegawaianController@formupdatedikpegawai');
	Route::post('/form/hapusdikpegawai', 'KepegawaianController@formdeletedikpegawai');
	Route::post('/form/tambahgolpegawai', 'KepegawaianController@forminsertgolpegawai');
	Route::post('/form/ubahgolpegawai', 'KepegawaianController@formupdategolpegawai');
	Route::post('/form/hapusgolpegawai', 'KepegawaianController@formdeletegolpegawai');
	Route::post('/form/tambahjabpegawai', 'KepegawaianController@forminsertjabpegawai');
	Route::post('/form/ubahjabpegawai', 'KepegawaianController@formupdatejabpegawai');
	Route::post('/form/hapusjabpegawai', 'KepegawaianController@formdeletejabpegawai');

	Route::get('/struktur', 'KepegawaianController@strukturorganisasi');

	Route::get('/entri kinerja', 'KepegawaianController@entrikinerja');
	Route::post('/kinerja tambah', 'KepegawaianController@kinerjatambah');
	Route::get('/getaktivitas', 'KepegawaianController@getaktivitas');
	Route::get('/getdetailaktivitas', 'KepegawaianController@getdetailaktivitas');
	Route::post('/form/tambahkinerja', 'KepegawaianController@forminsertkinerja');
	Route::post('/form/hapuskinerja', 'KepegawaianController@formdeletekinerja');
	Route::post('/form/tambahaktivitas', 'KepegawaianController@forminsertaktivitas');
	Route::get('/form/hapusaktivitas', 'KepegawaianController@formdeleteaktivitas');

	Route::get('/approve kinerja', 'KepegawaianController@approvekinerja');
	Route::post('/form/approvekinerja', 'KepegawaianController@formapprovekinerja');
	Route::post('/form/approvekinerjasingle', 'KepegawaianController@formapprovekinerjasingle');

	Route::get('/laporan kinerja', 'KepegawaianController@laporankinerja');

	Route::get('/status disposisi', 'KepegawaianController@statusdisposisi');

	Route::get('/surat keluar', 'KepegawaianController@suratkeluar');
	Route::get('/surat keluar tambah', 'KepegawaianController@suratkeluartambah');
	Route::post('/surat keluar ubah', 'KepegawaianController@suratkeluarubah');
	Route::post('/form/tambahsuratkeluar', 'KepegawaianController@forminsertsuratkeluar');
	Route::post('/form/ubahsuratkeluar', 'KepegawaianController@formupdatesuratkeluar');
	Route::post('/form/hapussuratkeluar', 'KepegawaianController@formdeletesuratkeluar');
});

Route::group(['prefix' => 'security'], function () {
	Route::get('/group user', 'SecurityController@grupall');
	Route::get('/group user/ubah', 'SecurityController@grupubah');
	Route::post('/form/tambahgrup', 'SecurityController@forminsertgrup');
	Route::post('/form/ubahgrup', 'SecurityController@formupdategrup');
	Route::post('/form/hapusgrup', 'SecurityController@formdeletegrup');

	Route::get('/tambah user', 'SecurityController@tambahuser');
	Route::post('/form/tambahuser', 'SecurityController@forminsertuser');

	Route::get('/manage user', 'SecurityController@manageuser');
	Route::post('/form/tambahuser', 'SecurityController@forminsertuser');
	Route::post('/form/ubahuser', 'SecurityController@formupdateuser');
	Route::post('/form/ubahpassuser', 'SecurityController@formupdatepassuser');
	Route::post('/form/hapususer', 'SecurityController@formdeleteuser');
});
Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
