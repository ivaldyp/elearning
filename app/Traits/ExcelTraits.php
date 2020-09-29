<?php

namespace App\Traits;

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

use App\Aset_quserid;
use App\Glo_profile_skpd;
use App\Dat_laporan;
use App\Dat_ttd;
use App\Log_susun_laporan;

trait ExcelTraits
{
	protected function excelhead($sheet, $row, $col, $alphabet, $year, $laporan)
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

	protected function excelpd($sheet, $row, $col, $alphabet, $pd, $upd, $kolokpd, $kolokupd)
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

	protected function excelkib($sheet, $row, $col, $alphabet, $kib)
	{
		$row+=2;
		$sheet->setCellValue($alphabet[$col].$row, 'KIB');
		$sheet->setCellValue($alphabet[$col+1].$row, ': '.$kib);

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelK01($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd)
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

		$sheet->setCellValue($alphabet[$col+11].$row, 'Keterangan');
		$sheet->mergeCells($alphabet[$col+11].$row.':'.$alphabet[$col+11].($row+2));

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
		$sheet->setCellValue($alphabet[$col+11].$row, '12');


		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+11].$row)->getAlignment()->setWrapText(true);
		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+11].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+11].$row)->getAlignment()->setVertical('center');

		$styleArray = [
		    'borders' => [
		        'allBorders' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		];

		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$col+11].$row)->applyFromArray($styleArray);

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
				$sheet->setCellValue($alphabet[$col+11].$row, $value['KETERANGAN']);

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

		$sheet->getStyle($alphabet[$col].($row-count($cekrekap)).':'.$alphabet[$col+11].$row)->applyFromArray($styleArray);

		$sheet->setCellValue($alphabet[$col+9].$row, '');

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

	protected function excelfooter($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd, $laporannow)
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
		$sheet->getColumnDimension('D')->setWidth('6');

		$arr = array($row, $col);

		return $arr;

	}
}
