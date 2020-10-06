<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Sec_access;
use App\Sec_menu;
use App\Dat_materi;

session_start();

class MateriController extends Controller
{
    use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function materiall(Request $request)
	{
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_laporan']['idgroup'], $thismenu['ids']);

		$materis = Dat_materi::
					where('sts', 1)
					->where('sao', 0)
					->orderBy('urut', 'asc')
					->get();

		return view('pages.bpadsetup.materi')
				->with('access', $access)
				->with('materis', $materis);
	}

	public function forminsertmateri(Request $request)
	{

		$urut = intval(Dat_materi::
						where('sao', 0)
						->where('sts', 1)
						->max('urut'));

		if ($request->urut) {
			$counturut = Dat_materi::
							where('sts', 1)
							->where('sao', 0)
							->where('urut', $request->urut)
							->count();

			if ($counturut != 0) {
				return redirect('/setup/materi')
					->with('message', 'Ganti nomor urut tersebut, atau kosongkan saja untuk Auto Assign')
					->with('msg_num', 2);
			}

			$urut = $request->urut;
		} else {
			if (is_null($urut)) {
				$urut = 1;
			} else {
				$urut = $urut + 1;
			}
		}

		$insertmateri = [
				'sts'       => 1,
				'tgl'       => date('Y-m-d H:i:s'),
				'updated_at'=> date('Y-m-d H:i:s'),
				'uname'     => Auth::user()->usname,
				'nm_materi'	=> $request->nm_materi,
				'sao'		=> 0,
				'url'		=> '',
				'urut'		=> $urut,
				'child'		=> 0,
				'tampilkan'	=> $request->tampilkan,
				'total'		=> 0,
			];

		Dat_materi::insert($insertmateri);

		return redirect('/setup/materi')
					->with('message', 'Materi berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatemateri(Request $request)
	{

		Dat_materi::
			where('ids', $request->ids)
			->update([
				'updated_at'=> date('Y-m-d H:i:s'),
				'nm_materi'	=> $request->nm_materi,
				'urut'      => $request->urut,
				'tampilkan' => $request->tampilkan,
			]);

		return redirect('/setup/materi')
					->with('message', 'Materi berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletemateri(Request $request)
	{
		Dat_materi::
			where('ids', $request->ids)
			->update([
				'sts'		=> 0,
				'updated_at'=> date('Y-m-d H:i:s'),
			]);

		Dat_materi::
			where('sao', $request->ids)
			->update([
				'sts'		=> 0,
				'updated_at'=> date('Y-m-d H:i:s'),
			]);

		return redirect('/setup/materi')
					->with('message', 'Materi berhasil dihapus')
					->with('msg_num', 1);
	}

	public function materivideoall(Request $request)
	{
		$parentid = $request->mat;

		$videos = Dat_materi::
			where('ids', $parentid)
			->orWhere('sao', $parentid)
			->where('sts', 1)
			->orderBy('sao', 'asc')
			->orderBy('urut', 'asc')
			->get();

		return view('pages.bpadsetup.video')
				->with('parentid', $parentid)
				->with('videos', $videos);
	}

	public function forminsertvideo(Request $request)
	{
		$sao = $request->sao;

		$urut = intval(Dat_materi::
						where('sao', $sao)
						->where('sts', 1)
						->max('urut'));

		if ($request->urut) {
			$counturut = Dat_materi::
							where('sts', 1)
							->where('sao', $sao)
							->where('urut', $request->urut)
							->count();

			if ($counturut != 0) {
				return redirect('/setup/materi/video?mat='.$sao)
					->with('message', 'Ganti nomor urut tersebut, atau kosongkan saja untuk Auto Assign')
					->with('msg_num', 2);
			}

			$urut = $request->urut;
		} else {
			if (is_null($urut)) {
				$urut = 1;
			} else {
				$urut = $urut + 1;
			}
		}

		//insert sub materi
		$insertmateri = [
				'sts'       => 1,
				'tgl'       => date('Y-m-d H:i:s'),
				'updated_at'=> date('Y-m-d H:i:s'),
				'uname'     => Auth::user()->usname,
				'nm_materi'	=> $request->nm_materi,
				'sao'		=> $sao,
				'url'		=> $request->url,
				'urut'		=> $urut,
				'child'		=> 0,
				'tampilkan'	=> $request->tampilkan,
				'total'		=> 0,
			];
		Dat_materi::insert($insertmateri);

		if ($request->tampilkan == 1) {
			$total = "total + 1";
		} else {
			$total = "total + 0";
		}

		// update parent materi
		Dat_materi::
			where('ids', $sao)
			->update([
				'updated_at'=> date('Y-m-d H:i:s'),
				'child'		=> 1,
				'total'		=> DB::raw($total), 
			]);

		return redirect('/setup/materi/video?mat='.$sao)
					->with('message', 'Sub Materi berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatevideo (Request $request)
	{
		$prev = Dat_materi::
					where('ids', $request->ids)
					->first();

		Dat_materi::
			where('ids', $request->ids)
			->update([
				'updated_at'=> date('Y-m-d H:i:s'),
				'nm_materi'	=> $request->nm_materi,
				'url'		=> $request->url,
				'urut'      => $request->urut,
				'tampilkan' => $request->tampilkan,
			]);

		if ($request->tampilkan != $prev['tampilkan']) {
			if ($request->tampilkan == 0) {
				Dat_materi::
					where('ids', $prev['sao'])
					->update([
						'total'		=> DB::raw('total - 1'), 
						'child'		=> DB::raw('case when (total-1) = 0 then 0 else 1 end'),
						'updated_at'=> date('Y-m-d H:i:s'),
					]);
			} else {
				Dat_materi::
					where('ids', $prev['sao'])
					->update([
						'total'		=> DB::raw('total + 1'), 
						'child'		=> 1,
						'updated_at'=> date('Y-m-d H:i:s'),
					]);
			}
		}

		return redirect('/setup/materi/video?mat='.$prev['sao'])
					->with('message', 'Materi berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletevideo (Request $request)
	{
		$parent = Dat_materi::where('ids', $request->parent)->first();

		Dat_materi::
			where('ids', $request->ids)
			->update([
				'sts'		=> 0,
				'updated_at'=> date('Y-m-d H:i:s'),
			]);

		Dat_materi::
			where('ids', $request->parent)
			->update([
				'total'		=> DB::raw('total - 1'), 
				'child'		=> DB::raw('case when (total-1) = 0 then 0 else 1 end'),
				'updated_at'=> date('Y-m-d H:i:s'),
			]);

		return redirect('/setup/materi/video?mat='.$request->parent)
					->with('message', 'Materi berhasil dihapus')
					->with('msg_num', 1);
	}
}
