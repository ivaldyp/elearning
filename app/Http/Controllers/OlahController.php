<?php

namespace App\Http\Controllers;

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

session_start();

class OlahController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(300000);
	}

	public function intrakomptabel(Request $request)
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

		$years = Glo_profile_skpd::
					distinct()
					->orderBy('tahun', 'desc')
					->get(['tahun']);

		$laporans = Dat_laporan::
					where('sts', 1)
					->where('tampilkan', 1)
					->orderBy('kode')
					->get();

		return view('pages.bpadlaporan.olah.intrakomptabel')
				->with('koloknow', $kolok)
				// ->with('searchnow', $searchnow)
				->with('yearnow', $year)
				->with('wilnow', $request->wilnow)
				->with('thisprofile', $thisprofile)
				->with('pds', $pds->get())
				->with('years', $years)
				->with('laporans', $laporans);
	}
}
