<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use App\Traits\SessionCheckTraits;

use App\Aset_quserid;
use App\Emp_data;
use App\Glo_profile_skpd;
use App\Sec_access;
use App\Sec_logins;
use App\Sec_menu;
use App\Sec_student;

session_start();

class HomeController extends Controller
{
	use SessionCheckTraits;

	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(300);
	}

	public function display_menus($query, $parent, $level = 0, $idgroup)
	{
		if ($parent == 0) {
			$sao = "(sao = 0 or sao is null)";
		} else {
			$sao = "(sao = ".$parent.")";
		}
							
		$query = DB::select( DB::raw("
					SELECT *
					FROM bpadlaporan.dbo.sec_menu
					JOIN bpadlaporan.dbo.sec_access ON bpadlaporan.dbo.sec_access.idtop = bpadlaporan.dbo.sec_menu.ids
					WHERE bpadlaporan.dbo.sec_access.idgroup = '$idgroup'
					AND bpadlaporan.dbo.sec_access.zviw = 'y'
					AND $sao
					AND bpadlaporan.dbo.sec_menu.tampilnew = 1
					ORDER BY bpadlaporan.dbo.sec_menu.urut
					"));
		$query = json_decode(json_encode($query), true);

		$result = '';

		$link = '';
		$arrLevel = ['<ul class="nav" id="side-menu">', '<ul class="nav nav-second-level">', '<ul class="nav nav-third-level">', '<ul class="nav nav-fourth-level">', '<ul class="nav nav-fourth-level">'];

		if (count($query) > 0) {

			$result .= $arrLevel[$level];

			if ($level == 0) {
				$result .= '<li id="li_portal"> <a href="/laporanbmd" class="waves-effect"> <i class="fa fa-globe fa-fw"></i> <span class="hide-menu">Laporan BPAD</span></a></li>';
			}
		
			foreach ($query as $menu) {
				if (is_null($menu['urlnew'])) {
					$link = 'javascript:void(0)';
				} else {
					if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
						$link = "https"; 
					else
						$link = "http"; 
					  
					$link .= "://";       
					$link .= $_SERVER['HTTP_HOST']; 
					$link .= $menu['urlnew'];
				}

				if ($menu['child'] == 0) {
					$result .= '<li> <a href="'.$link.'" class="waves-effect"><i class="fa '. (($menu['iconnew'])? $menu['iconnew'] :'').' fa-fw"></i> <span class="hide-menu">'.$menu['desk'].'</span></a></li>';
					
				} elseif ($menu['child'] == 1) {
					$result .= '<li> <a href="'.$link.'" class="waves-effect"><i class="fa '. (($menu['iconnew'])? $menu['iconnew'] :'').' fa-fw"></i> <span class="hide-menu">'.$menu['desk'].'<span class="fa arrow"></span></span></a>';
					
					$result .= $this->display_menus($query, $menu['ids'], $level+1, $idgroup);

					$result .= '</li>';
				}
			}

			$result .= '</ul>';
		}
		return $result;
	}

	// public function password(Request $request)
	// {
	// 	if (Auth::user()->id_emp) {
	// 		$ids = Auth::user()->id_emp;

	// 		Emp_data::
	// 		where('id_emp', $ids)
	// 		->update([
	// 			'passmd5' => md5($request->passmd5),
	// 		]);
	// 	} else {
	// 		$ids = Auth::user()->usname;

	// 		Sec_logins::
	// 		where('usname', $ids)
	// 		->update([
	// 			'passmd5' => md5($request->passmd5),
	// 		]);
	// 	}

	// 	return redirect('/home')
	// 				->with('message', 'Password berhasil diubah')
	// 				->with('msg_num', 1);
	// }

	public function index(Request $request)
	{
		// $this->checkSessionTime();
		
		unset($_SESSION['user_laporan']);
		unset($_SESSION['idgroup_laporan']);
		unset($_SESSION['kolok_laporan']);
		unset($_SESSION['menus_laporan']);


		date_default_timezone_set('Asia/Jakarta');
		
		if (Auth::user()->usname_skpd) {
			$iduser = Auth::user()->usname_skpd;
			$_SESSION['kolok_laporan'] = substr($iduser, 2, -1);

			// $user_data = Aset_quserid::
			// 				where('usname', $iduser)
			// 				->first();

			$asetid1 = Aset_quserid::
							where('usname', $iduser)
							->first();
			$asetid1 = new Collection($asetid1);

			$asetid2 = Glo_profile_skpd::
							where('kolok', $_SESSION['kolok_laporan'])
							->orderBy('tahun', 'desc')
							->first();
			$asetid2 = new Collection($asetid2);

			$merged = $asetid1->merge($asetid2);

			$user_data = $merged;

			$idgroup = 'SKPD';


		} elseif (Auth::user()->usname_admin) {
			$iduser = Auth::user()->usname_admin;

			$user_data = Sec_logins::where('usname', $iduser)->first();

			Sec_logins::where('usname', $user_data['usname'])
			->update([
				'lastlogin' => date('Y-m-d H:i:s'),
			]);	

			$idgroup = $user_data['idgroup'];

			// $_SESSION['kolok_laporan'] = '512';
		} elseif (Auth::user()->id_emp){
			$iduser = Auth::user()->id_emp;

			$user_data = Emp_data::where('id_emp', $iduser)->first();

			Emp_data::where('id_emp', $user_data['id_emp'])
			->update([
				'lastlogin' => date('Y-m-d H:i:s'),
			]);	

			$idgroup = $user_data['idgroup'];

			// $_SESSION['kolok_laporan'] = '512';
		}

		$_SESSION['idgroup_laporan'] = $idgroup;
		$_SESSION['user_laporan'] = $user_data;

		$all_menu = [];

		$menus = $this->display_menus($all_menu, 0, 0, $_SESSION['idgroup_laporan']);

		$_SESSION['menus_laporan'] = $menus;

		//////////////////////////////////////////////
		if ($request->yearnow) {
			$year = $request->yearnow;
		} else {
			$year = 2020;
		}

		$years = Glo_profile_skpd::
					distinct()
					->orderBy('tahun', 'desc')
					->get(['tahun']);

		//MULAI KIB
		// $arraykib = array();

		//KIB A
		$tbla = "bpadlaporandata.dbo.[" . $year . "_A_SALDOAKHIR]";
		$query = DB::select( DB::raw("
					IF OBJECT_ID('$tbla') IS NOT NULL
					   BEGIN
						  select 1 as nilai
					   END;
					ELSE
					   BEGIN
						  select 0 as nilai
					   END;"))[0];
		$query = json_decode(json_encode($query), true);
		if ($query['nilai'] == 1) {
			$kiba = DB::select( DB::raw("
						SELECT count(sts) as total
						FROM $tbla
						WHERE sts = 1
						"))[0];
			$kiba = json_decode(json_encode($kiba), true);
			$arraykib['KIBA'] = $kiba['total'];
		} else {
			$kiba = 0;
			$arraykib['KIBA'] = 0; 
		}

		//KIB B
		$tblb = "bpadlaporandata.dbo.[" . $year . "_B_SALDOAKHIR]";
		$query = DB::select( DB::raw("
					IF OBJECT_ID('$tblb') IS NOT NULL
					   BEGIN
						  select 1 as nilai
					   END;
					ELSE
					   BEGIN
						  select 0 as nilai
					   END;"))[0];
		$query = json_decode(json_encode($query), true);
		if ($query['nilai'] == 1) {
			$kibb = DB::select( DB::raw("
						SELECT count(sts) as total
						FROM $tblb
						WHERE sts = 1
						"))[0];
			$kibb = json_decode(json_encode($kibb), true);
			$arraykib['KIBB'] = $kibb['total'];
		} else {
			$kibb = 0;
			$arraykib['KIBB'] = 0; 
		}

		//KIB C
		$tblc = "bpadlaporandata.dbo.[" . $year . "_C_SALDOAKHIR]";
		$query = DB::select( DB::raw("
					IF OBJECT_ID('$tblc') IS NOT NULL
					   BEGIN
						  select 1 as nilai
					   END;
					ELSE
					   BEGIN
						  select 0 as nilai
					   END;"))[0];
		$query = json_decode(json_encode($query), true);
		if ($query['nilai'] == 1) {
			$kibc = DB::select( DB::raw("
						SELECT count(sts) as total
						FROM $tblc
						WHERE sts = 1
						"))[0];
			$kibc = json_decode(json_encode($kibc), true);
			$arraykib['KIBC'] = $kibc['total'];
		} else {
			$kibc = 0;
			$arraykib['KIBC'] = 0; 
		}

		//KIB D
		$tbld = "bpadlaporandata.dbo.[" . $year . "_D_SALDOAKHIR]";
		$query = DB::select( DB::raw("
					IF OBJECT_ID('$tbld') IS NOT NULL
					   BEGIN
						  select 1 as nilai
					   END;
					ELSE
					   BEGIN
						  select 0 as nilai
					   END;"))[0];
		$query = json_decode(json_encode($query), true);
		if ($query['nilai'] == 1) {
			$kibd = DB::select( DB::raw("
						SELECT count(sts) as total
						FROM $tbld
						WHERE sts = 1
						"))[0];
			$kibd = json_decode(json_encode($kibd), true);
			$arraykib['KIBD'] = $kibd['total'];
		} else {
			$kibd = 0;
			$arraykib['KIBD'] = 0; 
		}

		//KIB E
		$tble = "bpadlaporandata.dbo.[" . $year . "_E_SALDOAKHIR]";
		$query = DB::select( DB::raw("
					IF OBJECT_ID('$tble') IS NOT NULL
					   BEGIN
						  select 1 as nilai
					   END;
					ELSE
					   BEGIN
						  select 0 as nilai
					   END;"))[0];
		$query = json_decode(json_encode($query), true);
		if ($query['nilai'] == 1) {
			$kibe = DB::select( DB::raw("
						SELECT count(sts) as total
						FROM $tble
						WHERE sts = 1
						"))[0];
			$kibe = json_decode(json_encode($kibe), true);
			$arraykib['KIBE'] = $kibe['total'];
		} else {
			$kibe = 0;
			$arraykib['KIBE'] = 0; 
		}

		//KIB F
		$tblf = "bpadlaporandata.dbo.[" . $year . "_F_SALDOAKHIR]";
		$query = DB::select( DB::raw("
					IF OBJECT_ID('$tblf') IS NOT NULL
					   BEGIN
						  select 1 as nilai
					   END;
					ELSE
					   BEGIN
						  select 0 as nilai
					   END;"))[0];
		$query = json_decode(json_encode($query), true);
		if ($query['nilai'] == 1) {
			$kibf = DB::select( DB::raw("
						SELECT count(sts) as total
						FROM $tblf
						WHERE sts = 1
						"))[0];
			$kibf = json_decode(json_encode($kibf), true);
			$arraykib['KIBF'] = $kibf['total'];
		} else {
			$kibf = 0;
			$arraykib['KIBF'] = 0; 
		}

		return view('home')
				->with('years', $years)
				->with('yearnow', $year)
				->with('arraykib', $arraykib);
	}
}
