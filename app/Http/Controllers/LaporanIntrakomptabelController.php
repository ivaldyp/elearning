<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PDF;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\ExcelTraits;

use App\Aset_quserid;
use App\Glo_profile_skpd;
use App\Dat_laporan;
use App\Dat_ttd;
use App\Log_susun_laporan;

session_start();

class LaporanIntrakomptabelController extends Controller
{
	use ExcelTraits;

	public function __construct()
	{
		set_time_limit(300000);
	}

	public function index(Request $request)
	{

		if ($request->wilnow) {
			if ($request->wilnow == "prov") {
				$wil = 0;
			} else {
				$wil = $request->wilnow;
			}
		} else {
			$wil = "all";
		}
		$wil = strval($wil);

		if ($request->yearnow) {
			$year = $request->yearnow;
		} else {
			$year = 2020;
		}

		$kolok = $_SESSION['kolok_laporan'] ?? '';

		// if ($request->searchnow) {
		//  $searchnow = $request->searchnow;
		// } else {
		//  $searchnow = '';
		// }

		$thisprofile = Glo_profile_skpd::
						where('kolok', $kolok)
						->where('tahun', $year)
						->first();

		if ($kolok == '') {
			$pds = Glo_profile_skpd::
					where('tahun', $year);
		} else {
			if ($thisprofile['kolok'] == $thisprofile['kolokskpd']) {
				$pds = Glo_profile_skpd::
						where('kolokskpd', $_SESSION['kolok_laporan'])
						->where('tahun', $year);
			} else {
				$pds = Glo_profile_skpd::
						where('kolok', $_SESSION['kolok_laporan'])
						->where('tahun', $year);
			}
		}

		if ($wil != "all") {
			$pds->whereRaw("kd_wil = '$wil'");
		}

		// if ($searchnow && $searchnow != '') {
		//  $pds->where(function($query) use($searchnow){
		//              $query->whereRaw("kolokskpd like '%".$searchnow."%'")
		//                  ->orWhereRaw("kolok like '%".$searchnow."%'")
		//                  ->orWhereRaw("nalok like '%".$searchnow."%'");
		//          });
		// }

		$pds->where(function($query){
						$query->whereNull('upb_sekolah')
						->orWhere('upb_sekolah', '');
					});
		$pds->orderBy('kolokskpd');
		$pds->orderBy('nalok');
		$pds->orderBy('kolok');

		$years = Glo_profile_skpd::
					distinct()
					->orderBy('tahun', 'desc')
					->get(['tahun']);

		$laporans = Dat_laporan::
					where('sts', 1)
					->where('tampilkan', 1)
					->orderBy('kode')
					->get();

		return view('pages.bpadlaporan.intrakomptabel')
				->with('koloknow', $kolok)
				// ->with('searchnow', $searchnow)
				->with('yearnow', $year)
				->with('wilnow', $request->wilnow)
				->with('thisprofile', $thisprofile)
				->with('pds', $pds->get())
				->with('years', $years)
				->with('laporans', $laporans);
	}

