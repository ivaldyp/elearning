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

session_start();

class SecurityController extends Controller
{
	use SessionCheckTraits;

	public function display_roles($query, $idgroup, $access, $parent, $level = 0)
    {
        $query = Sec_menu::
                join('sec_access', 'sec_access.idtop', '=', 'sec_menu.ids')
                ->where('sec_menu.tipe', 'l')
                ->whereRaw('LEN(sec_menu.urut) = 1')
                ->where('sec_access.idgroup', $idgroup)
                // ->where('sec_access.zviw', 'y')
                ->where('sec_menu.sao', $parent)
                ->orderByRaw('CONVERT(INT, sec_menu.sao)')
                ->orderBy('sec_menu.urut')
                ->get();

        $result = '';

        if (count($query) > 0) {
            foreach ($query as $menu) {
            	$padding = ($level * 20) + 8;
                $result .= '<tr>
                				<td class="col-md-1">'.$level.'</td>
		        				<td style="padding-left:'.$padding.'px; '.(($level == 0) ? 'font-weight: bold;"' : '').'">'.$menu['desk'].'</td>
		        				<td>'.(($menu['zviw'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zadd'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zupd'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zdel'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.(($menu['zapr'] == 'y')? '<i style="color:green;" class="fa fa-check"></i>' : '<i style="color:red;" class="fa fa-times"></i>').'</td>
		        				<td>'.$menu['zket'].'</td>
		        				'.(($access['zupd'] == 'y' || $access['zdel'] == 'y') ? 

		        				'<td>
		        					'.(($access['zupd'] == 'y') ? 
			        					'<button type="button" class="btn btn-info btn-update" data-toggle="modal" data-target="#modal-update" data-ids="'.$menu['ids'].'" data-idgroup="'.$menu['idgroup'].'" data-zviw="'.$menu['zviw'].'" data-zadd="'.$menu['zadd'].'" data-zupd="'.$menu['zupd'].'" data-zdel="'.$menu['zdel'].'" data-zapr="'.$menu['zapr'].'" data-zket="'.$menu['zket'].'"><i class="fa fa-edit"></i></button>'
		        					: '').'
		        				</td>'

		        				: '' ).'
		        				
		        			</tr>';

                if ($menu['child'] == 1) {
                    $result .= $this->display_roles($query, $idgroup, $access, $menu['ids'], $level+1);
                }
            }
        }
        return $result;
    }

	public function grupall()
	{
		$this->checkSessionTime();
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		$groups = Sec_access::
					distinct('idgroup')
					->get('idgroup');

		return view('pages.bpadsecurity.grupuser')
				->with('access', $access)
				->with('groups', $groups);
	}

	public function grupubah(Request $request)
	{
		$this->checkSessionTime();
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		$groups = Sec_access::
					distinct('idgroup')
					->get('idgroup');

		$all_menu = [];

        $menus = $this->display_roles($all_menu, $request->name, $access, 0);


       	$pagename = $request->name;

		return view('pages.bpadsecurity.ubahgrup')
				->with('access', $access)
				->with('pagename', $pagename)
				->with('menus', $menus)
				->with('groups', $groups);
	}

	public function formupdategrup(Request $request)
	{
		$this->checkSessionTime();
		// $access = $this->checkAccess($_SESSION['user_data']['idgroup'], 4);

		if (!(isset($request->zviw))) {
			$request->zviw = 0;
		}

		if (!(isset($request->zadd))) {
			$request->zadd = 0;
		}

		if (!(isset($request->zupd))) {
			$request->zupd = 0;
		}

		if (!(isset($request->zdel))) {
			$request->zdel = 0;
		}

		if (!(isset($request->zapr))) {
			$request->zapr = 0;
		}
		var_dump($request->all());
		die(); 

		return view('pages.bpadsecurity.ubahgrup')
				->with('access', $access)
				->with('pagename', $pagename)
				->with('menus', $menus)
				->with('groups', $groups);
	}
}