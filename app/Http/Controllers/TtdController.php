<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\SessionCheckTraits;

use App\Aset_quserid;
use App\Dat_ttd;
use App\Glo_profile_skpd;
use App\Sec_access;
use App\Sec_menu;

session_start();

class TtdController extends Controller
{
	public function __construct()
	{
		// $this->middleware('auth');
		set_time_limit(300);
	}

    public function index(Request $request)
    {
    	if (Auth::user()->usname_skpd) {
    		$ttd = Dat_ttd::
    			where('usname', Auth::user()->usname_skpd)
    			->where('sts', 1)
    			->first();
    	} else {
    		$ttd = null;
    	}

    	if (is_null($ttd)) {
    		$ttd = null;
    	}

    	return view('pages.bpadttd.index')
    			->with('ttd', $ttd);
    }

    public function forminsertttd(Request $request)
    {

    	if (isset($request->ttd)) {
			$file = $request->ttd;

			if (strtolower($file->getClientOriginalExtension()) != "png" && strtolower($file->getClientOriginalExtension()) != "jpg" && strtolower($file->getClientOriginalExtension()) != "jpeg") {
				return redirect('/tanda tangan')->with('message', 'File yang diunggah harus berbentuk JPG / JPEG / PNG');     
			} 

			if ($_SESSION['user_data']['TLEVEL'] == 2) {
				$file_name = "ttdka";
			} else {
				$file_name = "ttdpb";
			}

			$file_name .= Auth::user()->usname_skpd;
			$file_name .= ".". $file->getClientOriginalExtension();

			$tujuan_upload = config('app.savefilettd');
			$tujuan_upload .= "\\" . Auth::user()->usname_skpd;

			// List of name of files inside 
			// specified folder 
			$allfiles = glob($tujuan_upload.'/*');  
			   
			// Deleting all the files in the list 
			foreach($allfiles as $all) { 
			   
			    if(is_file($all))  
			    
			        // Delete the given file 
			        unlink($all);  
			} 

			$file->move($tujuan_upload, $file_name);
		}

		if (is_null($request->ttd)) {
			$file_name = '';
		}

		$cekttd = Dat_ttd::
					where('sts', 1)
					->where('usname', Auth::user()->usname_skpd)
					->first();

		if ($cekttd) {
			Dat_ttd::
				where('sts', 1)
				->where('usname', Auth::user()->usname_skpd)
				->update([
					'nama'		=> ($request->nama ?? ''),
				]);

			if ($file_name != '') {
				Dat_ttd::
					where('sts', 1)
					->where('usname', Auth::user()->usname_skpd)
					->update([
						'ttd'		=> $file_name,
					]);
			}
		} else {
			$insertttd = [
				'sts'       => 1,
				'tgl'       => date('Y-m-d H:i:s'),
				'uname'     => Auth::user()->usname_skpd,
				'usname'	=> Auth::user()->usname_skpd,
				'nama'		=> ($request->nama ?? ''),
				'ttd'		=> $file_name,
			];
			Dat_ttd::insert($insertttd);
		}

		return redirect('/tanda tangan')
					->with('message', 'Berhasil memasukkan tanda tangan')
					->with('msg_num', 1);
    }

    public function formdeletettd(Request $request)
    {
    	$usname = $request->usname;

    	$fullpath = config('app.savefilettd') . "\\" . $usname;
		$fullpath .= "\\*";

		$files = glob($fullpath); // get all file names

		foreach($files as $file) { // iterate files
		  	if(is_file($file))
		    	unlink($file); // delete file
		}

		Dat_ttd::
			where('sts', 1)
			->where('usname', $usname)
			->update([
				'sts'	=> 0,
			]);

    	return redirect('/tanda tangan')
					->with('message', 'Berhasil menghapus tanda tangan')
					->with('msg_num', 1);
    }
}