	public function excel(Request $request)
	{
		$kolok = $request->kolok;
		$output = $request->output;
		$wil = $request->wilayah;
		$splitkib = explode("::", $request->kib);
		$kib = $splitkib[0];
		$year = $request->tahun;
		$splitdurasi = explode("::", $request->durasi);

		$laporannow = Dat_laporan::
						where('sts', 1)
						->where('kode', $request->laporan)
						->first();
		// return json_encode($laporannow['ids']);

		if (is_null($request->kib)) {
			return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan KIB tidak boleh kosong')
					->with('msg_num', 2);
		} else {
			// $cekkib = explode("::", $request->kib)[0];
			$tblname = "bpadlaporandata.dbo." . $year . "_" . $kib . "_SALDOAWAL";
			$query = DB::select( DB::raw("
						IF OBJECT_ID('$tblname') IS NOT NULL
						   BEGIN
							  select 1 as nilai
						   END;
						ELSE
						   BEGIN
							  select 0 as nilai
						   END;"))[0];
			$query = json_decode(json_encode($query), true);
			if ($query['nilai'] == 0) {
				return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
				->with('message', 'Data KIB ' . $kib . ' tahun ' . $year . ' belum ada')
				->with('msg_num', 2);
			}

			$tblname = "bpadlaporandata.dbo." . $year . "_" . $kib . "_SALDOAKHIR";
			$query = DB::select( DB::raw("
						IF OBJECT_ID('$tblname') IS NOT NULL
						   BEGIN
							  select 1 as nilai
						   END;
						ELSE
						   BEGIN
							  select 0 as nilai
						   END;"))[0];
			$query = json_decode(json_encode($query), true);
			if ($query['nilai'] == 0) {
				return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
				->with('message', 'Data KIB ' . $kib . ' tahun ' . $year . ' belum ada')
				->with('msg_num', 2);
			}

			$tblname = "bpadlaporandata.dbo." . $year . "_" . $kib . "_REKAP";
			$query = DB::select( DB::raw("
						IF OBJECT_ID('$tblname') IS NOT NULL
						   BEGIN
							  select 1 as nilai
						   END;
						ELSE
						   BEGIN
							  select 0 as nilai
						   END;"))[0];
			$query = json_decode(json_encode($query), true);
			if ($query['nilai'] == 0) {
				return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
				->with('message', 'Data KIB ' . $kib . ' tahun ' . $year . ' belum ada')
				->with('msg_num', 2);
			}
		}

		if (is_null($request->kolok) || $request->kolok == '') {
			return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan kolok tidak boleh kosong')
					->with('msg_num', 2);
		}

		if (is_null($year) || $year == '') {
			return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan tahun tidak boleh kosong')
					->with('msg_num', 2);
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$alphabet = array('','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$colstart = 2;
		$rowstart = 2;

		// if ($output == 'pdf') {
		// 	$this->pdf($sheet, $rowstart, $colstart, $alphabet, $kolok, $output, $wil, $splitkib, $year, $splitdurasi, $laporannow);
		// 	// return 0;
		// } else {
			$result = $this->excelhead($sheet, $rowstart, $colstart, $alphabet, $year, $laporannow);
			$row = $result[0];
			$col = $result[1];

			$nowuser1 = Glo_profile_skpd::
						where('kolok', $kolok)
						->where('tahun', $year)
						->first();
			$nowuser1 = new Collection($nowuser1);

			$nowuser2 = Dat_ttd::
						where('sts', 1)
						->where('usname', 'AS'.$kolok.'2')
						->first();
			$nowuser2 = new Collection($nowuser2);

			$nowuser = $nowuser2->merge($nowuser1);

			$nowuserskpd = Glo_profile_skpd::
						where('kolok', $nowuser['kolokskpd'])
						->where('tahun', $year)
						->first();

			if ($nowuser['kolokskpd'] == $nowuser['kolok']) {
				$pd = ucwords(strtolower($nowuser['nalok']));
				$upd = 'NONE';
				$kolokpd = $nowuser['kolok'];
				$kolokupd = 'NONE';
			} else {
				$pd = ucwords(strtolower($nowuserskpd['nalok']));
				$upd = ucwords(strtolower($nowuser['nalok']));
				$kolokpd = $nowuserskpd['kolok'];
				$kolokupd = $nowuser['kolok'];
			}

			$result = $this->excelpd($sheet, $row, $col, $alphabet, $pd, $upd, $kolokpd, $kolokupd);
			$row = $result[0];
			$col = $result[1];

			$result = $this->exceltipelaporan($sheet, $row, $col, $alphabet, $laporannow);
			$row = $result[0];
			$col = $result[1];

			$result = $this->excelkib($sheet, $row, $col, $alphabet, $kib, $splitkib[1]);
			$row = $result[0];
			$col = $result[1];

			$nmtabelawal = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_" . strtoupper($splitdurasi[0]) . "]"; 
			$nmtabelawalrekap = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_REKAP]"; 
			$nmtabelakhir = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_" . strtoupper($splitdurasi[1]) . "]"; 
			$nmtabelakhirrekap = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_REKAP]"; 

			$cekrekap = DB::select( DB::raw("
						SELECT awal.*, bar.NABAR as nabarref
						FROM $nmtabelawalrekap awal
						JOIN bpadas.dbo.[ASET_QFATBBAR] bar on bar.KOBAR = awal.KOBAR
						WHERE awal.sts = 1
						AND awal.kolok = '$kolok'
						ORDER BY kobar
						"));
			$cekrekap = json_decode(json_encode($cekrekap), true);

			if (count($cekrekap) == 0) {

				//SALDO AWAL ---- SALDOAWAL
				$queryawal = DB::select( DB::raw("
							SELECT
							  kobar
							  , kolok
							  , sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0) + ISNULL(NILRENOV, 0)) as total
							  , count(kobar) as kuantitas
							from $nmtabelawal
							where kolok like '$kolok'
							AND sts='1' AND kd_app='1' AND (jukor_form NOT IN ('-','#','F','TK','RNV','RNX','AGD','H','I','J','K','L','M','PPAX','PPAD','G','O') OR isnull(jukor_form,'')='')
							GROUP BY
							kobar, kolok;
							"));
				$queryawal = json_decode(json_encode($queryawal), true);

				$temp_nmtabelawalrekap = str_replace("[", "", $nmtabelawalrekap);
				$temp_nmtabelawalrekap = str_replace("]", "", $temp_nmtabelawalrekap);

				if (count($queryawal) == 0) {
					# code...
				} else {
					foreach ($queryawal as $key => $data) {
						DB::table($temp_nmtabelawalrekap)->insert(
							['sts' => 1,
							 'uname' => Auth::user()->usname_skpd, 
							 'tgl' => date('Y-m-d'),
							 'usname' => Auth::user()->usname_skpd, 
							 'tahun' => $year,
							 'KOLOK' => $data['kolok'],
							 'NALOK' => '',
							 'KOBAR' => $data['kobar'],
							 'NABAR' => '',
							 'NOREG' => '',
							 'SATUAN' => ($data['satuan'] ?? ''),
							 'KUANTITAS_SALDOAWAL' => $data['kuantitas'] ?? 0,
							 'HARGA_SALDOAWAL' => $data['total'] ?? 0,

							]
						);
					}
				}


				//SALDO AKHIR ---- SALDOAKHIR
				$queryakhir = DB::select( DB::raw("
							SELECT
							kobar
							, kolok
							, sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0) + ISNULL(NILRENOV, 0)) as total
							, count(kobar) as kuantitas
							-- , (
							-- 	SELECT distinct(CASE
							-- 			WHEN tnskoreksi is null or tnskoreksi = '' THEN NULL
							-- 			ELSE (tnskoreksi + ' - ' + prof.nalok) + '::' 
							-- 			END) AS 'data()' 
							-- 	FROM $nmtabelakhir qa
							-- 	join bpadas.dbo.glo_profile_skpd as prof on prof.kolok = qa.KOLOKLAMA and prof.tahun = $year
							-- 	where qa.KOLOK = sakhir.KOLOK
							-- 	and qa.kobar = sakhir.kobar
							-- 	and qa.SATUAN = sakhir.satuan
							-- 	FOR XML PATH('')
							-- ) as keterangan
							from $nmtabelakhir sakhir
							where kolok like '$kolok'
							AND sts='1' AND kd_app='1' AND (jukor_form NOT IN ('-','#','F','TK','RNV','RNX','AGD','H','I','J','K','L','M','PPAX','PPAD','G','O') OR isnull(jukor_form,'')='')
							GROUP BY
							kobar, kolok;
							"));
				$queryakhir = json_decode(json_encode($queryakhir), true);	

				$temp_nmtabelakhirrekap = str_replace("[", "", $nmtabelakhirrekap);
				$temp_nmtabelakhirrekap = str_replace("]", "", $temp_nmtabelakhirrekap);

				if (count($queryakhir) == 0) {
					# code...
				} else {
					foreach ($queryakhir as $key => $data) {
						// $keterangan = substr($data['keterangan'], 0, 199);
						DB::table($temp_nmtabelakhirrekap)
							->updateOrInsert(
								[
									'KOLOK' => $kolok
									, 'KOBAR' => $data['kobar']
									// , 'SATUAN' => $data['satuan']
									, 'sts' => 1
								],
								[
									'uname' => Auth::user()->usname_skpd, 
									'tgl' => date('Y-m-d'),
									'usname' => Auth::user()->usname_skpd, 
									'tahun' => $year,
									'NALOK' => '',
									'NABAR' => '',
									'NOREG' => '',
									'SATUAN' => ($data['satuan'] ?? ''),
									'KUANTITAS_SALDOAKHIR' => $data['kuantitas'] ?? 0,
									'HARGA_SALDOAKHIR' => $data['total'] ?? 0,
									// 'KETERANGAN' => $keterangan,
								]
							);
					}
				}


				//SELECT REKAPHASILNYA
				$cekrekap = DB::select( DB::raw("
							SELECT awal.*, bar.NABAR as nabarref
							FROM $nmtabelawalrekap awal
							JOIN bpadas.dbo.[ASET_QFATBBAR] bar on bar.KOBAR = awal.KOBAR
							WHERE awal.sts = 1
							AND awal.kolok = '$kolok'
							ORDER BY kobar
							"));
				$cekrekap = json_decode(json_encode($cekrekap), true);

				foreach ($cekrekap as $key => $rekap) {
					$kobarnow = $rekap['KOBAR'];
					$qmutasitambah = DB::select( DB::raw("
								SELECT 
									sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0) + ISNULL(NILRENOV, 0)) as total, 
									count(kobar) as kuantitas
								FROM $nmtabelakhir sakhir
								where sakhir.kolok = '$kolok'
								and sakhir.kobar = '$kobarnow'
								AND sakhir.sts='1' AND sakhir.kd_app='1' AND (sakhir.jukor_form NOT IN ('-','#','F','TK','RNV','RNX','AGD','H','I','J','K','L','M','PPAX','PPAD','G','O') OR isnull(sakhir.jukor_form,'')='')
								and sakhir.NOREG not in (select sawal.NOREG 
															from $nmtabelawal sawal
															where sawal.kolok = '$kolok'
															  and sawal.kobar = '$kobarnow'
														  )
								"))[0];
					$qmutasitambah = json_decode(json_encode($qmutasitambah), true);

					if ($qmutasitambah['kuantitas'] + $rekap['KUANTITAS_SALDOAWAL'] == $rekap['KUANTITAS_SALDOAKHIR']) {
						DB::table($temp_nmtabelakhirrekap)
						  ->where('kolok', $kolok)
						  ->where('kobar', $rekap['KOBAR'])
						  ->where('sts', 1)
						  ->update([
								'TAMBAH_QTY' => $qmutasitambah['kuantitas'] ,
								'TAMBAH_HARGA' => $qmutasitambah['total'],
								'KURANG_QTY' => 0,
								'KURANG_HARGA' => 0,
							]);
					} else {
						$kurangqty = abs( $rekap['KUANTITAS_SALDOAKHIR'] - ($qmutasitambah['kuantitas'] + $rekap['KUANTITAS_SALDOAWAL']) );
						$kurangharga = abs( $rekap['HARGA_SALDOAKHIR'] - ($qmutasitambah['total'] + $rekap['HARGA_SALDOAWAL']) );

						DB::table($temp_nmtabelakhirrekap)
						  ->where('kolok', $kolok)
						  ->where('kobar', $rekap['KOBAR'])
						  ->where('sts', 1)
						  ->update([
								'TAMBAH_QTY' => $qmutasitambah['kuantitas'],
								'TAMBAH_HARGA' => $qmutasitambah['total'],
								'KURANG_QTY' => $kurangqty,
								'KURANG_HARGA' => $kurangharga,
							]);
					}
				}

				$cekrekap = DB::select( DB::raw("
							SELECT awal.*, bar.NABAR as nabarref
							FROM $nmtabelawalrekap awal
							JOIN bpadas.dbo.[ASET_QFATBBAR] bar on bar.KOBAR = awal.KOBAR
							WHERE awal.sts = 1
							AND awal.kolok = '$kolok'
							ORDER BY kobar
							"));
				$cekrekap = json_decode(json_encode($cekrekap), true);
			}

			$result = $this->excelK01($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd);
			$row = $result[0];
			$col = $result[1];

			$result = $this->excelfooter($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd, $laporannow, $kib, $kolok);
			$row = $result[0];
			$col = $result[1];

			$filename = $year.'_'.$kib.'_'.$kolok.'_LAPORAN';   
			// $objPHPExcel->getActiveSheet()->setTitle("Title");   
			if ($output == 'pdf') {
				header("Content-type:application/pdf");
				$filename .= '.pdf';
				$writer = IOFactory::createWriter($spreadsheet, 'Mpdf');
				// $writer   =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Tcpdf');
				// $writer   =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Dompdf');
				//$writer   =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');

			} else {
				// // Redirect output to a client's web browser (Xlsx)
				// header('Cache-Control: max-age=0');
				// // If you're serving to IE 9, then the following may be needed
				// header('Cache-Control: max-age=1');
				 
				// // If you're serving to IE over SSL, then the following may be needed
				// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				// header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
				// header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				// header('Pragma: public'); // HTTP/1.
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				$filename .= '.xlsx';
				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			}

			header('Content-Disposition: attachment;filename="'.$filename.'"');
			ob_end_clean();
			$writer->save('php://output');
			// return json_encode($filename);
		// }

			

		////////////////////////////////////////////////////////////////////////////////

			
	}

	// public function pdf($sheet, $row, $col, $alphabet, $kolok, $output, $wil, $splitkib, $year, $splitdurasi, $laporannow)
	public function pdf(Request $request)
	{

		$kolok = $request->kolok;
		$output = $request->output;
		$wil = $request->wilayah;
		$splitkib = explode("::", $request->kib);
		$kib = $splitkib[0];
		$year = $request->tahun;
		$splitdurasi = explode("::", $request->durasi);

		$laporannow = Dat_laporan::
						where('sts', 1)
						->where('kode', $request->laporan)
						->first();
		// return json_encode($laporannow['ids']);

		if (is_null($request->kib)) {
			return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan KIB tidak boleh kosong')
					->with('msg_num', 2);
		} else {
			// $cekkib = explode("::", $request->kib)[0];
			$tblname = "bpadlaporandata.dbo." . $year . "_" . $kib . "_SALDOAWAL";
			$query = DB::select( DB::raw("
						IF OBJECT_ID('$tblname') IS NOT NULL
						   BEGIN
							  select 1 as nilai
						   END;
						ELSE
						   BEGIN
							  select 0 as nilai
						   END;"))[0];
			$query = json_decode(json_encode($query), true);
			if ($query['nilai'] == 0) {
				return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
				->with('message', 'Data KIB ' . $kib . ' tahun ' . $year . ' belum ada')
				->with('msg_num', 2);
			}

			$tblname = "bpadlaporandata.dbo." . $year . "_" . $kib . "_SALDOAKHIR";
			$query = DB::select( DB::raw("
						IF OBJECT_ID('$tblname') IS NOT NULL
						   BEGIN
							  select 1 as nilai
						   END;
						ELSE
						   BEGIN
							  select 0 as nilai
						   END;"))[0];
			$query = json_decode(json_encode($query), true);
			if ($query['nilai'] == 0) {
				return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
				->with('message', 'Data KIB ' . $kib . ' tahun ' . $year . ' belum ada')
				->with('msg_num', 2);
			}

			$tblname = "bpadlaporandata.dbo." . $year . "_" . $kib . "_REKAP";
			$query = DB::select( DB::raw("
						IF OBJECT_ID('$tblname') IS NOT NULL
						   BEGIN
							  select 1 as nilai
						   END;
						ELSE
						   BEGIN
							  select 0 as nilai
						   END;"))[0];
			$query = json_decode(json_encode($query), true);
			if ($query['nilai'] == 0) {
				return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
				->with('message', 'Data KIB ' . $kib . ' tahun ' . $year . ' belum ada')
				->with('msg_num', 2);
			}
		}

		if (is_null($request->kolok) || $request->kolok == '') {
			return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan kolok tidak boleh kosong')
					->with('msg_num', 2);
		}

		if (is_null($year) || $year == '') {
			return redirect('/laporan/intrakomptabel?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan tahun tidak boleh kosong')
					->with('msg_num', 2);
		}

		$nowuser1 = Glo_profile_skpd::
					where('kolok', $kolok)
					->where('tahun', $year)
					->first();
		$nowuser1 = new Collection($nowuser1);

		$nowuser2 = Dat_ttd::
					where('sts', 1)
					->where('usname', 'AS'.$kolok.'2')
					->first();
		$nowuser2 = new Collection($nowuser2);

		$nowuser = $nowuser2->merge($nowuser1);

		$nowuserskpd = Glo_profile_skpd::
					where('kolok', $nowuser['kolokskpd'])
					->where('tahun', $year)
					->first();

		if ($nowuser['kolokskpd'] == $nowuser['kolok']) {
			$pd = ucwords(strtolower($nowuser['nalok']));
			$upd = 'NONE';
			$kolokpd = $nowuser['kolok'];
			$kolokupd = 'NONE';
		} else {
			$pd = ucwords(strtolower($nowuserskpd['nalok']));
			$upd = ucwords(strtolower($nowuser['nalok']));
			$kolokpd = $nowuserskpd['kolok'];
			$kolokupd = $nowuser['kolok'];
		}

		$nmtabelawal = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_" . strtoupper($splitdurasi[0]) . "]"; 
		$nmtabelawalrekap = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_REKAP]"; 
		$nmtabelakhir = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_" . strtoupper($splitdurasi[1]) . "]"; 
		$nmtabelakhirrekap = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_REKAP]"; 

		$cekrekap = DB::select( DB::raw("
					SELECT awal.*, bar.NABAR as nabarref
					FROM $nmtabelawalrekap awal
					JOIN bpadas.dbo.[ASET_QFATBBAR] bar on bar.KOBAR = awal.KOBAR
					WHERE awal.sts = 1
					AND awal.kolok = '$kolok'
					ORDER BY kobar
					"));
		$cekrekap = json_decode(json_encode($cekrekap), true);

		if (count($cekrekap) == 0) {

			//SALDO AWAL ---- SALDOAWAL
			$queryawal = DB::select( DB::raw("
						SELECT
						  kobar
						  , kolok
						  , sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0) + ISNULL(NILRENOV, 0)) as total
						  , count(kobar) as kuantitas
						from $nmtabelawal
						where kolok like '$kolok'
						AND sts='1' AND kd_app='1' AND (jukor_form NOT IN ('-','#','F','TK','RNV','RNX','AGD','H','I','J','K','L','M','PPAX','PPAD','G','O') OR isnull(jukor_form,'')='')
						GROUP BY
						kobar, kolok;
						"));
			$queryawal = json_decode(json_encode($queryawal), true);

			$temp_nmtabelawalrekap = str_replace("[", "", $nmtabelawalrekap);
			$temp_nmtabelawalrekap = str_replace("]", "", $temp_nmtabelawalrekap);

			if (count($queryawal) == 0) {
				# code...
			} else {
				foreach ($queryawal as $key => $data) {
					DB::table($temp_nmtabelawalrekap)->insert(
						['sts' => 1,
						 'uname' => Auth::user()->usname_skpd, 
						 'tgl' => date('Y-m-d'),
						 'usname' => Auth::user()->usname_skpd, 
						 'tahun' => $year,
						 'KOLOK' => $data['kolok'],
						 'NALOK' => '',
						 'KOBAR' => $data['kobar'],
						 'NABAR' => '',
						 'NOREG' => '',
						 'SATUAN' => ($data['satuan'] ?? ''),
						 'KUANTITAS_SALDOAWAL' => $data['kuantitas'] ?? 0,
						 'HARGA_SALDOAWAL' => $data['total'] ?? 0,

						]
					);
				}
			}


			//SALDO AKHIR ---- SALDOAKHIR
			$queryakhir = DB::select( DB::raw("
						SELECT
						kobar
						, kolok
						, sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0) + ISNULL(NILRENOV, 0)) as total
						, count(kobar) as kuantitas
						-- , (
						-- 	SELECT distinct(CASE
						-- 			WHEN tnskoreksi is null or tnskoreksi = '' THEN NULL
						-- 			ELSE (tnskoreksi + ' - ' + prof.nalok) + '::' 
						-- 			END) AS 'data()' 
						-- 	FROM $nmtabelakhir qa
						-- 	join bpadas.dbo.glo_profile_skpd as prof on prof.kolok = qa.KOLOKLAMA and prof.tahun = $year
						-- 	where qa.KOLOK = sakhir.KOLOK
						-- 	and qa.kobar = sakhir.kobar
						-- 	and qa.SATUAN = sakhir.satuan
						-- 	FOR XML PATH('')
						-- ) as keterangan
						from $nmtabelakhir sakhir
						where kolok like '$kolok'
						AND sts='1' AND kd_app='1' AND (jukor_form NOT IN ('-','#','F','TK','RNV','RNX','AGD','H','I','J','K','L','M','PPAX','PPAD','G','O') OR isnull(jukor_form,'')='')
						GROUP BY
						kobar, kolok;
						"));
			$queryakhir = json_decode(json_encode($queryakhir), true);	

			$temp_nmtabelakhirrekap = str_replace("[", "", $nmtabelakhirrekap);
			$temp_nmtabelakhirrekap = str_replace("]", "", $temp_nmtabelakhirrekap);

			if (count($queryakhir) == 0) {
				# code...
			} else {
				foreach ($queryakhir as $key => $data) {
					// $keterangan = substr($data['keterangan'], 0, 199);
					DB::table($temp_nmtabelakhirrekap)
						->updateOrInsert(
							[
								'KOLOK' => $kolok
								, 'KOBAR' => $data['kobar']
								// , 'SATUAN' => $data['satuan']
								, 'sts' => 1
							],
							[
								'uname' => Auth::user()->usname_skpd, 
								'tgl' => date('Y-m-d'),
								'usname' => Auth::user()->usname_skpd, 
								'tahun' => $year,
								'NALOK' => '',
								'NABAR' => '',
								'NOREG' => '',
								'SATUAN' => ($data['satuan'] ?? ''),
								'KUANTITAS_SALDOAKHIR' => $data['kuantitas'] ?? 0,
								'HARGA_SALDOAKHIR' => $data['total'] ?? 0,
								// 'KETERANGAN' => $keterangan,
							]
						);
				}
			}


			//SELECT REKAPHASILNYA
			$cekrekap = DB::select( DB::raw("
						SELECT awal.*, bar.NABAR as nabarref
						FROM $nmtabelawalrekap awal
						JOIN bpadas.dbo.[ASET_QFATBBAR] bar on bar.KOBAR = awal.KOBAR
						WHERE awal.sts = 1
						AND awal.kolok = '$kolok'
						ORDER BY kobar
						"));
			$cekrekap = json_decode(json_encode($cekrekap), true);

			foreach ($cekrekap as $key => $rekap) {
				$kobarnow = $rekap['KOBAR'];
				$qmutasitambah = DB::select( DB::raw("
							SELECT 
								sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0) + ISNULL(NILRENOV, 0)) as total, 
								count(kobar) as kuantitas
							FROM $nmtabelakhir sakhir
							where sakhir.kolok = '$kolok'
							and sakhir.kobar = '$kobarnow'
							AND sakhir.sts='1' AND sakhir.kd_app='1' AND (sakhir.jukor_form NOT IN ('-','#','F','TK','RNV','RNX','AGD','H','I','J','K','L','M','PPAX','PPAD','G','O') OR isnull(sakhir.jukor_form,'')='')
							and sakhir.NOREG not in (select sawal.NOREG 
														from $nmtabelawal sawal
														where sawal.kolok = '$kolok'
														  and sawal.kobar = '$kobarnow'
													  )
							"))[0];
				$qmutasitambah = json_decode(json_encode($qmutasitambah), true);

				if ($qmutasitambah['kuantitas'] + $rekap['KUANTITAS_SALDOAWAL'] == $rekap['KUANTITAS_SALDOAKHIR']) {
					DB::table($temp_nmtabelakhirrekap)
					  ->where('kolok', $kolok)
					  ->where('kobar', $rekap['KOBAR'])
					  ->where('sts', 1)
					  ->update([
							'TAMBAH_QTY' => $qmutasitambah['kuantitas'] ,
							'TAMBAH_HARGA' => $qmutasitambah['total'],
							'KURANG_QTY' => 0,
							'KURANG_HARGA' => 0,
						]);
				} else {
					$kurangqty = abs( $rekap['KUANTITAS_SALDOAKHIR'] - ($qmutasitambah['kuantitas'] + $rekap['KUANTITAS_SALDOAWAL']) );
					$kurangharga = abs( $rekap['HARGA_SALDOAKHIR'] - ($qmutasitambah['total'] + $rekap['HARGA_SALDOAWAL']) );

					DB::table($temp_nmtabelakhirrekap)
					  ->where('kolok', $kolok)
					  ->where('kobar', $rekap['KOBAR'])
					  ->where('sts', 1)
					  ->update([
							'TAMBAH_QTY' => $qmutasitambah['kuantitas'],
							'TAMBAH_HARGA' => $qmutasitambah['total'],
							'KURANG_QTY' => $kurangqty,
							'KURANG_HARGA' => $kurangharga,
						]);
				}
			}

			$cekrekap = DB::select( DB::raw("
						SELECT awal.*, bar.NABAR as nabarref
						FROM $nmtabelawalrekap awal
						JOIN bpadas.dbo.[ASET_QFATBBAR] bar on bar.KOBAR = awal.KOBAR
						WHERE awal.sts = 1
						AND awal.kolok = '$kolok'
						ORDER BY kobar
						"));
			$cekrekap = json_decode(json_encode($cekrekap), true);
		}

		// return view('pages.bpadlaporan.intraprev.preview');
		$pdf = PDF::setPaper('a4', 'landscape');
		$pdf->loadView('pages.bpadlaporan.preview.intrakomptabel', 
						[
							'tahun' => $year,
							'kolok' => $kolok,
							'pd' => $pd,
							'upd' => $upd,
							'kolokpd' => $kolokpd,
							'kolokupd' => $kolokupd,
							'laporannow' => $laporannow,
							'kib' => $splitkib,
							'cekrekap' => $cekrekap,
							'nowuser' => $nowuser,
						]);
		// return $pdf->stream('preview.pdf');
		return $pdf->download('KIB_'.$splitkib[0].'_'.$kolok.'.pdf');
	}
}
