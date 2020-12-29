<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/css/animate.css" rel="stylesheet">

	<style type="text/css">

		.logoHeader {
		    /*height: 120px;*/
		    /*vertical-align: middle;*/
		    top: 0px;
		    text-align: center; 
		    font-family: Arial, Helvetica, sans-serif;
		}

		.floatLeft { float: left; }

		.floatRight { float: right; }

		table {
			width: 100%
		}

		.tipetop, .tipebot {
			vertical-align: middle;
			align-content: center;
			text-align: center;
			border: 1px solid black;
		}

		.tipetop {
			color: white;
			background-color: #808080;
		}

		.tablelaporan {
			text-transform: uppercase;
			font-size: 12px;
		}

		/*.tablelaporan thead tr th {
			
		}*/

		.tablelaporan thead tr th{
			border: 1px solid #808080;
			vertical-align: middle;
			align-content: center;
			text-align: center;
		}

		.headclrblue{
			background-color: #DCE6F1;
		}
		.headclrgray{
			background-color: #BFBFBF;
		}

		.headclrgray th{
			line-height: 2px;
			font-size: 10px;
		}

	</style>
</head>
<body >
	<header>
		<img class="floatLeft" src="{{ $_SERVER['DOCUMENT_ROOT']}}/laporanbmd/storage/img/excel/excel-logo-dki2.png" height="100">
		<img class="floatRight" src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/storage/img/excel/excel-logo-bpad2.png" height="100">
		<h3 class="logoHeader"><b>

			LAPORAN BARANG KUASA PENGGUNA<br>
			PER SUB-SUB RINCIAN OBJEK BARANG<br>
			TAHUN ANGGARAN {{ $tahun }}											
		</b></h3>
	</header>
	<div class="row">
		<div class="row col-md-12" style="border-top: 2px solid gray;"> 
			<table style="padding-top: 5px" class="">
				<tbody>
					<tr >
						<td class="col-md-1">SKPD/UKPD</td>
						<td class="col-md-10" style="width: 100%">: {{$kolokpd}} - {{ $upd == "NONE" ? strtoupper($pd) : strtoupper($upd) }}</td>
						<td class="col-md-1 tipetop">TIPE LAPORAN</td>
					</tr>
					<tr>
						<td class="col-md-1">KIB</td>
						<td class="col-md-10" style="width: 100%">: {{$kib[0]}} ({{ $kib[1] }})</td>
						<td class="col-md-1 tipebot">{{ strtoupper($laporannow['jns_laporan']) }}</td>
					</tr>
				</tbody>
			</table>	
		</div>
	</div>
	<div class="row">
		<table style="padding-top: 15px" class="table tablelaporan table-bordered">
			<thead style="">
				<tr class="headclrblue">
					<th rowspan="2">no</th>
					<th colspan="3">Sub-sub rincian objek</th>
					<th colspan="2">Saldo awal</th>
					<th colspan="2">mutasi bertambah</th>
					<th colspan="2">mutasi berkurang</th>
					<th colspan="2">Saldo akhir</th>
				</tr>
				<tr class="headclrblue">
					<th colspan="2">kobar</th>
					<th>Nama Barang</th>
					<th>Qty</th>
					<th>Nilai</th>
					<th>Qty</th>
					<th>Nilai</th>
					<th>Qty</th>
					<th>Nilai</th>
					<th>Qty</th>
					<th>Nilai</th>
				</tr>
				<tr class="headclrgray" >
					<th >1</th>
					<th  colspan="2">2</th>
					<th >3</th>
					<th >4</th>
					<th >5</th>
					<th >6</th>
					<th >7</th>
					<th >8</th>
					<th >9</th>
					<th >10</th>
					<th >11</th>
				</tr>
			</thead>
			<tbody>
				@foreach($cekrekap as $key => $rekap)
				<tr>
					<td style="text-align: center;">{{ $key+1 }}</td>
					<td colspan="2" style="text-align: center;">{{ $rekap['KOBAR'] }}</td>
					<td>{{ $rekap['nabarref'] }}</td>
					<td style="text-align: right;">{{ number_format($rekap['KUANTITAS_SALDOAWAL']) }}</td>
					<td style="text-align: right;">{{ number_format($rekap['HARGA_SALDOAWAL']) }}</td>
					<td style="text-align: right;">{{ number_format($rekap['TAMBAH_QTY']) }}</td>
					<td style="text-align: right;">{{ number_format($rekap['TAMBAH_HARGA']) }}</td>
					<td style="text-align: right;">{{ number_format($rekap['KURANG_QTY']) }}</td>
					<td style="text-align: right;">{{ number_format($rekap['KURANG_HARGA']) }}</td>
					<td style="text-align: right;">{{ number_format($rekap['KUANTITAS_SALDOAKHIR']) }}</td>
					<td style="text-align: right;">{{ number_format($rekap['HARGA_SALDOAKHIR']) }}</td>
				</tr>
				@endforeach
				<?php
					$jmlqtyawal = array_sum(array_column($cekrekap, 'KUANTITAS_SALDOAWAL'));
					$jmlnilawal = array_sum(array_column($cekrekap, 'HARGA_SALDOAWAL'));
					$jmlqtyakhir = array_sum(array_column($cekrekap, 'KUANTITAS_SALDOAKHIR'));
					$jmlnilakhir = array_sum(array_column($cekrekap, 'HARGA_SALDOAKHIR'));

					$jmlqtytambah = array_sum(array_column($cekrekap, 'TAMBAH_QTY'));
					$jmlhargatambah = array_sum(array_column($cekrekap, 'TAMBAH_HARGA'));
					$jmlqtykurang = array_sum(array_column($cekrekap, 'KURANG_QTY'));
					$jmlhargakurang = array_sum(array_column($cekrekap, 'KURANG_HARGA'));
				?>
				<tr>
					<td colspan="4" style="text-align: center; text-transform: uppercase; font-weight: bold;">Jumlah</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlqtyawal) }}</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlnilawal) }}</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlqtytambah) }}</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlhargatambah) }}</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlqtykurang) }}</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlhargakurang) }}</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlqtyakhir) }}</td>
					<td style="text-align: right; font-weight: bold;">{{ number_format($jmlnilakhir) }}</td>
				</tr>
			</tbody>
		</table>	
	</div>
	<div class="row" style="top: 30px">
		<table>
			<tr>
				<td class="col-md-10" style="width: 100%"></td>
				<td class="col-md-1" style="width: 100%"></td>
				<td class="col-md-1" style="width: 100%; text-align: center; vertical-align: middle;">
					<p style="bottom: 0px">Jakarta, {{date('d M Y')}}</p><br>
					<p style="top: 0px">KEPALA {{ ($upd == "NONE" ? strtoupper($pd) : strtoupper($upd)) }}</p><br>
					@if(isset($nowuser['ttd']) && $nowuser['ttd'] != '')
					<img src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/publicfile/ttd/AS{{$nowuser['kolok']}}2/{{$nowuser['ttd']}}" height="100">
					@endif
					<br><br>
					<p><span style="font-weight: bold;">{{ $nowuser['nm_ka'] }}</span><br>
					NIP. {{ $nowuser['nip_ka'] }}</p><br>
				</td>
			</tr>
		</table>
	</div>
	
		<!-- <div class="row">
				<div class="col-md-2">
					<img src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/img/excel/excel-logo-dki2.png" >
				</div>
				<div class="col-md-2">
					<div class="text-center">WOWOO</div>
				</div>
				<div class="col-md-2 pull-right">
					<img src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/img/excel/excel-logo-dki2.png" >
				</div>
		</div> -->
	
	
	

	<script src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/plugins/bower_components/jquery/dist/jquery.min.js"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/ample/plugins/bower_components/datatables/jquery.dataTables.min.js"></script>
	<script>
		$(function () {
			$('.myTable').DataTable({
				"ordering" : false,
				// "searching": false,
				// "bPaginate": false,
				// "bInfo" : false,
				"lengthChange": true,
				// "pageLength": 20,
				// "scrollY": "200px",
			});
		});
	</script>
</body>
</html>