<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Dat_materi;

session_start();

class LandingController extends Controller
{
	public function index()
	{
		return view('index');
	}

	public function logout()
	{
		unset($_SESSION['user_laporan']);
		unset($_SESSION['idgroup_laporan']);
		unset($_SESSION['kolok_laporan']);
		unset($_SESSION['menus_laporan']);
		
		Auth::logout();
		return redirect('/');
	}	

	public function testes()
	{
		return view('tes');
	}
}