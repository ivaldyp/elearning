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
use App\Traits\SessionCheckTraits;

use App\Aset_quserid;
use App\Glo_profile_skpd;
use App\Dat_laporan;
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

		if ($request->yearnow) {
			$year = $request->yearnow;
		} else {
			$year = 2020;
		}

		if ($request->koloknow) {
			$kolok = $request->koloknow;
		} else {
			if ($_SESSION['idgroup'] == 'SKPD') {
				$kolok = $_SESSION['kolok'];
			} else {
				$kolok = '';
			}
		}

		$thisprofile = Glo_profile_skpd::
						where('kolok', $_SESSION['kolok'])
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
			$pds->where('kd_wil', $wil);
		}

		$pds->orderBy('kolok');

		$years = Glo_profile_skpd::
					distinct()
					->orderBy('tahun', 'desc')
					->get(['tahun']);

		$laporans = Dat_laporan::
					where('sts', 1)
					->orderBy('jns_laporan')
					->get();

		return view('pages.bpadlaporan.rekap')
				->with('koloknow', $kolok)
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
		$year = $request->tahun;
		$splitdurasi = explode("::", $request->durasi);

		$nowuser = Glo_profile_skpd::
					where('kolok', $kolok)
					->where('tahun', $year)
					->first();

		$nowuserskpd = Glo_profile_skpd::
					where('kolok', $nowuser['kolokskpd'])
					->where('tahun', $year)
					->first();

		if ($nowuser['kolokskpd'] == $nowuser['kolok']) {
			$pd = ucwords(strtolower($nowuser['nalok']));
			$upd = 'NONE';
		} else {
			$pd = ucwords(strtolower($nowuserskpd['nalok']));
			$upd = ucwords(strtolower($nowuser['nalok']));
		}

		var_dump($request->all());
		// die();

		if (is_null($request->kib)) {
			return redirect('/laporan')
					->with('message', 'Pilihan KIB tidak boleh kosong')
					->with('msg_num', 2);
		}

		if (is_null($kolok) || $kolok == '') {
			return redirect('/laporan')
					->with('message', 'Pilihan kolok tidak boleh kosong')
					->with('msg_num', 2);
		}

		if (is_null($year) || $year == '') {
			return redirect('/laporan')
					->with('message', 'Pilihan tahun tidak boleh kosong')
					->with('msg_num', 2);
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$colstart = 1;
		$rowstart = 2;

		if ($request->kib[0] == 'semua') {
			$logsusun = Log_susun_laporan::
					where('sts', 1)
					->where('kolok', $kolok)
					->where('tahun', $year);
		} else {
			foreach ($request->kib as $key => $kib) {
				$nmtabelawal = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_" . strtoupper($splitdurasi[0]) . "]"; 
				$nmtabelawalrekap = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_REKAP_" . strtoupper($splitdurasi[0]) . "]"; 
				$nmtabelakhir = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_" . strtoupper($splitdurasi[1]) . "]"; 
				$nmtabelakhirrekap = "bpadlaporandata.dbo.[" . $year . "_" . $kib . "_REKAP_" . strtoupper($splitdurasi[1]) . "]"; 

				$cekrekap = DB::select( DB::raw("
							SELECT *
							FROM $nmtabelawalrekap
							WHERE sts = 1
							AND kolok = '$kolok'
							ORDER BY KOBAR
							"));
				$cekrekap = json_decode(json_encode($cekrekap), true);

				var_dump(count($cekrekap));

				if (count($cekrekap) == 0) {
					$query = DB::select( DB::raw("
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
					$query = json_decode(json_encode($query), true);

					$temp_nmtabelawalrekap = str_replace("[", "", $nmtabelawalrekap);
					$temp_nmtabelawalrekap = str_replace("]", "", $temp_nmtabelawalrekap);

					foreach ($query as $key => $data) {
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
						     'SATUAN' => $data['satuan'],
						     'KUANTITAS' => $data['kuantitas'],
						     'HARGA' => $data['total'],

						 	]
						);
					}

					$cekrekap = DB::select( DB::raw("
								SELECT *
								FROM $nmtabelawalrekap
								WHERE sts = 1
								AND kolok = '$kolok'
								ORDER BY KOBAR
								"));
					$cekrekap = json_decode(json_encode($cekrekap), true);
				} 

				$col = $colstart;
				$row = $rowstart;

				//HEADER DAN JUDUL
				$sheet->setCellValue($alphabet[$col].$row, 'PD');
				$sheet->setCellValue($alphabet[$col+1].$row, ': '.strtoupper($pd));
				
				$row += 2;
				$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+10].$row);
				$sheet->setCellValue($alphabet[$col].$row, "LAPORAN BARANG KUASA PENGGUNA");

				$row++;
				$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+10].$row);
				$sheet->setCellValue($alphabet[$col].$row, "LAPORAN ".strtoupper($request->laporan));

				$row++;
				$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+10].$row);
				$sheet->setCellValue($alphabet[$col].$row, "PER SUB-SUB RINCIAN OBJEK BARANG");

				$row++;
				$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+10].$row);
				$sheet->setCellValue($alphabet[$col].$row, "TAHUN ANGGARAN : ".$year);
				$sheet->getStyle($alphabet[$col].($row-4) . ':' . $alphabet[$col+10].$row)->getAlignment()->setHorizontal('center');
				$sheet->getStyle($alphabet[$col].($row-4) . ':' . $alphabet[$col+10].$row)->getAlignment()->setVertical('center');

				$row += 2;
				if ($upd != "NONE") {
					$sheet->setCellValue($alphabet[$col].$row, 'UPD');
					$sheet->setCellValue($alphabet[$col+1].$row, ': '.strtoupper($upd));

					$row++;
				}
				
				$sheet->setCellValue($alphabet[$col].$row, 'KIB');
				$sheet->setCellValue($alphabet[$col+1].$row, ': '.$kib);


				//TABLE HEAD
				$row += 2;
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
				$total = 0;
				foreach ($cekrekap as $key => $value) {
					$row++;
					$sheet->setCellValue($alphabet[$col].$row, $value['KOBAR']);
					$sheet->setCellValue($alphabet[$col+1].$row, $value['NABAR']);
					$sheet->setCellValue($alphabet[$col+2].$row, $value['SATUAN']);
					$sheet->setCellValue($alphabet[$col+3].$row, $value['KUANTITAS']);
					$sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('###');
					$sheet->setCellValueExplicit($alphabet[$col+4].$row, $value['HARGA'], DataType::TYPE_NUMERIC);
					$sheet->setCellValue($alphabet[$col+5].$row, '');
					$sheet->setCellValue($alphabet[$col+6].$row, '');
					$sheet->setCellValue($alphabet[$col+7].$row, '');
					$sheet->setCellValue($alphabet[$col+8].$row, '');
					$sheet->setCellValue($alphabet[$col+9].$row, '');
					$sheet->setCellValue($alphabet[$col+10].$row, '');

					$total += $value['HARGA'];
				}

				//TABLE TOTAL
				$row++;
				$sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('###');
				$sheet->setCellValueExplicit($alphabet[$col+4].$row, $value['HARGA'], DataType::TYPE_NUMERIC);
				$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+3].$row);
				$sheet->getStyle($alphabet[$col].$row.':'.$alphabet[$col+3].$row)->getAlignment()->setHorizontal('center');
				$sheet->getStyle($alphabet[$col].$row .':'. $alphabet[$col+3].$row)->getAlignment()->setVertical('center');

				$sheet->setCellValue($alphabet[$col+4].$row, $total);

				$sheet->getStyle($alphabet[$col].($row-count($cekrekap)).':'.$alphabet[$col+10].$row)->applyFromArray($styleArray);


				//TABLE FOOTER
				$row+=2;
				$sheet->setCellValue($alphabet[$col+8].$row, 'Jakarta, '. date('d M Y'));
				$sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);

				$row+=2;
				$sheet->setCellValue($alphabet[$col+8].$row, 'KEPALA '.($upd == "NONE" ? strtoupper($pd) : strtoupper($upd)));
				$sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);
				$sheet->getRowDimension($row)->setRowHeight(30);

				$row+=3;
				$sheet->setCellValue($alphabet[$col+8].$row, '..............');
				$sheet->mergeCells($alphabet[$col+8].($row-2).':'.$alphabet[$col+10].$row);

				$row++;
				$sheet->setCellValue($alphabet[$col+8].$row, $nowuser['nm_ka']);
				$sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);

				$row++;
				$sheet->setCellValue($alphabet[$col+8].$row, 'NIP. '.$nowuser['nip_ka']);
				$sheet->mergeCells($alphabet[$col+8].$row.':'.$alphabet[$col+10].$row);

				$sheet->getStyle($alphabet[$col+8].($row-7).':'.$alphabet[$col+10].$row)->getAlignment()->setWrapText(true);
				$sheet->getStyle($alphabet[$col+8].($row-7).':'.$alphabet[$col+10].$row)->getAlignment()->setHorizontal('center');
				$sheet->getStyle($alphabet[$col+8].($row-7).':'.$alphabet[$col+10].$row)->getAlignment()->setVertical('center');
			}
			$filename = $year.'_'.$kolok.'_LAPORAN'.'.xlsx';
		}

		foreach(range('B','G') as $columnID) {
		    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
		        ->setAutoSize(true);
		}

		$sheet->getStyle('F:F')
		    ->getNumberFormat()
		    ->setFormatCode('###,###,###.##');

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
}
