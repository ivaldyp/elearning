<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\SessionCheckTraits;

use App\Aset_quserid;
use App\Glo_profile_skpd;
use App\Dat_laporan;
use App\Dat_ttd;
use App\Log_susun_laporan;

session_start();

class LaporanController extends Controller
{
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

		$kolok = $_SESSION['kolok'] ?? '';

		// if ($request->searchnow) {
		// 	$searchnow = $request->searchnow;
		// } else {
		// 	$searchnow = '';
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
						where('kolokskpd', $_SESSION['kolok'])
						->where('tahun', $year);
			} else {
				$pds = Glo_profile_skpd::
						where('kolok', $_SESSION['kolok'])
						->where('tahun', $year);
			}
		}

		if ($wil != "all") {
			$pds->whereRaw("kd_wil = '$wil'");
		}

		// if ($searchnow && $searchnow != '') {
		// 	$pds->where(function($query) use($searchnow){
		// 			    $query->whereRaw("kolokskpd like '%".$searchnow."%'")
		// 					->orWhereRaw("kolok like '%".$searchnow."%'")
		// 					->orWhereRaw("nalok like '%".$searchnow."%'");
		// 			});
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

		return view('pages.bpadlaporan.rekap')
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
		// $kolok = $request->kolok;
		$year = $request->tahun;
		$wil = $request->wilayah;
		$splitdurasi = explode("::", $request->durasi);

		$laporannow = Dat_laporan::
						where('sts', 1)
						->where('kode', $request->laporan)
						->first();

		if (is_null($request->kib)) {
			return redirect('/laporan?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan KIB tidak boleh kosong')
					->with('msg_num', 2);
		} else {
			foreach ($request->kib as $key => $cekkib) {
				$tblname = "bpadlaporandata.dbo." . $year . "_" . $cekkib . "_SALDOAWAL";
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
					return redirect('/laporan?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Data KIB ' . $cekkib . ' tahun ' . $year . ' belum ada')
					->with('msg_num', 2);
				}

				$tblname = "bpadlaporandata.dbo." . $year . "_" . $cekkib . "_SALDOAKHIR";
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
					return redirect('/laporan?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Data KIB ' . $cekkib . ' tahun ' . $year . ' belum ada')
					->with('msg_num', 2);
				}

				$tblname = "bpadlaporandata.dbo." . $year . "_" . $cekkib . "_REKAP";
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
					return redirect('/laporan?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Data KIB ' . $cekkib . ' tahun ' . $year . ' belum ada')
					->with('msg_num', 2);
				}
			}
		}

		if (is_null($request->kolok) || $request->kolok == '') {
			return redirect('/laporan?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan kolok tidak boleh kosong')
					->with('msg_num', 2);
		}

		if (is_null($year) || $year == '') {
			return redirect('/laporan?yearnow='.$year.'&wilnow='.$wil)
					->with('message', 'Pilihan tahun tidak boleh kosong')
					->with('msg_num', 2);
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$colstart = 1;
		$rowstart = 2;

		$result = $this->excelhead($sheet, $rowstart, $colstart, $alphabet, $year, $laporannow);
		$row = $result[0];
		$col = $result[1];

		foreach ($request->kolok as $key => $kolok) {
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

			if ($request->kib[0] == 'semua') {
				$logsusun = Log_susun_laporan::
						where('sts', 1)
						->where('kolok', $kolok)
						->where('tahun', $year);
			} else {
				if (!(isset($col))) {
					$col = $colstart;
				}

				if (!(isset($row))) {
					$row = $rowstart;
				}

				foreach ($request->kib as $key => $kib) {
					// $tabelawal = $year . "_" . $kib . "_" . strtoupper($splitdurasi[0]);
					// $tabelakhir = $year . "_" . $kib . "_" . strtoupper($splitdurasi[1]);
					// $nmtabelawal = "bpadlaporandata.dbo.[" . $tabelawal . "]"; 
					// $nmtabelawalrekap = "bpadlaporandata.dbo.[" . $tabelawal . "]"; 
					// $nmtabelakhir = "bpadlaporandata.dbo.[" . $tabelakhir . "]"; 
					// $nmtabelakhirrekap = "bpadlaporandata.dbo.[" . $tabelakhir . "]"; 

						 

					//TABLE HEAD

					$result = $this->excelkib($sheet, $row, $col, $alphabet, $kib);
					$row = $result[0];
					$col = $result[1];

					if ($laporannow['kode'] == "K01" || $laporannow['kode'] == "K05" || $laporannow['kode'] == "K06") {
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
									"));
						$cekrekap = json_decode(json_encode($cekrekap), true);

						if (count($cekrekap) == 0) {

							//SALDO AWAL ---- SALDOAWAL
							$queryawal = DB::select( DB::raw("
										SELECT
									      kobar
										  , kolok
										  , satuan
										  , sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0)) as total
										  , count(kobar) as kuantitas
										from $nmtabelawal
										where kolok like '$kolok'
										GROUP BY
									    kobar, kolok, satuan;
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
									     'KUANTITAS_SALDOAWAL' => $data['kuantitas'],
									     'HARGA_SALDOAWAL' => $data['total'],

									 	]
									);
								}
							}


							//SALDO AKHIR ---- SALDOAKHIR
							$queryakhir = DB::select( DB::raw("
										SELECT
									      kobar
										  , kolok
										  , satuan
										  , sum(ISNULL(harga, 0) + ISNULL(jukor_niladd, 0) + ISNULL(jukor_nilai, 0) + ISNULL(jukor_kapitalisasi, 0)) as total
										  , count(kobar) as kuantitas
										from $nmtabelakhir
										where kolok like '$kolok'
										GROUP BY
									    kobar, kolok, satuan;
									    "));
							$queryakhir = json_decode(json_encode($queryakhir), true);	

							$temp_nmtabelakhirrekap = str_replace("[", "", $nmtabelakhirrekap);
							$temp_nmtabelakhirrekap = str_replace("]", "", $temp_nmtabelakhirrekap);

							if (count($queryakhir) == 0) {
								# code...
							} else {
								foreach ($queryakhir as $key => $data) {
									// $cekudahadabelom = DB::select( DB::raw("
									// 			SELECT count(kobar)
									// 			from $nmtabelakhirrekap
									// 			where kolok like '$kolok'
									// 			AND kobar like '$data['kobar']'
									// 			AND satuan like '$data['satuan']'
									// 		    "));
									// $cekudahadabelom = json_decode(json_encode($cekudahadabelom), true);

									// if ($cekudahadabelom == 0) {
									// 	DB::table($temp_nmtabelakhirrekap)->insert(
									// 	    ['sts' => 1,
									// 	     'uname' => Auth::user()->usname_skpd, 
									// 	     'tgl' => date('Y-m-d'),
									// 	     'usname' => Auth::user()->usname_skpd, 
									// 	     'tahun' => $year,
									// 	     'KOLOK' => $data['kolok'],
									// 	     'NALOK' => '',
									// 	     'KOBAR' => $data['kobar'],
									// 	     'NABAR' => '',
									// 	     'NOREG' => '',
									// 	     'SATUAN' => ($data['satuan'] ?? ''),
									// 	     'KUANTITAS_SALDOAKHIR' => $data['kuantitas'],
									// 	     'HARGA_SALDOAKHIR' => $data['total'],

									// 	 	]
									// 	);
									// } else {

									// }

									DB::table($temp_nmtabelakhirrekap)
									    ->updateOrInsert(
									        ['KOLOK' => $kolok, 'KOBAR' => $data['kobar'], 'SATUAN' => $data['satuan'], 'sts' => 1],
									        [
												'uname' => Auth::user()->usname_skpd, 
												'tgl' => date('Y-m-d'),
												'usname' => Auth::user()->usname_skpd, 
												'tahun' => $year,
												'NALOK' => '',
												'NABAR' => '',
												'NOREG' => '',
												'SATUAN' => ($data['satuan'] ?? ''),
												'KUANTITAS_SALDOAKHIR' => $data['kuantitas'],
												'HARGA_SALDOAKHIR' => $data['total'],
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
										"));
							$cekrekap = json_decode(json_encode($cekrekap), true);
						}

						$result = $this->excelK01($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd);
					} elseif ($laporannow['kode'] == "K02") {
						# code...
					} elseif ($laporannow['kode'] == "K03") {
						# code...
					} elseif ($laporannow['kode'] == "K04") {
						# code...
					}
					$row = $result[0];
					$col = $result[1];
				}
				$result = $this->excelfooter($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd, $laporannow);
				$row = $result[0];
				$col = $result[1];
			}
		}

		$filename = $year.'_LAPORAN'.'.xlsx';		
		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
	}

	public function excelhead($sheet, $row, $col, $alphabet, $year, $laporan)
	{
		//HEADER DAN JUDUL
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$col].$row, "LAPORAN BARANG KUASA PENGGUNA");

		$row++;
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$col].$row, "LAPORAN ".strtoupper($laporan['jns_laporan']));

		$row++;
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$col].$row, "PER SUB-SUB RINCIAN OBJEK BARANG");

		$row++;
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$col].$row, "TAHUN ANGGARAN : ".$year);
		$sheet->getStyle($alphabet[$col].($row-4) . ':' . $alphabet[$laporan['back_column']-1].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col].($row-4) . ':' . $alphabet[$laporan['back_column']-1].$row)->getAlignment()->setVertical('center');

		$arr = array($row, $col);

		return $arr;
	}

	public function excelpd($sheet, $row, $col, $alphabet, $pd, $upd, $kolokpd, $kolokupd)
	{
		$row+=2;
		$sheet->setCellValue($alphabet[$col].$row, 'PD');
		$sheet->setCellValue($alphabet[$col+1].$row, ': '.$kolokpd);
		$sheet->setCellValue($alphabet[$col+2].$row, strtoupper($pd));

		if ($upd != "NONE") {
			$row++;
			$sheet->setCellValue($alphabet[$col].$row, 'UPD');
			$sheet->setCellValue($alphabet[$col+1].$row, ': '.$kolokupd);
			$sheet->setCellValue($alphabet[$col+2].$row, strtoupper($upd));			
		}

		$arr = array($row, $col);

		return $arr;
	}

	public function excelkib($sheet, $row, $col, $alphabet, $kib)
	{
		$row+=2;
		$sheet->setCellValue($alphabet[$col].$row, 'KIB');
		$sheet->setCellValue($alphabet[$col+1].$row, ': '.$kib);

		$arr = array($row, $col);

		return $arr;
	}

	public function excelK01($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd)
	{
		$row++;
		$sheet->setCellValue($alphabet[$col].$row, 'Sub-Sub Rincian Objek');
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+1].($row+1));

		$sheet->setCellValue($alphabet[$col+2].$row, 'Satuan');
		$sheet->mergeCells($alphabet[$col+2].$row.':'.$alphabet[$col+2].($row+2));

		$sheet->setCellValue($alphabet[$col+3].$row, 'Saldo Awal Per Januari '.$year);
		$sheet->mergeCells($alphabet[$col+3].$row.':'.$alphabet[$col+4].($row+1));

		$sheet->setCellValue($alphabet[$col+5].$row, 'Mutasi');
		$sheet->mergeCells($alphabet[$col+5].$row.':'.$alphabet[$col+8].$row);

		$sheet->setCellValue($alphabet[$col+9].$row, 'Saldo Akhir Per '.$year);
		$sheet->mergeCells($alphabet[$col+9].$row.':'.$alphabet[$col+10].($row+1));

		$row++;
		$sheet->setCellValue($alphabet[$col+5].$row, 'Bertambah');
		$sheet->mergeCells($alphabet[$col+5].$row.':'.$alphabet[$col+6].$row);

		$sheet->setCellValue($alphabet[$col+7].$row, 'Berkurang');
		$sheet->mergeCells($alphabet[$col+7].$row.':'.$alphabet[$col+8].$row);

		$row++;
		$sheet->setCellValue($alphabet[$col].$row, 'Kode');
		$sheet->setCellValue($alphabet[$col+1].$row, 'Uraian');
		$sheet->setCellValue($alphabet[$col+3].$row, 'Kuantitas');
		$sheet->setCellValue($alphabet[$col+4].$row, 'Nilai');
		$sheet->setCellValue($alphabet[$col+5].$row, 'Kuantitas');
		$sheet->setCellValue($alphabet[$col+6].$row, 'Nilai');
		$sheet->setCellValue($alphabet[$col+7].$row, 'Kuantitas');
		$sheet->setCellValue($alphabet[$col+8].$row, 'Nilai');
		$sheet->setCellValue($alphabet[$col+9].$row, 'Kuantitas');
		$sheet->setCellValue($alphabet[$col+10].$row, 'Nilai');

		$row++;
		$sheet->setCellValue($alphabet[$col].$row, '1');
		$sheet->setCellValue($alphabet[$col+1].$row, '2');
		$sheet->setCellValue($alphabet[$col+2].$row, '3');
		$sheet->setCellValue($alphabet[$col+3].$row, '4');
		$sheet->setCellValue($alphabet[$col+4].$row, '5');
		$sheet->setCellValue($alphabet[$col+5].$row, '6');
		$sheet->setCellValue($alphabet[$col+6].$row, '7');
		$sheet->setCellValue($alphabet[$col+7].$row, '8');
		$sheet->setCellValue($alphabet[$col+8].$row, '9');
		$sheet->setCellValue($alphabet[$col+9].$row, '10');
		$sheet->setCellValue($alphabet[$col+10].$row, '11');


		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+10].$row)->getAlignment()->setWrapText(true);
		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+10].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+10].$row)->getAlignment()->setVertical('center');

		$styleArray = [
		    'borders' => [
		        'allBorders' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		];

		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+10].$row)->applyFromArray($styleArray);

		//TABLE ISI
		$jmlhawal = 0;
		$totalawal = 0;
		$jmlhakhir = 0;
		$totalakhir = 0;
		if (count($cekrekap) == 0) {
		} else {
			foreach ($cekrekap as $key => $value) {
				$row++;
				$sheet->setCellValue($alphabet[$col].$row, $value['KOBAR']);
				$sheet->setCellValue($alphabet[$col+1].$row, $value['nabarref']);
				$sheet->setCellValue($alphabet[$col+2].$row, $value['SATUAN']);
				$sheet->setCellValue($alphabet[$col+3].$row, $value['KUANTITAS_SALDOAWAL']);
				$sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('###');
				$sheet->setCellValueExplicit($alphabet[$col+4].$row, $value['HARGA_SALDOAWAL'], DataType::TYPE_NUMERIC);
				$sheet->setCellValue($alphabet[$col+5].$row, '');
				$sheet->setCellValue($alphabet[$col+6].$row, '');
				$sheet->setCellValue($alphabet[$col+7].$row, '');
				$sheet->setCellValue($alphabet[$col+8].$row, '');
				$sheet->setCellValue($alphabet[$col+9].$row, $value['KUANTITAS_SALDOAKHIR']);
				$sheet->getStyle($alphabet[$col+10].$row)->getNumberFormat()->setFormatCode('###');
				$sheet->setCellValueExplicit($alphabet[$col+10].$row, $value['HARGA_SALDOAKHIR'], DataType::TYPE_NUMERIC);

				$jmlhawal += $value['KUANTITAS_SALDOAWAL'];
				$totalawal += $value['HARGA_SALDOAWAL'];

				$jmlhakhir += $value['KUANTITAS_SALDOAKHIR'];
				$totalakhir += $value['HARGA_SALDOAKHIR'];
			}
		}
		

		//TABLE TOTAL
		$row++;
		// $sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('###');
		$sheet->setCellValue($alphabet[$col].$row, 'Total');
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+2].$row);
		$sheet->getStyle($alphabet[$col].$row.':'.$alphabet[$col+2].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col].$row .':'. $alphabet[$col+2].$row)->getAlignment()->setVertical('center');

		$sheet->setCellValue($alphabet[$col+3].$row, $jmlhawal);
		$sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('###');
		$sheet->setCellValueExplicit($alphabet[$col+4].$row, $totalawal, DataType::TYPE_NUMERIC);

		$sheet->setCellValue($alphabet[$col+9].$row, $jmlhakhir);
		$sheet->getStyle($alphabet[$col+10].$row)->getNumberFormat()->setFormatCode('###');
		$sheet->setCellValueExplicit($alphabet[$col+10].$row, $totalakhir, DataType::TYPE_NUMERIC);

		$sheet->getStyle($alphabet[$col].($row-count($cekrekap)).':'.$alphabet[$col+10].$row)->applyFromArray($styleArray);

		// //TABLE FOOTER
		// $row+=2;
		// $sheet->setCellValue($alphabet[$col+8].$row, 'Jakarta, '. date('d M Y'));
		// $sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);

		// $row+=2;
		// $sheet->setCellValue($alphabet[$col+8].$row, 'KEPALA '.($upd == "NONE" ? strtoupper($pd) : strtoupper($upd)));
		// $sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);
		// $sheet->getRowDimension($row)->setRowHeight(30);

		// $row+=3;
		// $sheet->setCellValue($alphabet[$col+8].$row, '..............');
		// $sheet->mergeCells($alphabet[$col+8].($row-2).':'.$alphabet[$col+10].$row);

		// $row++;
		// $sheet->setCellValue($alphabet[$col+8].$row, $nowuser['nm_ka']);
		// $sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);

		// $row++;
		// $sheet->setCellValue($alphabet[$col+8].$row, 'NIP. '.$nowuser['nip_ka']);
		// $sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);

		// $sheet->getStyle($alphabet[$col+8].($row-7).':'.$alphabet[$col+10].$row)->getAlignment()->setWrapText(true);
		// $sheet->getStyle($alphabet[$col+8].($row-7).':'.$alphabet[$col+10].$row)->getAlignment()->setVertical('center');
		// $sheet->getStyle($alphabet[$col+8].($row-7).':'.$alphabet[$col+10].$row)->getAlignment()->setHorizontal('center');

		// $row+=3;

		// foreach(range('B','Z') as $columnID) {
		//     $sheet->getColumnDimension($columnID)
		//         ->setAutoSize(true);
		// }

		// $sheet->getStyle('F:F')
		//     ->getNumberFormat()
		//     ->setFormatCode('###,###,###,###,###');

		$arr = array($row, $col);

		return $arr;
	}

	public function excelfooter($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd, $laporannow)
	{
		

		//TABLE FOOTER
		$row+=2;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-3].$row, 'Jakarta, '. date('d M Y'));
		$sheet->mergeCells($alphabet[$laporannow['back_column']-3].$row.':'.$alphabet[$laporannow['back_column']-1].$row);

		$row+=2;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-3].$row, 'KEPALA '.($upd == "NONE" ? strtoupper($pd) : strtoupper($upd)));
		$sheet->mergeCells($alphabet[$laporannow['back_column']-3].$row.':'.$alphabet[$laporannow['back_column']-1].$row);
		$sheet->getRowDimension($row)->setRowHeight(30);

		$row+=5;
		if (isset($nowuser['ttd']) && $nowuser['ttd'] != '') {
			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			// $drawing->setPath('public/publicfile/ttd/AS005090000000002/ttdkaAS005090000000002.png');
			$drawing->setPath('public/publicfile/ttd/AS'.$nowuser['kolok'].'2/'.$nowuser['ttd']); // put your path and image here
			$drawing->setCoordinates(strtoupper($alphabet[$laporannow['back_column']-2]).($row-4));
			$drawing->setHeight(100);
			$drawing->setResizeProportional(true);
			$drawing->setWorksheet($sheet);
		} else {
			$sheet->setCellValue($alphabet[$laporannow['back_column']-3].($row-4), '..............');
		}
		$sheet->mergeCells($alphabet[$laporannow['back_column']-3].($row-4).':'.$alphabet[$laporannow['back_column']-1].$row);

		$row++;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-3].$row, $nowuser['nm_ka']);
		$sheet->mergeCells($alphabet[$laporannow['back_column']-3].$row.':'.$alphabet[$laporannow['back_column']-1].$row);

		$row++;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-3].$row, 'NIP. '.$nowuser['nip_ka']);
		$sheet->mergeCells($alphabet[$laporannow['back_column']-3].$row.':'.$alphabet[$laporannow['back_column']-1].$row);

		$sheet->getStyle($alphabet[$laporannow['back_column']-3].($row-7).':'.$alphabet[$laporannow['back_column']-1].$row)->getAlignment()->setWrapText(true);
		$sheet->getStyle($alphabet[$laporannow['back_column']-3].($row-7).':'.$alphabet[$laporannow['back_column']-1].$row)->getAlignment()->setVertical('center');
		$sheet->getStyle($alphabet[$laporannow['back_column']-3].($row-7).':'.$alphabet[$laporannow['back_column']-1].$row)->getAlignment()->setHorizontal('center');

		$row+=3;

		foreach(range('B','Z') as $columnID) {
		    $sheet->getColumnDimension($columnID)
		        ->setAutoSize(true);
		}

		$arr = array($row, $col);

		return $arr;

	}
}
