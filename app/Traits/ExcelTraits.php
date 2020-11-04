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
		//
		$sheet->getRowDimension($row)->setRowHeight(7);

		//kiri kanan kecilin
		$sheet->getColumnDimension('A')->setWidth(2.7);
		$sheet->getColumnDimension('B')->setWidth(1.72);
		$sheet->getColumnDimension($alphabet[$laporan['back_column']+1])->setWidth(1.72);

		//HEADER DAN JUDUL
		$row++;
		$sheet->mergeCells($alphabet[$col+1].$row.':'.$alphabet[$laporan['back_column']].$row);
		$sheet->setCellValue($alphabet[$col+1].$row, "LAPORAN BARANG KUASA PENGGUNA");
		$sheet->getStyle($alphabet[$col+1].$row)->getFont()->setBold( true );

		$row++;
		$sheet->mergeCells($alphabet[$col+1].$row.':'.$alphabet[$laporan['back_column']].$row);
		$sheet->setCellValue($alphabet[$col+1].$row, "PER SUB-SUB RINCIAN OBJEK BARANG");
		$sheet->getStyle($alphabet[$col+1].$row)->getFont()->setBold( true );

		$row++;
		$sheet->mergeCells($alphabet[$col+1].$row.':'.$alphabet[$laporan['back_column']].$row);
		$sheet->setCellValue($alphabet[$col+1].$row, "TAHUN ANGGARAN : ".$year);
		$sheet->getStyle($alphabet[$col+1].$row)->getFont()->setBold( true );

		$sheet->getStyle($alphabet[$col+1].($row-3) . ':' . $alphabet[$laporan['back_column']].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col+1].($row-3) . ':' . $alphabet[$laporan['back_column']].$row)->getAlignment()->setVertical('center');

		$row++;
		$styleArray = [
			'borders' => [
				'bottom' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
					'color' => array('rgb' => '808080'),
				],
			],

		];
		$sheet->getStyle($alphabet[$col+1].$row.':'.$alphabet[$laporan['back_column']].$row)->applyFromArray($styleArray);

		$fontArray = [
			'font'  => [
				'name'  => 'Arial Narrow',
				'size'	=> 10,
			],
		];
		$sheet->getStyle($alphabet[$col+1].$row.':'.$alphabet[$laporan['back_column']].$row)->applyFromArray($fontArray);

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelpd($sheet, $row, $col, $alphabet, $pd, $upd, $kolokpd, $kolokupd)
	{
		$row++;
		$sheet->getRowDimension($row)->setRowHeight(5.5);

		$row++;
		$sheet->setCellValue($alphabet[$col+1].$row, 'SKPD/UKPD');
		$sheet->setCellValue($alphabet[$col+3].$row, ': ' . $kolokpd . ' - ' . ($upd == "NONE" ? strtoupper($pd) : strtoupper($upd)) );
		$sheet->mergeCells($alphabet[$col+3].$row.':'.$alphabet[$col+6].$row);

		$fontArray = [
			'font'  => [
				'name'  => 'Arial Narrow',
				'size'	=> 10,
			],
		];
		$sheet->getStyle($alphabet[$col+1].$row.':'.$alphabet[$col+6].$row)->applyFromArray($fontArray);

		$arr = array($row, $col);

		return $arr;
	}

	protected function exceltipelaporan($sheet, $row, $col, $alphabet, $laporan)
	{
		// $sheet->mergeCells($alphabet[$col].$row.':'.$alphabet[$laporan['back_column']-1].$row);
		$sheet->setCellValue($alphabet[$laporan['back_column']-1].$row, "TIPE LAPORAN");
		$sheet->mergeCells($alphabet[$laporan['back_column']-1].$row.':'.$alphabet[$laporan['back_column']].$row);
		$sheet->setCellValue($alphabet[$laporan['back_column']-1].($row+1), strtoupper($laporan['jns_laporan']));
		$sheet->mergeCells($alphabet[$laporan['back_column']-1].($row+1).':'.$alphabet[$laporan['back_column']].($row+1));

		$sheet->getStyle($alphabet[$laporan['back_column'] - 1].$row.':'.$alphabet[$laporan['back_column']].($row+1))->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$laporan['back_column'] - 1].$row .':'. $alphabet[$laporan['back_column']].($row+1))->getAlignment()->setVertical('center');

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
			'font'  => [
				'bold'  => true,
				'name'  => 'Arial Narrow',
				'size'	=> 10,
			],
		];
		$sheet->getStyle($alphabet[$laporan['back_column'] - 1].$row . ':' . $alphabet[$laporan['back_column']].($row+1))->applyFromArray($styleArray);

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
		$sheet->getStyle($alphabet[$laporan['back_column'] - 1].$row)->applyFromArray($styleArray2);

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelkib($sheet, $row, $col, $alphabet, $kib, $namakib)
	{
		$row++;
		$sheet->setCellValue($alphabet[$col+1].$row, 'KIB');
		$sheet->setCellValue($alphabet[$col+3].$row, ': '.$kib.' ('.$namakib.')');
		$sheet->mergeCells($alphabet[$col+3].$row.':'.$alphabet[$col+6].$row);

		$fontArray = [
			'font'  => [
				'name'  => 'Arial Narrow',
				'size'	=> 10,
			],
		];
		$sheet->getStyle($alphabet[$col+1].$row.':'.$alphabet[$col+6].$row)->applyFromArray($fontArray);

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelK01($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd)
	{
		//SET HEADER TABEL
		$row+=2;
		$sheet->setCellValue($alphabet[$col+1].$row, 'NO');
		$sheet->mergeCells($alphabet[$col+1].$row.':'.$alphabet[$col+1].($row+1));

		$sheet->setCellValue($alphabet[$col+2].$row, 'SUB-SUB RINCIAN OBJEK');
		$sheet->mergeCells($alphabet[$col+2].$row.':'.$alphabet[$col+4].$row);

		$sheet->setCellValue($alphabet[$col+5].$row, 'SALDO AWAL');
		$sheet->mergeCells($alphabet[$col+5].$row.':'.$alphabet[$col+6].$row);

		$sheet->setCellValue($alphabet[$col+7].$row, 'MUTASI BERTAMBAH');
		$sheet->mergeCells($alphabet[$col+7].$row.':'.$alphabet[$col+8].$row);

		$sheet->setCellValue($alphabet[$col+9].$row, 'MUTASI BERKURANG');
		$sheet->mergeCells($alphabet[$col+9].$row.':'.$alphabet[$col+10].$row);

		$sheet->setCellValue($alphabet[$col+11].$row, 'SALDO AKHIR');
		$sheet->mergeCells($alphabet[$col+11].$row.':'.$alphabet[$col+12].$row);

		$row++;
		$sheet->setCellValue($alphabet[$col+2].$row, 'KOBAR');
		$sheet->mergeCells($alphabet[$col+2].$row.':'.$alphabet[$col+3].$row);
		$sheet->setCellValue($alphabet[$col+4].$row, 'NAMA BARANG');
		$sheet->setCellValue($alphabet[$col+5].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+6].$row, 'NILAI');
		$sheet->setCellValue($alphabet[$col+7].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+8].$row, 'NILAI');
		$sheet->setCellValue($alphabet[$col+9].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+10].$row, 'NILAI');
		$sheet->setCellValue($alphabet[$col+11].$row, 'QTY');
		$sheet->setCellValue($alphabet[$col+12].$row, 'NILAI');

		$sheet->getStyle($alphabet[$col+1].($row-1) . ':' . $alphabet[$col+12].$row)->getFont()->setBold( true );

		$colorArray = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'DCE6F1',
				],
			],
		];
		$sheet->getStyle($alphabet[$col+1].($row-1) . ':' . $alphabet[$col+12].$row)->applyFromArray($colorArray);

		$fontArray = [
			'font'  => [
				'name'  => 'Arial Narrow',
				'size'	=> 10,
			],
		];
		$sheet->getStyle($alphabet[$col+1].($row-1).':'.$alphabet[$col+12].$row)->applyFromArray($fontArray);


		//SET ANGKA ANTARA HEADER DAN BODY TABEL
		$row++;
		$sheet->setCellValue($alphabet[$col+1].$row, '1');
		$sheet->setCellValue($alphabet[$col+2].$row, '2');
		$sheet->mergeCells($alphabet[$col+2].$row.':'.$alphabet[$col+3].$row);
		$sheet->setCellValue($alphabet[$col+4].$row, '3');
		$sheet->setCellValue($alphabet[$col+5].$row, '4');
		$sheet->setCellValue($alphabet[$col+6].$row, '5');
		$sheet->setCellValue($alphabet[$col+7].$row, '6');
		$sheet->setCellValue($alphabet[$col+8].$row, '7');
		$sheet->setCellValue($alphabet[$col+9].$row, '8');
		$sheet->setCellValue($alphabet[$col+10].$row, '9');
		$sheet->setCellValue($alphabet[$col+11].$row, '10');
		$sheet->setCellValue($alphabet[$col+12].$row, '11');
		$sheet->getRowDimension($row)->setRowHeight(10.5);

		$colorArray2 = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'BFBFBF',
				],
			],
		];
		$sheet->getStyle($alphabet[$col+1].$row . ':' . $alphabet[$col+12].$row)->applyFromArray($colorArray2);

		$fontArray = [
			'font'  => [
				'name'  => 'Arial Narrow',
				'size'	=> 8,
			],
		];
		$sheet->getStyle($alphabet[$col+1].$row.':'.$alphabet[$col+12].$row)->applyFromArray($fontArray);

		$sheet->getStyle($alphabet[$col+1].($row-2) . ':' . $alphabet[$col+12].$row)->getAlignment()->setWrapText(true);
		$sheet->getStyle($alphabet[$col+1].($row-2) . ':' . $alphabet[$col+12].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col+1].($row-2) . ':' . $alphabet[$col+12].$row)->getAlignment()->setVertical('center');

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => array('rgb' => 'A6A6A6'),
				],
				'bottom' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
					'color' => array('rgb' => 'A6A6A6'),
				],
			],

		];

		$sheet->getStyle($alphabet[$col+1].($row-2) . ':' . $alphabet[$col+12].$row)->applyFromArray($styleArray);

		//SET COLUMN WIDTHH
		$sheet->getColumnDimension('C')->setWidth(4.2);
		$sheet->getColumnDimension('D')->setWidth(6.6);
		$sheet->getColumnDimension('E')->setWidth(5.6);
		$sheet->getColumnDimension('F')->setWidth(42.2);
		$sheet->getColumnDimension('G')->setWidth(5.7);
		$sheet->getColumnDimension('H')->setWidth(15.44);
		$sheet->getColumnDimension('I')->setWidth(5.7);
		$sheet->getColumnDimension('J')->setWidth(15.44);
		$sheet->getColumnDimension('K')->setWidth(5.7);
		$sheet->getColumnDimension('L')->setWidth(15.44);
		$sheet->getColumnDimension('M')->setWidth(5.7);
		$sheet->getColumnDimension('N')->setWidth(15.44);

		//TABLE ISI
		$jmlhawal = 0;
		$totalawal = 0;
		$jmlhakhir = 0;
		$totalakhir = 0;
		if (count($cekrekap) == 0) {
		} else {
			foreach ($cekrekap as $key => $value) {
				$row++;
				$sheet->setCellValue($alphabet[$col+1].$row, ($key+1));
				$sheet->getStyle($alphabet[$col+1].$row)->getAlignment()->setVertical('center');
				$sheet->getStyle($alphabet[$col+1].$row)->getAlignment()->setHorizontal('center');

				$sheet->mergeCells($alphabet[$col+2].$row.':'.$alphabet[$col+3].$row);
				$sheet->setCellValue($alphabet[$col+2].$row, $value['KOBAR']);
				$sheet->getStyle($alphabet[$col+2].$row)->getAlignment()->setHorizontal('center');

				$sheet->setCellValue($alphabet[$col+4].$row, $value['nabarref']);
				// $sheet->setCellValue($alphabet[$col+2].$row, $value['SATUAN']);

				$sheet->setCellValue($alphabet[$col+5].$row, is_null($value['KUANTITAS_SALDOAWAL']) ? 0 : $value['KUANTITAS_SALDOAWAL']);
				$sheet->getStyle($alphabet[$col+5].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+6].$row, is_null($value['HARGA_SALDOAWAL']) ? 0 : $value['HARGA_SALDOAWAL'] );
				$sheet->getStyle($alphabet[$col+6].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+7].$row, is_null($value['TAMBAH_QTY']) ? 0 : $value['TAMBAH_QTY'] );
				$sheet->getStyle($alphabet[$col+7].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+8].$row, is_null($value['TAMBAH_HARGA']) ? 0 : $value['TAMBAH_HARGA'] );
				$sheet->getStyle($alphabet[$col+8].$row)->getNumberFormat()->setFormatCode('#,##0');
				
				$sheet->setCellValue($alphabet[$col+9].$row, is_null($value['KURANG_QTY']) ? 0 : $value['KURANG_QTY'] );
				$sheet->getStyle($alphabet[$col+9].$row)->getNumberFormat()->setFormatCode('#,##0');

				$sheet->setCellValue($alphabet[$col+10].$row, is_null($value['KURANG_HARGA']) ? 0 : $value['KURANG_HARGA'] );
				$sheet->getStyle($alphabet[$col+10].$row)->getNumberFormat()->setFormatCode('#,##0');
				
				$sheet->setCellValue($alphabet[$col+11].$row, is_null($value['KUANTITAS_SALDOAKHIR']) ? 0 : $value['KUANTITAS_SALDOAKHIR']);
				$sheet->getStyle($alphabet[$col+11].$row)->getNumberFormat()->setFormatCode('#,##0');
				
				$sheet->setCellValue($alphabet[$col+12].$row, is_null($value['HARGA_SALDOAKHIR']) ? 0 : $value['HARGA_SALDOAKHIR']);
				$sheet->getStyle($alphabet[$col+12].$row)->getNumberFormat()->setFormatCode('#,##0');

				// $jmlhawal += $value['KUANTITAS_SALDOAWAL'];
				// $totalawal += $value['HARGA_SALDOAWAL'];

				// $jmlhakhir += $value['KUANTITAS_SALDOAKHIR'];
				// $totalakhir += $value['HARGA_SALDOAKHIR'];
			}
		}
		

		//TABLE TOTAL
		$row++;
		// $sheet->getStyle($alphabet[$col+4].$row)->getNumberFormat()->setFormatCode('#,##0');
		$sheet->setCellValue($alphabet[$col+1].$row, 'JUMLAH');
		$sheet->getStyle($alphabet[$col+1].$row)->getFont()->setBold( true );
		$sheet->mergeCells($alphabet[$col+1].$row.':'.$alphabet[$col+4].$row);
		$sheet->getStyle($alphabet[$col+1].$row.':'.$alphabet[$col+4].$row)->getAlignment()->setHorizontal('center');
		$sheet->getStyle($alphabet[$col+1].$row .':'. $alphabet[$col+4].$row)->getAlignment()->setVertical('center');

		//sum buat kolom saldoawal & akhir
		$jmlqtyawal = strtoupper($alphabet[$col+5]).($row-1).':'.strtoupper($alphabet[$col+5]).($row-count($cekrekap));
		$jmlnilawal = strtoupper($alphabet[$col+6]).($row-1).':'.strtoupper($alphabet[$col+6]).($row-count($cekrekap));
		$jmlqtyakhir = strtoupper($alphabet[$col+11]).($row-1).':'.strtoupper($alphabet[$col+11]).($row-count($cekrekap));
		$jmlnilakhir = strtoupper($alphabet[$col+12]).($row-1).':'.strtoupper($alphabet[$col+12]).($row-count($cekrekap));

		$sheet->setCellValue( $alphabet[$col+5].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlqtyawal.')');
		$sheet->getStyle($alphabet[$col+5].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue( $alphabet[$col+6].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlnilawal.')');
		$sheet->getStyle($alphabet[$col+6].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue( $alphabet[$col+11].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlqtyakhir.')');
		$sheet->getStyle($alphabet[$col+11].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue( $alphabet[$col+12].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlnilakhir.')');
		$sheet->getStyle($alphabet[$col+12].$row)->getNumberFormat()->setFormatCode('#,##0');

		//sum buat mutasi tambah & kurang
		$jmlqtytambah = strtoupper($alphabet[$col+7]).($row-1).':'.strtoupper($alphabet[$col+7]).($row-count($cekrekap));
		$jmlhargatambah = strtoupper($alphabet[$col+8]).($row-1).':'.strtoupper($alphabet[$col+8]).($row-count($cekrekap));
		$jmlqtykurang = strtoupper($alphabet[$col+9]).($row-1).':'.strtoupper($alphabet[$col+9]).($row-count($cekrekap));
		$jmlhargakurang = strtoupper($alphabet[$col+10]).($row-1).':'.strtoupper($alphabet[$col+10]).($row-count($cekrekap));

		$sheet->setCellValue( $alphabet[$col+7].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlqtytambah.')');
		$sheet->getStyle($alphabet[$col+7].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue( $alphabet[$col+8].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlhargatambah.')');
		$sheet->getStyle($alphabet[$col+8].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue( $alphabet[$col+9].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlqtykurang.')');
		$sheet->getStyle($alphabet[$col+9].$row)->getNumberFormat()->setFormatCode('#,##0');

		$sheet->setCellValue( $alphabet[$col+10].$row, count($cekrekap)==0 ? '0' : '=SUM('.$jmlhargakurang.')');
		$sheet->getStyle($alphabet[$col+10].$row)->getNumberFormat()->setFormatCode('#,##0');



		$sheet->getStyle($alphabet[$col+1].($row-count($cekrekap)).':'.$alphabet[$col+12].$row)->applyFromArray($styleArray);

		$styleArray = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
					'color' => array('rgb' => 'A6A6A6'),
				],
			],
		];
		$sheet->getStyle($alphabet[$col+1].$row.':'.$alphabet[$col+12].$row)->applyFromArray($styleArray);

		$fontArray = [
			'font'  => [
				'name'  => 'Arial Narrow',
				'size'	=> 10,
			],
		];
		$sheet->getStyle($alphabet[$col+1].($row-count($cekrekap)).':'.$alphabet[$col+12].$row)->applyFromArray($fontArray);

		$row++;

		$arr = array($row, $col);

		return $arr;
	}

	protected function excelfooter($sheet, $row, $col, $alphabet, $year, $cekrekap, $nowuser, $pd, $upd, $laporannow, $kib, $kolok)
	{
		//TABLE FOOTER

		//TANGGAL
		$row++;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-2].$row, 'Jakarta, '. date('d M Y'));
		$sheet->mergeCells($alphabet[$laporannow['back_column']-2].$row.':'.$alphabet[$laporannow['back_column']].$row);

		//JABATAN
		$row+=2;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-2].$row, 'KEPALA '.($upd == "NONE" ? strtoupper($pd) : strtoupper($upd)));
		$sheet->mergeCells($alphabet[$laporannow['back_column']-2].$row.':'.$alphabet[$laporannow['back_column']].$row);
		$sheet->getRowDimension($row)->setRowHeight(30);

		//TANDATANGAN
		$row+=5;
		if (isset($nowuser['ttd']) && $nowuser['ttd'] != '') {
			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			// $drawing->setPath('public/publicfile/ttd/AS005090000000002/ttdkaAS005090000000002.png');
			$drawing->setPath('public/publicfile/ttd/AS'.$nowuser['kolok'].'2/'.$nowuser['ttd']); // put your path and image here
			$drawing->setCoordinates(strtoupper($alphabet[$laporannow['back_column']-2]).($row-4));
			$drawing->setOffsetX(40);
			$drawing->setHeight(100);
			$drawing->setResizeProportional(true);
			$drawing->setWorksheet($sheet);
		} else {
			//teks kalo gambar ttd kosong belum di upload
			$sheet->setCellValue($alphabet[$laporannow['back_column']-2].($row-4), '');
		}
		$sheet->mergeCells($alphabet[$laporannow['back_column']-2].($row-4).':'.$alphabet[$laporannow['back_column']].$row);

		//NAMA
		$row++;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-2].$row, $nowuser['nm_ka']);
		$sheet->mergeCells($alphabet[$laporannow['back_column']-2].$row.':'.$alphabet[$laporannow['back_column']].$row);
		$sheet->getStyle($alphabet[$laporannow['back_column']-2].$row)->getFont()->setBold( true );

		//NIP
		$row++;
		$sheet->setCellValue($alphabet[$laporannow['back_column']-2].$row, 'NIP. '.$nowuser['nip_ka']);
		$sheet->mergeCells($alphabet[$laporannow['back_column']-2].$row.':'.$alphabet[$laporannow['back_column']].$row);

		$sheet->getStyle($alphabet[$laporannow['back_column']-2].($row-9).':'.$alphabet[$laporannow['back_column']].$row)->getAlignment()->setWrapText(true);
		$sheet->getStyle($alphabet[$laporannow['back_column']-2].($row-9).':'.$alphabet[$laporannow['back_column']].$row)->getAlignment()->setVertical('center');
		$sheet->getStyle($alphabet[$laporannow['back_column']-2].($row-9).':'.$alphabet[$laporannow['back_column']].$row)->getAlignment()->setHorizontal('center');


		//SET FOOTER TABEL FONT ARIAL NARROW
		$styleArray = [
			'font'  => [
				'name'  => 'Arial Narrow',
				'size'	=> 10,
			],
		];
		$sheet->getStyle($alphabet[$laporannow['back_column']-2].($row-9).':'.$alphabet[$laporannow['back_column']].$row)->applyFromArray($styleArray);
		// $sheet->getStyle('A13:Z13')->getFont()->setSize(8);

		//OFF GRIDLINE
		$sheet->setShowGridlines(false);


		//OUTER BORDER
		$outerBorderArray = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => array('rgb' => 'A6A6A6'),
				],
				'left' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => array('rgb' => 'A6A6A6'),
				],
				'right' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => array('rgb' => 'A6A6A6'),
				],
				'bottom' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => array('rgb' => 'A6A6A6'),
				],
			],
		];
		$sheet->getStyle('B2:'.$alphabet[$laporannow['back_column']+1].$row)->applyFromArray($outerBorderArray);
		$sheet->getStyle('B2:'.$alphabet[$laporannow['back_column']+1].$row)->getAlignment()->setVertical('center');


		//TAMBAH IMG LOGO DKI
		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing->setPath($_SERVER["DOCUMENT_ROOT"].'/laporanbmd/public/img/excel/excel-logo-dki2.png');
		$drawing->setCoordinates(strtoupper($alphabet[$col+2]).'3');
		// $drawing->setHeight(100);
		// $drawing->setResizeProportional(true);
		$drawing->setWorksheet($sheet);

		//TAMBAH IMG LOGO BPAD
		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing->setPath($_SERVER["DOCUMENT_ROOT"].'/laporanbmd/public/img/excel/excel-logo-bpad2.png');
		$drawing->setCoordinates(strtoupper($alphabet[$laporannow['back_column']]).'3');
		$drawing->setHeight(75);
		$drawing->setResizeProportional(true);
		$drawing->setWorksheet($sheet);

		//SET ORIENTATION LANDSCAPE
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

		//RENAME SHEET
		$sheet->setTitle("KIB".$kib.'_'.$kolok);

		$arr = array($row, $col);

		return $arr;

	}
}
