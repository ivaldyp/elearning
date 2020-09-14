<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Dat_laporan;

session_start();

class SetupController extends Controller
{
    public function laporanall(Request $request)
    {
    	$laporans = Dat_laporan::
    				where('sts', 1)
    				->orderBy('kode')
    				->get();

    	return view('pages.bpadsetup.laporan')
				->with('laporans', $laporans);
    }

    public function forminsertlaporan(Request $request)
    {
		$insert = [
			'sts'       => 1,
			'uname'     => Auth::user()->usname,
			'tgl'       => date('Y-m-d H:i:s'),
			'kode'		=> $request->kode,
			'jns_laporan' => $request->jns_laporan,
			'tampilkan'	=> $request->tampilkan ?? 0,
		];

		Dat_laporan::insert($insert);

		return redirect('/setup/laporan')
					->with('message', 'Laporan baru berhasil ditambah')
					->with('msg_num', 1);
    }

    public function formupdatelaporan (Request $request)
    {
    	Dat_laporan::
			where('ids', $request->ids)
			->update([
				'kode'		=> $request->kode,
				'jns_laporan' => $request->jns_laporan,
				'tampilkan'	=> $request->tampilkan ?? 0,
			]);

		return redirect('/setup/laporan')
					->with('message', 'Laporan berhasil diubah')
					->with('msg_num', 1);
    }

    public function formdeletelaporan (Request $request)
    {
    	Dat_laporan::
			where('ids', $request->ids)
			->update([
				'sts' 	=> 0,
			]);

		return redirect('/setup/laporan')
					->with('message', 'Laporan berhasil dihapus')
					->with('msg_num', 1);
    }
}
