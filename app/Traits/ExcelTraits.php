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
		$sheet->getStyle($alphabet[$col].$row)->getFont()->setBold( true );

		// $row++;
		// $sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		// $sheet->setCellValue($alphabet[$col].$row, "LAPORAN ".strtoupper($laporan['jns_laporan']));

		$row++;
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$col].$row, "PER SUB-SUB RINCIAN OBJEK BARANG");
		$sheet->getStyle($alphabet[$col].$row)->getFont()->setBold( true );

		$row++;
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$col].$row, "TAHUN ANGGARAN : ".$year);
		$sheet->getStyle($alphabet[$col].$row)->getFont()->setBold( true );

		$styleArray = [
			'font'  => [
				'name'  => 'Arial'
			],
		];
		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$laporan['back_column']-1].$row)->applyFromArray($styleArray);

		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$laporan['back_column']-1].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col].($row-3) . ':' . $alphabet[$laporan['back_column']-1].$row)->getAlignment()->setVertical('center');

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelpd($sheet, $row, $col, $alphabet, $pd, $upd, $kolokpd, $kolokupd)
	{
		$row+=2;
		$sheet->setCellValue($alphabet[$col].$row, 'SKPD/UKPD');
		$sheet->setCellValue($alphabet[$col+1].$row, ': ' . $kolokpd . ' - ' . ($upd == "NONE" ? strtoupper($pd) : strtoupper($upd)) );
		// $sheet->setCellValue($alphabet[$col+2].$row, strtoupper($pd));

		// if ($upd != "NONE") {
		// 	$row++;
		// 	$sheet->setCellValue($alphabet[$col].$row, 'UPD');
		// 	$sheet->setCellValue($alphabet[$col+1].$row, ': '.$kolokupd);
		// 	$sheet->setCellValue($alphabet[$col+2].$row, strtoupper($upd));			
		// }

		$arr = array($row, $col);

		return $arr;
	}

	protected function exceltipelaporan($sheet, $row, $col, $alphabet, $laporan)
	{
		// $sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$col + $laporan['back_column'] - 2].$row, "TIPE LAPORAN");
		$sheet->setCellValue($alphabet[$col + $laporan['back_column'] - 2].($row+1), strtoupper($laporan['jns_laporan']));

		$sheet->getStyle($alphabet[$col + $laporan['back_column'] - 2].$row.':'.$alphabet[$col + $laporan['back_column'] - 2].($row+1))->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col + $laporan['back_column'] - 2].$row .':'. $alphabet[$col + $laporan['back_column'] - 2].($row+1))->getAlignment()->setVertical('center');

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
			'font'  => [
				'bold'  => true,
				'name'  => 'Arial'
			],
		];
		$sheet->getStyle($alphabet[$col + $laporan['back_column'] - 2].$row . ':' . $alphabet[$col + $laporan['back_column'] - 2].($row+1))->applyFromArray($styleArray);

		$styleArray2 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => '808080',
				],
			],
			'font'  => [
				'color' => array('rgb' => 'FFFFFF'),
			],
		];
		$sheet->getStyle($alphabet[$col + $laporan['back_column'] - 2].$row)->applyFromArray($styleArray2);

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelkib($sheet, $row, $col, $alphabet, $kib)
	{
		$row++;
		$sheet->setCellValue($alphabet[$col].$row, 'KIB');
		$sheet->setCellValue($alphabet[$col+1].$row, ': '.$kib);

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelK01($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd)
	{
		$row++;
		$sheet->setCellValue($alphabet[$col].$row, 'NO');
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col].($row+1));

		$sheet->setCellValue($alphabet[$col+1].$row, 'SUB-SUB RINCIAN OBJEK');
		$sheet->mergeCells($alphabet[$col+1].$row.':'.$alphabet[$col+2].$row);

		$sheet->setCellValue($alphabet[$col+3].$row, 'SALDO AWAL');
		$sheet->mergeCells($alphabet[$col+3].$row.':'.$alphabet[$col+4].$row);

		$sheet->setCellValue($alphabet[$col+5].$row, 'MUTASI BERTAMBAH');
		$sheet->mergeCells($alphabet[$col+5].$row.':'.$alphabet[$col+6].$row);

		$sheet->setCellValue($alphabet[$col+7].$row, 'MUTASI BERKURANG');
		$sheet->mergeCells($alphabet[$col+7].$row.':'.$alphabet[$col+8].$row);

		$sheet->setCellValue($alphabet[$col+9].$row, 'SALDO AKHIR');
		$sheet->mergeCells($alphabet[$col+9].$row.':'.$alphabet[$col+10].$row);

		$row++;
		$sheet->setCellValue($alphabet[$col+1].$row, 'KOBAR');
		$sheet->setCellValue($alphabet[$col+2].$row, 'NAMA BARANG');
		$sheet->setCellValue($alphabet[$col+3].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+4].$row, 'NILAI');
		$sheet->setCellValue($alphabet[$col+5].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+6].$row, 'NILAI');
		$sheet->setCellValue($alphabet[$col+7].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+8].$row, 'NILAI');
		$sheet->setCellValue($alphabet[$col+9].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+10].$row, 'NILAI');

		$sheet->getStyle($alphabet[$col].($row-1) . ':' . $alphabet[$col+10].$row)->getFont()->setBold( true );

		$colorArray = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => '92D050',
				],
			],
			'font'  => [
				'name'  => 'Arial'
			],
		];
		$sheet->getStyle($alphabet[$col].($row-1) . ':' . $alphabet[$col+10].$row)->applyFromArray($colorArray);

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
		$sheet->getRowDimension($row)->setRowHeight(10);
		$sheet->getStyle($alphabet[$col].$row .':'. $alphabet[$col+10].$row)->getFont()->setSize(9);

		$colorArray2 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'FABF8F',
				],
			],
		];
		$sheet->getStyle($alphabet[$col].$row . ':' . $alphabet[$col+10].$row)->applyFromArray($colorArray2);

		$sheet->getStyle($alphabet[$col].($row-2) . ':' . $alphabet[$col+10].$row)->getAlignment()->setWrapText(true);
		$sheet->getStyle($alphabet[$col].($row-2) . ':' . $alphabet[$col+10].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col].($row-2) . ':' . $alphabet[$col+10].$row)->getAlignment()->setVertical('center');

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'bottom' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
				],
			],

		];

		$sheet->getStyle($alphabet[$col].($row-2) . ':' . $alphabet[$col+10].$row)->applyFromArray($styleArray);

		//TABLE ISI
		$jmlhawal = 0;
		$totalawal = 0;
		$jmlhakhir = 0;
		$totalakhir = 0;
		if (count($cekrekap) == 0) {
		} else {
			foreach ($cekrekap as $key => $value) {
				$row++;
				$sheet->setCellValue($alphabet[$col].$row, ($key+1));
				$sheet->getStyle($alphabet[$col].$row)->getAlignment()->setVertical('center');
				$sheet->getStyle($alphabet[$col].$row)->getAlignment()->setHorizontal('center');

				$sheet->setCellValue($alphabet[$col+1].$row, $value['KOBAR']);

				$sheet->setCellValue($alphabet[$col+2].$row, $value['nabarref']);
				// $sheet->setCellValue($alphabet[$col+2].$row, $value['SATUAN']);

				$sheet->setCellValue($alphabet[$col+3].$row, is_null($value['KUANTITAS_SALDOAWAL']) ? 0 : $value['KUANTITAS_SALDOAWAL']);
				$sheet->getStyle($alphabet[$col+3].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+4].$row, is_null($value['HARGA_SALDOAWAL']) ? 0 : $value['HARGA_SALDOAWAL'] );
				$sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+5].$row, is_null($value['TAMBAH_QTY']) ? 0 : $value['TAMBAH_QTY'] );
				$sheet->getStyle($alphabet[$col+5].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+6].$row, is_null($value['TAMBAH_HARGA']) ? 0 : $value['TAMBAH_HARGA'] );
				$sheet->getStyle($alphabet[$col+6].$row)->getNumberFormat()->setFormatCode('#,##0');
				
				$sheet->setCellValue($alphabet[$col+7].$row, is_null($value['KURANG_QTY']) ? 0 : $value['KURANG_QTY'] );
				$sheet->getStyle($alphabet[$col+7].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+8].$row, is_null($value['KURANG_HARGA']) ? 0 : $value['KURANG_HARGA'] );
				$sheet->getStyle($alphabet[$col+8].$row)->getNumberFormat()->setFormatCode('#,##0');
				
				$sheet->setCellValue($alphabet[$col+9].$row, is_null($value['KUANTITAS_SALDOAKHIR']) ? 0 : $value['KUANTITAS_SALDOAKHIR']);
				$sheet->getStyle($alphabet[$col+9].$row)->getNumberFormat()->setFormatCode('#,##0');
				
				$sheet->setCellValue($alphabet[$col+10].$row, is_null($value['HARGA_SALDOAKHIR']) ? 0 : $value['HARGA_SALDOAKHIR']);
				$sheet->getStyle($alphabet[$col+10].$row)->getNumberFormat()->setFormatCode('#,##0');

				// $jmlhawal += $value['KUANTITAS_SALDOAWAL'];
				// $totalawal += $value['HARGA_SALDOAWAL'];

				// $jmlhakhir += $value['KUANTITAS_SALDOAKHIR'];
				// $totalakhir += $value['HARGA_SALDOAKHIR'];
			}
		}
		

		//TABLE TOTAL
		$row++;
		// $sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('#,##0');
		$sheet->setCellValue($alphabet[$col].$row, 'JUMLAH');
		$sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$col+2].$row);
		$sheet->getStyle($alphabet[$col].$row.':'.$alphabet[$col+2].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col].$row .':'. $alphabet[$col+2].$row)->getAlignment()->setVertical('center');

		//sum buat kolom saldoawal & akhir
		$jmlqtyawal = strtoupper($alphabet[$col+3]).($row-1).':'.strtoupper($alphabet[$col+3]).($row-count($cekrekap));
		$jmlnilawal = strtoupper($alphabet[$col+4]).($row-1).':'.strtoupper($alphabet[$col+4]).($row-count($cekrekap));
		$jmlqtyakhir = strtoupper($alphabet[$col+9]).($row-1).':'.strtoupper($alphabet[$col+9]).($row-count($cekrekap));
		$jmlnilakhir = strtoupper($alphabet[$col+10]).($row-1).':'.strtoupper($alphabet[$col+10]).($row-count($cekrekap));

		$sheet->setCellValue($alphabet[$col+3].$row, '=SUM('.$jmlqtyawal.')');
		$sheet->getStyle($alphabet[$col+3].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue($alphabet[$col+4].$row, '=SUM('.$jmlnilawal.')');
		$sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue($alphabet[$col+9].$row, '=SUM('.$jmlqtyakhir.')');
		$sheet->getStyle($alphabet[$col+9].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue($alphabet[$col+10].$row, '=SUM('.$jmlnilakhir.')');
		$sheet->getStyle($alphabet[$col+10].$row)->getNumberFormat()->setFormatCode('#,##0');

		//sum buat mutasi tambah & kurang
		$jmlqtytambah = strtoupper($alphabet[$col+5]).($row-1).':'.strtoupper($alphabet[$col+5]).($row-count($cekrekap));
		$jmlhargatambah = strtoupper($alphabet[$col+6]).($row-1).':'.strtoupper($alphabet[$col+6]).($row-count($cekrekap));
		$jmlqtykurang = strtoupper($alphabet[$col+7]).($row-1).':'.strtoupper($alphabet[$col+7]).($row-count($cekrekap));
		$jmlhargakurang = strtoupper($alphabet[$col+8]).($row-1).':'.strtoupper($alphabet[$col+8]).($row-count($cekrekap));

		$sheet->setCellValue($alphabet[$col+5].$row, '=SUM('.$jmlqtytambah.')');
		$sheet->getStyle($alphabet[$col+5].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue($alphabet[$col+6].$row, '=SUM('.$jmlhargatambah.')');
		$sheet->getStyle($alphabet[$col+6].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue($alphabet[$col+7].$row, '=SUM('.$jmlqtykurang.')');
		$sheet->getStyle($alphabet[$col+7].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue($alphabet[$col+8].$row, '=SUM('.$jmlhargakurang.')');
		$sheet->getStyle($alphabet[$col+8].$row)->getNumberFormat()->setFormatCode('#,##0');



		$sheet->getStyle($alphabet[$col].($row-count($cekrekap)).':'.$alphabet[$col+10].$row)->applyFromArray($styleArray);

	    $row++;

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelfooter($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd, $laporannow)
	{
		//TABLE FOOTER
		$row++;
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

		$sheet->getStyle($alphabet[$laporannow['back_column']-3].($row-9).':'.$alphabet[$laporannow['back_column']-1].$row)->getAlignment()->setWrapText(true);
		$sheet->getStyle($alphabet[$laporannow['back_column']-3].($row-9).':'.$alphabet[$laporannow['back_column']-1].$row)->getAlignment()->setVertical('center');
		$sheet->getStyle($alphabet[$laporannow['back_column']-3].($row-9).':'.$alphabet[$laporannow['back_column']-1].$row)->getAlignment()->setHorizontal('center');

		$row+=3;

		$sheet->getColumnDimension('A')->setWidth(2);

		foreach(range('B','Z') as $columnID) {
			$sheet->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		$sheet->getColumnDimension('C')->setAutoSize(false);
		$sheet->getColumnDimension('C')->setWidth(14);

		$sheet->getColumnDimension('E')->setAutoSize(false);
		$sheet->getColumnDimension('E')->setWidth(7);
		$sheet->getColumnDimension('G')->setAutoSize(false);
		$sheet->getColumnDimension('G')->setWidth(7);
		$sheet->getColumnDimension('I')->setAutoSize(false);
		$sheet->getColumnDimension('I')->setWidth(7);
		$sheet->getColumnDimension('K')->setAutoSize(false);
		$sheet->getColumnDimension('K')->setWidth(7);

		$sheet->setShowGridlines(false);

		$arr = array($row, $col);

		return $arr;

	}
}
