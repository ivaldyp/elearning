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

class LaporanPersediaanController extends Controller
{
	use ExcelTraits;

	public function __construct()
	{
		$this->middleware('auth');
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

		return view('pages.bpadlaporan.persediaan')
				->with('koloknow', $kolok)
				// ->with('searchnow', $searchnow)
				->with('yearnow', $year)
				->with('wilnow', $request->wilnow)
				->with('thisprofile', $thisprofile)
				->with('pds', $pds->get());
	}

	public function excel(Request $request)
	{
		$tahun = $request->tahun;
		$periode = $request->periode;
		$kolok = $request->kolok;

		$laporannow = Dat_laporan::
						where('sts', 1)
						->where('kode', $request->laporan)
						->first();

		$nowuser1 = Glo_profile_skpd::
					where('kolok', $kolok)
					->where('tahun', $tahun)
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
					->where('tahun', $tahun)
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


		if ($tahun < date('Y')) {
			$month = 12;
		} else {
			$month = (int) date('m');
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$alphabet = array('','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$colstart = 2;
		$rowstart = 2;

		$result = $this->excelhead($sheet, $rowstart, $colstart, $alphabet, $tahun, $laporannow);
		$row = $result[0];
		$col = $result[1];

		$result = $this->excelpd($sheet, $row, $col, $alphabet, $pd, $upd, $kolokpd, $kolokupd);
		$row = $result[0];
		$col = $result[1];

		$result = $this->exceltipelaporan($sheet, $row, $col, $alphabet, $laporannow);
		$row = $result[0];
		$col = $result[1];

		$result = $this->excelperiodepersediaan($sheet, $row, $col, $alphabet, $periode);
		$row = $result[0];
		$col = $result[1];

		$persediaan = $this->loaddata($tahun, $month, $periode, $kolok);

		$result = $this->excelK03($sheet, $row, $col, $alphabet, $tahun, $persediaan, $nowuser, $pd, $upd);
		$row = $result[0];
		$col = $result[1];

		$result = $this->excelfooter($sheet, $row, $col, $alphabet, $tahun, $nowuser, $pd, $upd, $laporannow, $kolok);
		$row = $result[0];
		$col = $result[1];

		$filename = 'LAPORAN_PERSEDIAAN_'.$tahun.'_'.$kolok;

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$filename .= '.xlsx';
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		ob_end_clean();
		$writer->save('php://output');
	}

	public function pdf(Request $request)
	{
		$tahun = $request->tahun;
		$periode = $request->periode;
		$kolok = $request->kolok;

		$nowuser1 = Glo_profile_skpd::
					where('kolok', $kolok)
					->where('tahun', $tahun)
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
					->where('tahun', $tahun)
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


		if ($tahun < date('Y')) {
			$month = 12;
		} else {
			$month = (int) date('m');
		}

		$persediaan = $this->loaddata($tahun, $month, $periode, $kolok);
		
		// return view('pages.bpadlaporan.intraprev.preview');
		$pdf = PDF::setPaper('a4', 'landscape');
		$pdf->loadView('pages.bpadlaporan.preview.persediaan', 
						[
							'tahun' => $tahun,
							'kolok' => $kolok,
							'pd' => $pd,
							'upd' => $upd,
							'kolokpd' => $kolokpd,
							'kolokupd' => $kolokupd,
							'laporannow' => "Persediaan",
							'persediaan' => $persediaan,
							'periode' => $periode,
							'nowuser' => $nowuser,
						]);
		// return $pdf->stream('preview.pdf');
		return $pdf->download('LAPORAN_PERSEDIAAN_'.$tahun.'_'.$kolok.'.pdf');
		// return $pdf->download('KIB_'.$splitkib[0].'_'.$kolok.'.pdf');
	}

	public function view(Request $request)
	{
		$tahun = $request->tahun;
		$periode = $request->periode;
		$kolok = $request->kolok;

		$nowuser1 = Glo_profile_skpd::
					where('kolok', $kolok)
					->where('tahun', $tahun)
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
					->where('tahun', $tahun)
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


		if ($tahun < date('Y')) {
			$month = 12;
		} else {
			$month = (int) date('m');
		}

		$persediaan = $this->loaddata($tahun, $month, $periode, $kolok);

		return view('pages.bpadlaporan.preview.persediaan')
				->with('tahun', $tahun)
				->with('kolok', $kolok)
				->with('pd', $pd)
				->with('upd', $upd)
				->with('kolokpd', $kolokpd)
				->with('kolokupd', $kolokupd)
				->with('laporannow', "Persediaan")
				->with('persediaan', $persediaan)
				->with('periode', $periode)
				->with('nowuser', $nowuser);
	}

	protected function loaddata(int $tahun, int $month, int $periode, string $kolok)
	{
		$client = new \GuzzleHttp\Client();

		$persediaan = [];
		$persediaan['habis']['awalqty'] = 0;
		$persediaan['habis']['tambahqty'] = 0;
		$persediaan['habis']['kurangqty'] = 0;
		$persediaan['habis']['awalnil'] = 0;
		$persediaan['habis']['tambahnil'] = 0;
		$persediaan['habis']['kurangnil'] = 0;
		$persediaan['bahan']['awalqty'] = 0;
		$persediaan['bahan']['tambahqty'] = 0;
		$persediaan['bahan']['kurangqty'] = 0;
		$persediaan['bahan']['awalnil'] = 0;
		$persediaan['bahan']['tambahnil'] = 0;
		$persediaan['bahan']['kurangnil'] = 0;
		$persediaan['lain']['awalqty'] = 0;
		$persediaan['lain']['tambahqty'] = 0;
		$persediaan['lain']['kurangqty'] = 0;
		$persediaan['lain']['awalnil'] = 0;
		$persediaan['lain']['tambahnil'] = 0;
		$persediaan['lain']['kurangnil'] = 0;

		if ($periode == 1) {
			
			//WS saldoawal
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=saldoawal&tahun='.$tahun);
			$dataawal = json_decode($response->getBody());

			//WS mutasitambah
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=mutasit&tahun='.$tahun.'&bulan=12&tipemutasi=2');
			$datatambah = json_decode($response->getBody());

			//WS mutasiakhir
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=mutasik&tahun='.$tahun.'&bulan=12&tipemutasi=2');
			$datakurang = json_decode($response->getBody());

		} elseif ($periode == 2) {
			if ($month > 6) {
				$month = 6;
			}

			//WS saldoawal
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=saldoawal&tahun='.$tahun);
			$dataawal = json_decode($response->getBody());

			//WS mutasitambah
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=mutasit&tahun='.$tahun.'&bulan='.$month.'&tipemutasi=2');
			$datatambah = json_decode($response->getBody());

			//WS mutasiakhir
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=mutasik&tahun='.$tahun.'&bulan='.$month.'&tipemutasi=2');
			$datakurang = json_decode($response->getBody());
			
		} elseif ($periode == 3) {
			if ($month < 7) {
				$month = 6;
			}

			//WS saldoawal
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=saldoawal&tahun='.$tahun);
			$dataawal = json_decode($response->getBody());

			//WS mutasitambah
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=mutasit&tahun='.$tahun.'&bulan='.$month.'&tipemutasi=2');
			$datatambah = json_decode($response->getBody());

			//WS mutasiakhir
			$response = $client->request('GET', 'https://aset.jakarta.go.id/ws/persediaan.aspx?u=bpadws&p=!@bpad_dki@!&tipe=mutasik&tahun='.$tahun.'&bulan='.$month.'&tipemutasi=2');
			$datakurang = json_decode($response->getBody());
		}

		//saldoawal
		foreach ($dataawal->hasil as $key => $data) {
			if ($data->kolok == $kolok) {
				if ($data->jnsbrg == 'Persediaan Bahan Pakai Habis') {
					$persediaan['habis']['awalqty'] += floor($data->jmlbrg);
					$persediaan['habis']['awalnil'] += floor($data->hrgtotal);
				} elseif ($data->jnsbrg == 'Persediaan Bahan/Material') {
					$persediaan['bahan']['awalqty'] += floor($data->jmlbrg);
					$persediaan['bahan']['awalnil'] += floor($data->hrgtotal);
				} elseif ($data->jnsbrg == 'Persediaan Barang Lainnya') {
					$persediaan['lain']['awalqty'] += floor($data->jmlbrg);
					$persediaan['lain']['awalnil'] += floor($data->hrgtotal);
				}
			}	
		}

		//mutasi tambah
		foreach ($datatambah->hasil as $key => $data) {
			if ($data->kolok == $kolok) {
				if ($data->jnsbrg == 'Persediaan Bahan Pakai Habis') {
					$persediaan['habis']['tambahqty'] += floor($data->jmlbrg);
					$persediaan['habis']['tambahnil'] += floor($data->hrgtotal);
				} elseif ($data->jnsbrg == 'Persediaan Bahan/Material') {
					$persediaan['bahan']['tambahqty'] += floor($data->jmlbrg);
					$persediaan['bahan']['tambahnil'] += floor($data->hrgtotal);
				} elseif ($data->jnsbrg == 'Persediaan Barang Lainnya') {
					$persediaan['lain']['tambahqty'] += floor($data->jmlbrg);
					$persediaan['lain']['tambahnil'] += floor($data->hrgtotal);
				}
			}	
		}

		//mutasi kurang
		foreach ($datakurang->hasil as $key => $data) {
			if ($data->kolok == $kolok) {
				if ($data->jnsbrg == 'Persediaan Bahan Pakai Habis') {
					$persediaan['habis']['kurangqty'] += floor($data->jmlbrg);
					$persediaan['habis']['kurangnil'] += floor($data->hrgtotal);
				} elseif ($data->jnsbrg == 'Persediaan Bahan/Material') {
					$persediaan['bahan']['kurangqty'] += floor($data->jmlbrg);
					$persediaan['bahan']['kurangnil'] += floor($data->hrgtotal);
				} elseif ($data->jnsbrg == 'Persediaan Barang Lainnya') {
					$persediaan['lain']['kurangqty'] += floor($data->jmlbrg);
					$persediaan['lain']['kurangnil'] += floor($data->hrgtotal);
				}
			}	
		}

		//saldoakhir
		$persediaan['habis']['akhirqty'] 
		= ($persediaan['habis']['awalqty'] ?? 0) 
		+ ($persediaan['habis']['tambahqty'] ?? 0) 
		- ($persediaan['habis']['kurangqty'] ?? 0);
		
		$persediaan['habis']['akhirnil'] 
		= ($persediaan['habis']['awalnil'] ?? 0) 
		+ ($persediaan['habis']['tambahnil'] ?? 0) 
		- ($persediaan['habis']['kurangnil'] ?? 0);
		
		$persediaan['bahan']['akhirqty'] 
		= ($persediaan['bahan']['awalqty'] ?? 0) 
		+ ($persediaan['bahan']['tambahqty'] ?? 0) 
		- ($persediaan['bahan']['kurangqty'] ?? 0);
		
		$persediaan['bahan']['akhirnil'] 
		= ($persediaan['bahan']['awalnil'] ?? 0) 
		+ ($persediaan['bahan']['tambahnil'] ?? 0) 
		- ($persediaan['bahan']['kurangnil'] ?? 0);
		
		$persediaan['lain']['akhirqty'] 
		= ($persediaan['lain']['awalqty'] ?? 0) 
		+ ($persediaan['lain']['tambahqty'] ?? 0) 
		- ($persediaan['lain']['kurangqty'] ?? 0);
		
		$persediaan['lain']['akhirnil'] 
		= ($persediaan['lain']['awalnil'] ?? 0) 
		+ ($persediaan['lain']['tambahnil'] ?? 0) 
		- ($persediaan['lain']['kurangnil'] ?? 0);

		return $persediaan;
	}
}
