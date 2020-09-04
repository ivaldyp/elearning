<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Aset_quserid;
use App\Glo_profile_skpd;

session_start();

class LbkpGabunganController extends Controller
{
	public function rekap(Request $request)
	{
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
					where('tahun', $year)
					->orderBy('kolok')
					->get();
		} else {
			if ($thisprofile['kolok'] == $thisprofile['kolokskpd']) {
				$pds = Glo_profile_skpd::
						where('kolokskpd', $_SESSION['kolok'])
						->where('tahun', $year)
						->orderBy('kolok')
						->get();
			} else {
				$pds = Glo_profile_skpd::
						where('kolok', $_SESSION['kolok'])
						->where('tahun', $year)
						->orderBy('kolok')
						->get();
			}
		}

		$years = Glo_profile_skpd::
					distinct()
					->orderBy('tahun')
					->get(['tahun']);

		return view('pages.bpadlbkp.gabunganrekap')
				->with('koloknow', $kolok)
				->with('yearnow', $year)
				->with('thisprofile', $thisprofile)
				->with('pds', $pds)
				->with('years', $years);
	}

	public function excelrekap(Request $request)
	{
		$kolok = $request->koloknow;
		$year = $request->yearnow;

		$thisprofile = Glo_profile_skpd::
						where('kolok', $kolok)
						->where('tahun', $year)
						->first();

		if (is_null($thisprofile)) {
			return redirect('/lbkp/gabungan/rekap?yearnow='.$year.'&koloknow='.$kolok)
					->with('message', 'Data tidak ditemukan')
					->with('msg_num', 2);  
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();


	}

	public function detail(Request $request)
	{
		return view('pages.bpadlbkp.gabungandetail');
	}
}
