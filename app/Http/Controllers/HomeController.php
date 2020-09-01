<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Dat_materi;
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
				$result .= '<li id="li_portal"> <a href="/laporanbmd" class="waves-effect"> <i class="fa fa-globe fa-fw"></i> <span class="hide-menu">E-Learning BPAD</span></a></li>';
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
		$this->checkSessionTime();
		
		unset($_SESSION['user_data']);

		date_default_timezone_set('Asia/Jakarta');
		
		if (is_null(Auth::user()->usname)) {
			$iduser = Auth::user()->id_user;

			$user_data = Sec_student::where('id_user', $iduser)->first();

			Sec_student::where('id_user', $user_data['id_user'])
			->update([
				'lastlogin' => date('Y-m-d H:i:s'),
			]);	

		} else {
			$iduser = Auth::user()->usname;

			$user_data = Sec_logins::
							where('usname', $iduser)
							->first();

			Sec_logins::where('usname', $user_data['usname'])
			->update([
				'lastlogin' => date('Y-m-d H:i:s'),
			]);	
		}

		$_SESSION['user_data'] = $user_data;

		if (Auth::user()->id_user) {

			$materis = Dat_materi::
						where('sts', 1)
						->orderByRaw('(case when sao = 0 then ids else sao end), sao, ids')
						->get();

			$countmateri = Dat_materi::
					where('sts', 1)
					->where('sao', 0)
					->count();

			return view('index')
				->with('iduser', $iduser)
				->with('materis', $materis)
				->with('countmateri', $countmateri);
		} else {
			$all_menu = [];

			$menus = $this->display_menus($all_menu, 0, 0, $_SESSION['user_data']['idgroup']);

			$_SESSION['menus'] = $menus;

			return view('home')
				->with('iduser', $iduser);
		}

	}
}
