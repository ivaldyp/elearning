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
		<img class="floatLeft" src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/img/excel/excel-logo-dki2.png" height="100">
		<img class="floatRight" src="{{ $_SERVER['DOCUMENT_ROOT'] }}/laporanbmd/public/img/excel/excel-logo-bpad2.png" height="100">
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
						<td class="col-md-9" style="width: 100%">: {{$kolokpd}} - {{ $upd == "NONE" ? strtoupper($pd) : strtoupper($upd) }}</td>
						<td class="col-md-2 tipetop">TIPE LAPORAN</td>
					</tr>
					<tr>
						<td class="col-md-1">PERIODE</td>
						<td class="col-md-9" style="width: 100%">
							@if($periode == 1)
								: TAHUNAN
							@elseif($periode == 2)
								: SEMESTER 1
							@elseif($periode == 3)
								: SEMESTER 2
							@endif
						</td>
						<td class="col-md-2 tipebot">PERSEDIAAN</td>
					</tr>
				</tbody>
			</table>	
		</div>
	</div>

	<?php
		$keykategori = [
		    "habis" => "Persediaan Bahan Pakai Habis",
		    "bahan" => "Persediaan Bahan/Material",
		    "lain" => "Persediaan Barang Lainnya",
		];
	?>

	<div class="row">
		<table style="padding-top: 15px" class="table tablelaporan table-bordered">
			<thead style="">
				<tr class="headclrblue">
					<th rowspan="2">No</th>
					<th colspan="3" rowspan="2">Kategori Barang</th>
					<th colspan="2">Saldo awal</th>
					<th colspan="2">Mutasi Bertambah</th>
					<th colspan="2">Mutasi Berkurang</th>
					<th colspan="2">Saldo akhir</th>
				</tr>
				<tr class="headclrblue">
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
					<th  colspan="3">2</th>
					<th >3</th>
					<th >4</th>
					<th >5</th>
					<th >6</th>
					<th >7</th>
					<th >8</th>
					<th >9</th>
					<th >10</th>

				</tr>
			</thead>
			<tbody>
				@php ($i = 0) @endphp
				@foreach($persediaan as $key => $data)
				<tr>
					<td style="text-align: center;">{{ $i+1 }}</td>
					<td colspan="3">{{ $keykategori[$key] }}</td>
					<td style="text-align: right;">{{ number_format($data['awalqty']) ?? 0 }}</td>
					<td style="text-align: right;">{{ number_format($data['awalnil']) ?? 0 }}</td>
					<td style="text-align: right;">{{ number_format($data['tambahqty']) ?? 0 }}</td>
					<td style="text-align: right;">{{ number_format($data['tambahnil']) ?? 0 }}</td>
					<td style="text-align: right;">{{ number_format($data['kurangqty']) ?? 0 }}</td>
					<td style="text-align: right;">{{ number_format($data['kurangnil']) ?? 0 }}</td>
					<td style="text-align: right;">{{ number_format($data['akhirqty']) ?? 0 }}</td>
					<td style="text-align: right;">{{ number_format($data['akhirnil']) ?? 0 }}</td>
				</tr>
				@php $i++ @endphp
				@endforeach
				<?php
					$jmlqtyawal = array_sum(array_column($persediaan, 'awalqty'));
					$jmlnilawal = array_sum(array_column($persediaan, 'awalnil'));
					$jmlqtyakhir = array_sum(array_column($persediaan, 'akhirqty'));
					$jmlnilakhir = array_sum(array_column($persediaan, 'akhirnil'));

					$jmlqtytambah = array_sum(array_column($persediaan, 'tambahqty'));
					$jmlhargatambah = array_sum(array_column($persediaan, 'tambahnil'));
					$jmlqtykurang = array_sum(array_column($persediaan, 'kurangqty'));
					$jmlhargakurang = array_sum(array_column($persediaan, 'kurangnil'));
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