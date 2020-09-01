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

use App\Emp_data;
use App\Emp_dik;
use App\Emp_gol;
use App\Emp_jab;
use App\Fr_suratkeluar;
use App\Fr_disposisi;
use App\Glo_dik;
use App\Glo_disposisi_kode;
use App\Glo_org_golongan;
use App\Glo_org_jabatan;
use App\Glo_org_kedemp;
use App\Glo_org_lokasi;
use App\Glo_org_statusemp;
use App\glo_org_unitkerja;
use App\Kinerja_data;
use App\Kinerja_detail;
use App\Sec_access;
use App\Sec_menu;
use App\V_disposisi;

session_start();

class KepegawaianController extends Controller
{
	use SessionCheckTraits;

	public function __construct()
	{
		
		$this->middleware('auth');
		set_time_limit(300);
	}

	// ------------------ DATA PEGAWAI ------------------ //

	public function pegawaiall(Request $request)
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$units = Glo_org_unitkerja::orderBy('kd_unit')->get();

		if (is_null($request->kednow)) {
			$kednow = 'AKTIF';
		} else {
			$kednow = $request->kednow;
		}

		if (is_null($request->unit)) {
			if (Auth::user()->id_emp) {
				$idunit = $_SESSION['user_data']['idunit'];
			} else {
				$idunit = '01';
			}
		} else {
			$idunit = $request->unit;
		}

		$employees = DB::select( DB::raw("  
					SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.nm_unit, tbunit.child, d.nm_lok from bpadlaporan.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpadlaporan.dbo.emp_gol,bpadlaporan.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpadlaporan.dbo.emp_dik,bpadlaporan.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and idunit like '$idunit%' AND ked_emp = '$kednow'
					order by idunit asc, nm_emp ASC") );
		$employees = json_decode(json_encode($employees), true);
		
		$kedudukans = Glo_org_kedemp::get();

		return view('pages.bpadkepegawaian.pegawai')
				->with('access', $access)
				->with('kednow', $kednow)
				->with('idunit', $idunit)
				->with('employees', $employees)
				->with('units', $units)
				->with('kedudukans', $kedudukans);
		
	}

	public function pegawaitambah()
	{
		$this->checkSessionTime();

		$id_emp = explode(".", Emp_data::max('id_emp'));

		$statuses = Glo_org_statusemp::get();

		$idgroups = Sec_access::
					distinct('idgroup')
					->where('zfor', '2,')
					->orderBy('idgroup')
					->get('idgroup');

		$pendidikans = Glo_dik::
						orderBy('urut')
						->get();

		$golongans = Glo_org_golongan::
					orderBy('gol')
					->get();

		$jabatans = Glo_org_jabatan::
					orderBy('jabatan')
					->get();

		$lokasis = Glo_org_lokasi::
					orderBy('kd_lok')
					->get();

		$kedudukans = Glo_org_kedemp::get();

		$units = glo_org_unitkerja::orderBy('kd_unit', 'asc')->get();

		return view('pages.bpadkepegawaian.pegawaitambah')
				->with('id_emp', $id_emp)
				->with('statuses', $statuses)
				->with('idgroups', $idgroups)
				->with('pendidikans', $pendidikans)
				->with('golongans', $golongans)
				->with('jabatans', $jabatans)
				->with('lokasis', $lokasis)
				->with('kedudukans', $kedudukans)
				->with('units', $units);
	}

	public function pegawaiubah(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $request->id_emp;

		$emp_data = Emp_data::
						where('id_emp', $id_emp)
						->first();

		$emp_dik = Emp_dik::
						where('noid', $id_emp)
						->where('sts', 1)
						->orderBy('th_sek', 'desc')
						->get();

		$emp_gol = Emp_gol::
						where('noid', $id_emp)
						->where('sts', 1)
						->orderBy('tmt_gol', 'desc')
						->get();

		$emp_jab = Emp_jab::
						with('lokasi')
						->with('unit')
						->where('noid', $id_emp)
						->where('sts', 1)
						->orderBy('tmt_jab', 'desc')
						->get();

		$statuses = Glo_org_statusemp::get();

		$idgroups = Sec_access::
					distinct('idgroup')
					->where('zfor', '2,')
					->orderBy('idgroup')
					->get('idgroup');

		$pendidikans = Glo_dik::
						orderBy('urut')
						->get();

		$golongans = Glo_org_golongan::
					orderBy('gol')
					->get();

		$jabatans = Glo_org_jabatan::
					orderBy('jabatan')
					->get();

		$lokasis = Glo_org_lokasi::
					orderBy('kd_lok')
					->get();

		$kedudukans = Glo_org_kedemp::get();

		$units = glo_org_unitkerja::orderBy('kd_unit', 'asc')->get();

		return view('pages.bpadkepegawaian.pegawaiubah')
				->with('id_emp', $id_emp)
				->with('emp_data', $emp_data)
				->with('emp_dik', $emp_dik)
				->with('emp_gol', $emp_gol)
				->with('emp_jab', $emp_jab)
				->with('statuses', $statuses)
				->with('idgroups', $idgroups)
				->with('pendidikans', $pendidikans)
				->with('golongans', $golongans)
				->with('jabatans', $jabatans)
				->with('lokasis', $lokasis)
				->with('kedudukans', $kedudukans)
				->with('units', $units);
	}

	public function forminsertpegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = explode(".", Emp_data::max('id_emp'));
		$new_id_emp = $id_emp[0] . "." . $id_emp[1] . "." . $id_emp[2] . "." . ($id_emp[3] + 1);

		$filefoto = '';
		// $filettd = '';
		// $fileijazah = '';
		// $fileskgol = '';
		// $fileskjab = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		// if (isset($request->filefoto)) {
		// 	$file = $request->filefoto;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/tambah pegawai')->with('message', 'Ukuran file foto pegawai terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$filefoto .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgemp');
		// 	$file->move($tujuan_upload, $filefoto);
		// }
			
		// if (!(isset($filefoto))) {
		// 	$filefoto = '';
		// }

		// (IDENTITAS) cek dan set variabel untuk file foto ttd pegawai
		// if (isset($request->filettd)) {
		// 	$file = $request->filettd;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/tambah pegawai')->with('message', 'Ukuran file foto tandatangan terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$filettd .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgttd');
		// 	$file->move($tujuan_upload, $filettd);
		// }
			
		// if (!(isset($filettd))) {
		// 	$filettd = '';
		// }

		// (PENDIDIKAN) cek dan set variabel untuk file foto ijazah
		// if (isset($request->fileijazah)) {
		// 	$file = $request->fileijazah;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/tambah pegawai')->with('message', 'Ukuran file foto ijazah terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$fileijazah .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgdik');
		// 	$file->move($tujuan_upload, $fileijazah);
		// }
			
		// if (!(isset($fileijazah))) {
		// 	$fileijazah = '';
		// }

		// (GOLONGAN) cek dan set variabel untuk file SK Golongan
		// if (isset($request->fileskgol)) {
		// 	$file = $request->fileskgol;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/tambah pegawai')->with('message', 'Ukuran file foto SK golongan terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$fileskgol .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimggol');
		// 	$file->move($tujuan_upload, $fileskgol);
		// }
			
		// if (!(isset($fileskgol))) {
		// 	$fileskgol = '';
		// }

		// (JABATAN) cek dan set variabel untuk file SK Jabatan
		// if (isset($request->fileskjab)) {
		// 	$file = $request->fileskjab;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/tambah pegawai')->with('message', 'Ukuran file foto SK jabatan terlalu besar (Maksimal 2MB)');    
		// 	} 

		// 	$fileskjab .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgjab');
		// 	$file->move($tujuan_upload, $fileskjab);
		// }
			
		// if (!(isset($fileskjab))) {
		// 	$fileskjab = '';
		// }
	
		// mulai insert

		// var_dump($request->all());
		// die();

		$insert_emp_data = [
				// IDENTITAS
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'createdate' => date('Y-m-d H:i:s'),
				'id_emp' => $new_id_emp,
				'nip_emp' => ($request->nip_emp ? $request->nip_emp : ''),
				'nrk_emp' => ($request->nrk_emp ? $request->nrk_emp : ''),
				'nm_emp' => ($request->nm_emp ? $request->nm_emp : ''),
				'gelar_dpn' => ($request->gelar_dpn ? $request->gelar_dpn : ''),
				'gelar_blk' => ($request->gelar_blk ? $request->gelar_blk : ''),
				'jnkel_emp' => $request->jnkel_emp,
				'tempat_lahir' => ($request->tempat_lahir ? $request->tempat_lahir : ''),
				'tgl_lahir' => (isset($request->tgl_lahir) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_lahir))) : null),
				'idagama' => $request->idagama,
				'alamat_emp' => ($request->alamat_emp ? $request->alamat_emp : ''),
				'tlp_emp' => ($request->tlp_emp ? $request->tlp_emp : ''),
				'email_emp' => ($request->email_emp ? $request->email_emp : ''),
				'status_emp' => $request->status_emp,
				'ked_emp' => $request->ked_emp,
				'status_nikah' => $request->status_nikah,
				'gol_darah' => $request->gol_darah,
				'nm_bank' => ($request->nm_bank ? $request->nm_bank : ''),
				'cb_bank' => ($request->cb_bank ? $request->cb_bank : ''),
				'an_bank' => ($request->an_bank ? $request->an_bank : ''),
				'nr_bank' => ($request->nr_bank ? $request->nr_bank : ''),
				'no_taspen' => ($request->no_taspen ? $request->no_taspen : ''),
				'npwp' => ($request->npwp ? $request->npwp : ''),
				'no_askes' => ($request->no_askes ? $request->no_askes : ''),
				'no_jamsos' => ($request->no_jamsos ? $request->no_jamsos : ''),
				'tgl_join' => (isset($request->tgl_join) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_join))) : null),
				'tgl_end' => null,
				'reason' => '',
				'idgroup' => $request->idgroup,
				'pass_emp' => '',
				'foto' => $filefoto,
				'lastlogin' => null,
				'lastip' => '',
				'lasttemp' => '',
				'dwinternal' => '',
				'dwaset' => '',
				'ttd' => '',
				'telegram_id' => '',
				'passmd5' => md5($request->passmd5),
				// 'tampilnew' => 1,
			];

		$insert_emp_dik = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $new_id_emp,
				'iddik' => $request->iddik,
				'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
				'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
				'no_sek' => ($request->no_sek ? $request->no_sek : ''),
				'th_sek' => ($request->th_sek ? $request->th_sek : ''),
				'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
				'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
				'ijz_cpns' => 'T',
				// 'tampilnew' => 1,
			];

		$insert_emp_gol = [
				// GOLONGAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $new_id_emp,
				'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : null),
				'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : null),
				'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
				'idgol' => $request->idgol,
				'jns_kp' => $request->jns_kp,
				'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
				'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
				// 'tampilnew' => 1,
			];

		$jabatan = explode("||", $request->jabatan);
		$jns_jab = $jabatan[0];
		$idjab = $jabatan[1];
		$insert_emp_jab = [
				// JABATAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $new_id_emp,
				'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
				'idskpd' => '1.20.512',
				'idunit' => $request->idunit,
				'idlok' => $request->idlok,
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $jns_jab,
				'idjab' => $idjab,
				'eselon' => $request->eselon,
				// 'tampilnew' => 1,
			];

		Emp_data::insert($insert_emp_data);
		Emp_dik::insert($insert_emp_dik);
		Emp_gol::insert($insert_emp_gol);
		Emp_jab::insert($insert_emp_jab);

		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatepegawai(Request $request)
	{
		$this->checkSessionTime();

		$id_emp = $request->id_emp;
		
		$filefoto = '';
		// $filettd = '';
		// $fileijazah = '';
		// $fileskgol = '';
		// $fileskjab = '';

		// (IDENTITAS) cek dan set variabel untuk file foto pegawai
		// if (isset($request->filefoto)) {
		// 	$file = $request->filefoto;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/data pegawai')->with('message', 'Ukuran file foto pegawai terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$filefoto .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgemp');
		// 	$file->move($tujuan_upload, $filefoto);
		// }
			
		// if (!(isset($filefoto))) {
		// 	$filefoto = '';
		// }

		// (IDENTITAS) cek dan set variabel untuk file foto ttd pegawai
		// if (isset($request->filettd)) {
		// 	$file = $request->filettd;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/data pegawai')->with('message', 'Ukuran file foto tandatangan terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$filettd .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgttd');
		// 	$file->move($tujuan_upload, $filettd);
		// }
			
		// if (!(isset($filettd))) {
		// 	$filettd = '';
		// }

		// (PENDIDIKAN) cek dan set variabel untuk file foto ijazah
		// if (isset($request->fileijazah)) {
		// 	$file = $request->fileijazah;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/data pegawai')->with('message', 'Ukuran file foto ijazah terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$fileijazah .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgdik');
		// 	$file->move($tujuan_upload, $fileijazah);
		// }
			
		// if (!(isset($fileijazah))) {
		// 	$fileijazah = '';
		// }

		// (GOLONGAN) cek dan set variabel untuk file SK Golongan
		// if (isset($request->fileskgol)) {
		// 	$file = $request->fileskgol;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/data pegawai')->with('message', 'Ukuran file foto SK golongan terlalu besar (Maksimal 2MB)');     
		// 	} 

		// 	$fileskgol .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimggol');
		// 	$file->move($tujuan_upload, $fileskgol);
		// }
			
		// if (!(isset($fileskgol))) {
		// 	$fileskgol = '';
		// }

		// (JABATAN) cek dan set variabel untuk file SK Jabatan
		// if (isset($request->fileskjab)) {
		// 	$file = $request->fileskjab;

		// 	if ($file->getSize() > 2222222) {
		// 		return redirect('/kepegawaian/data pegawai')->with('message', 'Ukuran file foto SK jabatan terlalu besar (Maksimal 2MB)');    
		// 	} 

		// 	$fileskjab .= $new_id_emp . ".". $file->getClientOriginalExtension();

		// 	$tujuan_upload = config('app.savefileimgjab');
		// 	$file->move($tujuan_upload, $fileskjab);
		// }
			
		// if (!(isset($fileskjab))) {
		// 	$fileskjab = '';
		// }


		// ubah semua variabel tanggal jadi format 'Ymd'
		// if (isset($request->tgl_join)) {
		// 	$tgl_join = date('Y-m-d',strtotime($request->tgl_join));
		// } else {
		// 	$tgl_join = null;
		// }

		// if (isset($request->tgl_lahir)) {
		// 	$tgl_lahir = date('Y-m-d',strtotime($request->tgl_lahir));
		// } else {
		// 	$tgl_lahir = null;
		// }

		// if (isset($request->tmt_gol)) {
		// 	$tmt_gol = date('Y-m-d',strtotime($request->tmt_gol));
		// } else {
		// 	$tmt_gol = null;
		// }

		// if (isset($request->tmt_sk_gol)) {
		// 	$tmt_sk_gol = date('Y-m-d',strtotime($request->tmt_sk_gol));
		// } else {
		// 	$tmt_sk_gol = null;
		// }

		// if (isset($request->tmt_jab)) {
		// 	$tmt_jab = date('Y-m-d',strtotime($request->tmt_jab));
		// } else {
		// 	$tmt_jab = null;
		// }

		// if (isset($request->tmt_sk_jab)) {
		// 	$tmt_sk_jab = date('Y-m-d',strtotime($request->tmt_sk_jab));
		// } else {
		// 	$tmt_sk_jab = null;
		// }
	
		// mulai insert

		Emp_data::where('id_emp', $id_emp)
			->update([
				'tgl_join' => (isset($request->tgl_join) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_join))) : null),
				'status_emp' => $request->status_emp,
				'nip_emp' => ($request->nip_emp ? $request->nip_emp : ''),
				'nrk_emp' => ($request->nrk_emp ? $request->nrk_emp : ''),
				'nm_emp' => ($request->nm_emp ? strtoupper($request->nm_emp) : ''),
				'gelar_dpn' => ($request->gelar_dpn ? $request->gelar_dpn : ''),
				'gelar_blk' => ($request->gelar_blk ? $request->gelar_blk : ''),
				'jnkel_emp' => $request->jnkel_emp,
				'tempat_lahir' => ($request->tempat_lahir ? $request->tempat_lahir : ''),
				'tgl_lahir' => (isset($request->tgl_lahir) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_lahir))) : null),
				'idagama' => $request->idagama,
				'alamat_emp' => ($request->alamat_emp ? $request->alamat_emp : ''),
				'tlp_emp' => ($request->tlp_emp ? $request->tlp_emp : ''),
				'email_emp' => ($request->email_emp ? $request->email_emp : ''),
				'status_nikah' => $request->status_nikah,
				'gol_darah' => $request->gol_darah,
				'nm_bank' => ($request->nm_bank ? $request->nm_bank : ''),
				'cb_bank' => ($request->cb_bank ? $request->cb_bank : ''),
				'an_bank' => ($request->an_bank ? $request->an_bank : ''),
				'nr_bank' => ($request->nr_bank ? $request->nr_bank : ''),
				'no_taspen' => ($request->no_taspen ? $request->no_taspen : ''),
				'npwp' => ($request->npwp ? $request->npwp : ''),
				'no_askes' => ($request->no_askes ? $request->no_askes : ''),
				'no_jamsos' => ($request->no_jamsos ? $request->no_jamsos : ''),
				'idgroup' => $request->idgroup,
			]);

		// Emp_dik::where('noid', $id_emp)
		// 	->update([
		// 		'iddik' => $request->iddik,
		// 		'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
		// 		'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
		// 		'no_sek' => ($request->no_sek ? $request->no_sek : ''),
		// 		'th_sek' => ($request->th_sek ? $request->th_sek : ''),
		// 		'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
		// 		'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
		// 		'ijz_cpns' => $request->ijz_cpns,
		// 	]);

		// Emp_gol::where('noid', $id_emp)
		// 	->update([
		// 		'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : null),
		// 		'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
		// 		'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : null),
		// 		'idgol' => $request->idgol,
		// 		'jns_kp' => $request->jns_kp,
		// 		'mk_thn' => ($request->mk_thn ? $request->mk_thn : ''),
		// 		'mk_bln' => ($request->mk_bln ? $request->mk_bln : ''),
		// 	]);

		// Emp_jab::where('noid', $id_emp)
		// 	->update([
		// 		// JABATAN
		// 		'idskpd' => '1.20.512',
		// 		'jns_jab' => $jns_jab,
		// 		'idjab' => $idjab,
		// 		'idunit' => $request->idunit,
		// 		'idlok' => $request->idlok,
		// 		'eselon' => $request->eselon,
		// 		'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
		// 		'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
		// 		'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
		// 	]);

		// if ($filefoto != '') {
		// 	Emp_data::where('id_emp', $id_emp)
		// 	->update([
		// 		'foto' => $filefoto,
		// 	]);
		// }

		// if ($filettd != '') {
		// 	Emp_data::where('id_emp', $id_emp)
		// 	->update([
		// 		'ttd' => $filettd,
		// 	]);
		// }

		// if ($fileijazah != '') {
		// 	Emp_dik::where('noid', $id_emp)
		// 	->update([
		// 		'gambar' => $fileijazah,
		// 	]);
		// }

		// if ($fileskgol != '') {
		// 	Emp_gol::where('noid', $id_emp)
		// 	->update([
		// 		'gambar' => $fileskgol,
		// 	]);
		// }

		// if ($fileskjab != '') {
		// 	Emp_jab::where('noid', $id_emp)
		// 	->update([
		// 		'gambar' => $fileskjab,
		// 	]);
		// }

		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletepegawai(Request $request)
	{
		$this->checkSessionTime();

		Emp_data::where('id_emp', $request->id_emp)
					->update([
						'sts' => 0,
						'ked_emp' => "HAPUS",
					]);

		Emp_dik::where('noid', $request->id_emp)
					->update([
						'sts' => 0,
					]);

		Emp_gol::where('noid', $request->id_emp)
					->update([
						'sts' => 0,
					]);

		Emp_jab::where('noid', $request->id_emp)
					->update([
						'sts' => 0,
					]);
					
		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Pegawai '.$request->nm_emp.' berhasil dihapus')
					->with('msg_num', 1);
	}

	public function formupdatepassuser(Request $request)
	{
		$this->checkSessionTime();

		Emp_data::
			where('id_emp', $request->id_emp)
			->update([
				'passmd5' => md5($request->passmd5),
			]);

		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Password '.$request->nm_emp.' berhasil diubah')
					->with('msg_num', 1);
	}

	public function formupdatestatuspegawai(Request $request)
	{
		$this->checkSessionTime();

		if ($request->ked_emp == 'AKTIF') {
			$tgl_end = null;
		} else {
			$tgl_end = (isset($request->tgl_end) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_end))) : null);
		}

		Emp_data::where('id_emp', $request->id_emp)
			->update([
				'tgl_end' => $tgl_end,
				'ked_emp' => $request->ked_emp,
			]);

		return redirect('/kepegawaian/data pegawai')
					->with('message', 'Status pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function forminsertdikpegawai(Request $request)
	{
		$this->checkSessionTime();

		$insert_emp_dik = [
				// PENDIDIKAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $request->noid,
				'iddik' => $request->iddik,
				'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
				'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
				'no_sek' => ($request->no_sek ? $request->no_sek : ''),
				'th_sek' => ($request->th_sek ? $request->th_sek : ''),
				'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
				'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
				'ijz_cpns' => 'T',
				'tampilnew' => 1,
			];

		Emp_dik::insert($insert_emp_dik);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data pendidikan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatedikpegawai(Request $request)
	{
		$this->checkSessionTime();

		Emp_dik::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'iddik' => $request->iddik,
				'prog_sek' => ($request->prog_sek ? $request->prog_sek : ''),
				'nm_sek' => ($request->nm_sek ? $request->nm_sek : ''),
				'no_sek' => ($request->no_sek ? $request->no_sek : ''),
				'th_sek' => ($request->th_sek ? $request->th_sek : ''),
				'gelar_dpn_sek' => ($request->gelar_dpn_sek ? $request->gelar_dpn_sek : ''),
				'gelar_blk_sek' => ($request->gelar_blk_sek ? $request->gelar_blk_sek : ''),
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data pendidikan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletedikpegawai(Request $request)
	{
		$this->checkSessionTime();

		Emp_dik::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data pendidikan '.$request->iddik.' berhasil dihapus')
					->with('msg_num', 1);
	}

	//------------------------------------------------------

	public function forminsertgolpegawai(Request $request)
	{
		$this->checkSessionTime();

		$insert_emp_gol = [
				// GOLONGAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $request->noid,
				'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : null),
				'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : null),
				'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
				'idgol' => $request->idgol,
				'jns_kp' => $request->jns_kp,
				'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
				'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
				// 'tampilnew' => 1,
			];

		Emp_gol::insert($insert_emp_gol);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data golongan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdategolpegawai(Request $request)
	{
		$this->checkSessionTime();

		Emp_gol::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'tmt_gol' => (isset($request->tmt_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_gol))) : null),
				'tmt_sk_gol' => (isset($request->tmt_sk_gol) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_gol))) : null),
				'no_sk_gol' => ($request->no_sk_gol ? $request->no_sk_gol : ''),
				'idgol' => $request->idgol,
				'jns_kp' => $request->jns_kp,
				'mk_thn' => ($request->mk_thn ? $request->mk_thn : 0),
				'mk_bln' => ($request->mk_bln ? $request->mk_bln : 0),
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data golongan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletegolpegawai(Request $request)
	{
		$this->checkSessionTime();

		Emp_gol::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data golongan '.$request->idgol.' berhasil dihapus')
					->with('msg_num', 1);
	}

	//--------------------------------------------------

	public function forminsertjabpegawai(Request $request)
	{
		$this->checkSessionTime();

		$jabatan = explode("||", $request->jabatan);
		$jns_jab = $jabatan[0];
		$idjab = $jabatan[1];
		$insert_emp_jab = [
				// JABATAN
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'noid' => $request->noid,
				'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
				'idskpd' => '1.20.512',
				'idunit' => $request->idunit,
				'idlok' => $request->idlok,
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $jns_jab,
				'idjab' => $idjab,
				'eselon' => $request->eselon,
				// 'tampilnew' => 1,
			];

		Emp_jab::insert($insert_emp_jab);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data jabatan pegawai berhasil ditambah')
					->with('msg_num', 1);
	}

	public function formupdatejabpegawai(Request $request)
	{
		$this->checkSessionTime();

		$jabatan = explode("||", $request->jabatan);
		$jns_jab = $jabatan[0];
		$idjab = $jabatan[1];

		Emp_jab::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'tmt_jab' => (isset($request->tmt_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_jab))) : null),
				'idunit' => $request->idunit,
				'idlok' => $request->idlok,
				'tmt_sk_jab' => (isset($request->tmt_sk_jab) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tmt_sk_jab))) : null),
				'no_sk_jab' => ($request->no_sk_jab ? $request->no_sk_jab : ''),
				'jns_jab' => $jns_jab,
				'idjab' => $idjab,
				'eselon' => $request->eselon,
				// 'tampilnew' => 1,
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data jabatan pegawai berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletejabpegawai(Request $request)
	{
		$this->checkSessionTime();

		Emp_jab::where('noid', $request->noid)
			->where('ids', $request->ids)
			->update([
				'sts' => 0,
			]);

		return redirect('/kepegawaian/ubah pegawai?id_emp='.$request->noid)
					->with('message', 'Data jabatan '.$request->idjab.' berhasil dihapus')
					->with('msg_num', 1);
	}

	// ------------------ DATA PEGAWAI ------------------ //

	// --------------- STRUKTUR ORGANISASI --------------- //

	public function strukturorganisasi()
	{
		$this->checkSessionTime();

		return view('pages.bpadkepegawaian.struktur');
	}

	// --------------- STRUKTUR ORGANISASI --------------- //

	// ---------------- STATUS DISPOSISI ---------------- //

	public function statusdisposisi()
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$ids = Auth::user()->id_emp;

		// if ($ids) {
		// 	$data_self = DB::select( DB::raw("  
		// 						SELECT top 1 id_emp, nrk_emp, nip_emp, nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes from bpadlaporan.dbo.emp_data as a
		// 						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
		// 						CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
		// 						,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
		// 						and id_emp like '$ids'") )[0];
		// 	$data_self = json_decode(json_encode($data_self), true);
		// } else {
		// 	$data_self = DB::select( DB::raw("  
		// 						SELECT top 1 id_emp, nrk_emp, nip_emp, nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes from bpadlaporan.dbo.emp_data as a
		// 						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
		// 						CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
		// 						,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
		// 						and idunit like '01' and ked_emp = 'aktif'") )[0];
		// 	$data_self = json_decode(json_encode($data_self), true);
		// }
	
		if ($ids) {
			$data_self = DB::select( DB::raw("  
								SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpadlaporan.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								CROSS APPLY (
									select  count(disp.rd) as 'notread' from bpadlaporan.dbo.fr_disposisi disp
									  where rd = 'N' and sts = 1
									  and disp.to_pm = a.id_emp) notread
								CROSS APPLY (
									select  count(disp.rd) as 'yesread' from bpadlaporan.dbo.fr_disposisi disp
									  where rd = 'Y' and sts = 1
									  and disp.to_pm = a.id_emp) yesread
								CROSS APPLY (
									select  count(disp.rd) as 'lanjut' from bpadlaporan.dbo.fr_disposisi disp
									  where rd = 'S' and sts = 1
									  and disp.to_pm = a.id_emp) lanjut
								,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and id_emp like '$ids'
								") )[0];
			$data_self = json_decode(json_encode($data_self), true);
		} else {
			$data_self = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpadlaporan.dbo.emp_data as a
								CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
								CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
								CROSS APPLY (
									select  count(disp.rd) as 'notread' from bpadlaporan.dbo.fr_disposisi disp
									  where rd = 'N' and sts = 1
									  and disp.to_pm = a.id_emp) notread
								CROSS APPLY (
									select  count(disp.rd) as 'yesread' from bpadlaporan.dbo.fr_disposisi disp
									  where rd = 'Y' and sts = 1
									  and disp.to_pm = a.id_emp) yesread
								CROSS APPLY (
									select  count(disp.rd) as 'lanjut' from bpadlaporan.dbo.fr_disposisi disp
									  where rd = 'S' and sts = 1
									  and disp.to_pm = a.id_emp) lanjut
								,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
								and idunit like '01' and ked_emp = 'aktif'
								") )[0];
			$data_self = json_decode(json_encode($data_self), true);
		}		

		$result = '';

		if (strlen($data_self['idunit']) < 10) {
			$result .= '<strong>';
		}

		$result .= '<tr '.(strlen($data_self['idunit']) < 10 ? 'style="font-weight:bold"' : '' ).'>
						<td>'.$data_self['id_emp'].'</td>
						<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
						<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
						<td>'.ucwords($data_self['notes']).'</td>
					';	
		$total = $data_self['notread'] + $data_self['yesread'] + $data_self['lanjut'];
		$result .= '	<td '. ($data_self['notread'] > 0 ? 'class="text-danger"' : '') .'>'.$data_self['notread'].'</td>
						<td>'.$data_self['yesread'].'</td>
						<td>'.$data_self['lanjut'].'</td>
						<td><b>'.$total.'</b></td>
					</tr>';

		if (strlen($data_self['idunit']) < 10) {
			$result .= '</strong>';
		}

		$nowunit = $data_self['idunit'];

		$data_stafs = DB::select( DB::raw("  SELECT a.id_emp, a.nrk_emp, a.nip_emp, a.nm_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.notes, d.nm_lok, notread.notread, yesread.yesread, lanjut.lanjut from bpadlaporan.dbo.emp_data as a
							CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
							CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
							CROSS APPLY (
								select  count(disp.rd) as 'notread' from bpadlaporan.dbo.fr_disposisi disp
								  where rd = 'N' and sts = 1
								  and disp.to_pm = a.id_emp) notread
							CROSS APPLY (
								select  count(disp.rd) as 'yesread' from bpadlaporan.dbo.fr_disposisi disp
								  where rd = 'Y' and sts = 1
								  and disp.to_pm = a.id_emp) yesread
							CROSS APPLY (
								select  count(disp.rd) as 'lanjut' from bpadlaporan.dbo.fr_disposisi disp
								  where rd = 'S' and sts = 1
								  and disp.to_pm = a.id_emp) lanjut
							,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
							and tbunit.sao like '$nowunit%' and ked_emp = 'aktif'
							order by idunit asc, nm_emp asc
							") );
		$data_stafs = json_decode(json_encode($data_stafs), true);

		foreach ($data_stafs as $key => $staf) {
			$result .= '<tr '.(strlen($staf['idunit']) < 10 ? 'style="font-weight:bold"' : '' ).'>
							<td>'.$staf['id_emp'].'</td>
							<td>'.(is_null($staf['nrk_emp']) || $staf['nrk_emp'] == '' ? '-' : $staf['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($staf['nm_emp'])).'</td>
							<td>'.ucwords($staf['notes']).'</td>
					';	
			$total = $staf['notread'] + $staf['yesread'] + $staf['lanjut'];
			$result .= '	<td '. ($staf['notread'] > 0 ? 'class="text-danger"' : '') .'>'.$staf['notread'].'</td>
							<td>'.$staf['yesread'].'</td>
							<td>'.$staf['lanjut'].'</td>
							<td><b>'.$total.'</b></td>
						</tr>';
		}

		return view('pages.bpadkepegawaian.statusdisposisi')
				->with('access', $access)
				->with('result', $result);

		if (strlen($data_self['idunit']) == 10) {
			// kalo dia staf
			$result = '';

			$result .= '<tr>
							<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
							<td>'.ucwords(strtolower($data_self['nm_unit'])).'</td>
						';

			$belum = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as belum
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'N'
					"))[0]), true);

			$baca = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as baca
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'Y'
					"))[0]), true);

			$balas = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as balas
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'S'
					"))[0]), true);

			$total = $belum['belum'] + $baca['baca'] + $balas['balas'];
			
			$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';

		} elseif (strlen($data_self['idunit']) == 2) {
			// kalo dia kepala badan
			$result = '<tr>
							<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
							<td>'.ucwords(strtolower($data_self['nm_unit'])).'</td>
						';

			$belum = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as belum
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$data_self['id_emp']."'
						and rd = 'N'
					"))[0]), true);

			$baca = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as baca
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$data_self['id_emp']."'
						and rd = 'Y'
					"))[0]), true);

			$balas = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as balas
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$data_self['id_emp']."'
						and rd = 'S'
					"))[0]), true);

			$total = $belum['belum'] + $baca['baca'] + $balas['balas'];

			$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';

			$idunit = $data_self['idunit'];
			$querys = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpadlaporan.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbunit.sao like '$idunit%' and ked_emp = 'aktif'
						order by tbunit.kd_unit") );
			$querys = json_decode(json_encode($querys), true);

			foreach ($querys as $key => $query) {
				$result .= '<tr>
								<td>'.(is_null($query['nrk_emp']) || $query['nrk_emp'] == '' ? '-' : $query['nrk_emp'] ).'</td>
								<td>'.ucwords(strtolower($query['nm_emp'])).'</td>
								<td>'.ucwords(strtolower($query['nm_unit'])).'</td>
							';

				$belum = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as belum
							FROM bpadlaporan.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'N'
						"))[0]), true);

				$baca = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as baca
							FROM bpadlaporan.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'Y'
						"))[0]), true);

				$balas = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as balas
							FROM bpadlaporan.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'S'
						"))[0]), true);

				$total = $belum['belum'] + $baca['baca'] + $balas['balas'];
				
				$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';
			}
		} else {
			// kalo dia atasan biasa
			$result = '<tr>
							<td>'.(is_null($data_self['nrk_emp']) || $data_self['nrk_emp'] == '' ? '-' : $data_self['nrk_emp'] ).'</td>
							<td>'.ucwords(strtolower($data_self['nm_emp'])).'</td>
							<td>'.ucwords(strtolower($data_self['nm_unit'])).'</td>
						';

			$belum = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as belum
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'N'
					"))[0]), true);

			$baca = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as baca
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'Y'
					"))[0]), true);

			$balas = json_decode(json_encode(DB::select( DB::raw("
						SELECT Count(id_emp) as balas
						FROM bpadlaporan.dbo.v_disposisi
						where id_emp like '".$ids."'
						and rd = 'S'
					"))[0]), true);

			$total = $belum['belum'] + $baca['baca'] + $balas['balas'];

			$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';

			$idunit = $data_self['idunit'];
			$querys = DB::select( DB::raw("  
						SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit from bpadlaporan.dbo.emp_data as a
						CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
						CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
						,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
						and tbunit.sao like '$idunit%' and ked_emp = 'aktif'
						order by tbunit.kd_unit") );
			$querys = json_decode(json_encode($querys), true);

			foreach ($querys as $key => $query) {
				$result .= '<tr>
								<td>'.(is_null($query['nrk_emp']) || $query['nrk_emp'] == '' ? '-' : $query['nrk_emp'] ).'</td>
								<td>'.ucwords(strtolower($query['nm_emp'])).'</td>
								<td>'.ucwords(strtolower($query['nm_unit'])).'</td>
							';

				$belum = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as belum
							FROM bpadlaporan.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'N'
						"))[0]), true);

				$baca = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as baca
							FROM bpadlaporan.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'Y'
						"))[0]), true);

				$balas = json_decode(json_encode(DB::select( DB::raw("
							SELECT Count(id_emp) as balas
							FROM bpadlaporan.dbo.v_disposisi
							where id_emp like '".$query['id_emp']."'
							and rd = 'S'
						"))[0]), true);

				$total = $belum['belum'] + $baca['baca'] + $balas['balas'];
				
				$result .= '	<td '. ($belum['belum'] > 0 ? 'class="text-danger"' : '') .'>'.$belum['belum'].'</td>
								<td>'.$baca['baca'].'</td>
								<td>'.$balas['balas'].'</td>
								<td><b>'.$total.'</b></td>
							</tr>';
			}
		}

		

	}

	public function suratkeluar()
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$surats = Fr_suratkeluar::
					orderBy('tgl_input', 'desc')
					->get();

		return view('pages.bpadkepegawaian.suratkeluar')
				->with('access', $access)
				->with('surats', $surats);
	}

	public function suratkeluartambah()
	{
		$this->checkSessionTime();

		$disposisis = Fr_disposisi::
						limit(200)
						->whereNotNull('kode_disposisi')
						->Where('kode_disposisi', '<>', '')
						->orderBy('no_form', 'desc')
						->get();

		$dispkodes = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		return view('pages.bpadkepegawaian.suratkeluartambah')
				->with('disposisis', $disposisis)
				->with('dispkodes', $dispkodes);
	}

	public function suratkeluarubah(Request $request)
	{
		$this->checkSessionTime();

		$surat = Fr_suratkeluar::
					where('ids', $request->ids)
					->first();

		$disposisis = Fr_disposisi::
						limit(200)
						->whereNotNull('kode_disposisi')
						->Where('kode_disposisi', '<>', '')
						->orderBy('no_form', 'desc')
						->get();

		$dispkodes = Glo_disposisi_kode::orderBy('kd_jnssurat')->get();

		return view('pages.bpadkepegawaian.suratkeluarubah')
				->with('surat', $surat)
				->with('disposisis', $disposisis)
				->with('dispkodes', $dispkodes);
	}

	public function forminsertsuratkeluar(Request $request)
	{
		$this->checkSessionTime();
		$accessid = $this->checkAccess($_SESSION['user_data']['idgroup'], 1375);

		$maxnoform = Fr_suratkeluar::max('no_form');
		if (is_null($maxnoform)) {
			$newnoform = '1.20.512.20200001';
		} else {
			$splitnoform = explode(".", $maxnoform); 
			$newnoform = $splitnoform[0] . "." . $splitnoform[1] . "." . $splitnoform[2] . "." . ($splitnoform[3]+1);
		}

		$filesuratkeluar = '';

		if (isset($request->nm_file)) {
			$file = $request->nm_file;

			if ($file->getSize() > 2222222) {
				return redirect('/kepegawaian/surat keluar tambah')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 

			$filesuratkeluar .= $file->getClientOriginalName();

			$tujuan_upload = config('app.savefilesuratkeluar');
			$file->move($tujuan_upload, $filesuratkeluar);
		}
			
		if (!(isset($filesuratkeluar))) {
			$filesuratkeluar = '';
		}

		$insertsurat = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'kd_skpd' => '1.20.512',
			'kd_unit' => '01',
			'no_form' => $newnoform,
			'tgl_terima' => (isset($request->tgl_terima) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_terima))) : null),
			'usr_input' => (isset(Auth::user()->usname) ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl_input' => date('Y-m-d H:i:s'),
			'kode_disposisi' => $request->kode_disposisi,
			'perihal' => ($request->perihal ? $request->perihal : ''),
			'tgl_surat' => ($request->tgl_surat ? $request->tgl_surat : ''),
			'no_surat' => ($request->no_surat ? $request->no_surat : ''),
			'asal_surat' => ($request->asal_surat ? $request->asal_surat : ''),
			'ket_lain' => ($request->ket_lain ? $request->ket_lain : ''),
			'nm_file' => $filesuratkeluar,
			'kepada' => ($request->kepada ? $request->kepada : ''),
			'no_form_in' => $request->no_form_in,
		];

		Fr_suratkeluar::insert($insertsurat);

		return redirect('/kepegawaian/surat keluar')
				->with('message', 'Surat Keluar berhasil dibuat')
				->with('msg_num', 1);
	}

	public function formupdatesuratkeluar(Request $request)
	{
		$this->checkSessionTime();

		$filesuratkeluar = '';

		if (isset($request->nm_file)) {
			$file = $request->nm_file;

			if ($file->getSize() > 2222222) {
				return redirect('/kepegawaian/surat keluar tambah')->with('message', 'Ukuran file terlalu besar (Maksimal 2MB)');     
			} 

			$filesuratkeluar .= $file->getClientOriginalName();

			$tujuan_upload = config('app.savefilesuratkeluar');
			$file->move($tujuan_upload, $filesuratkeluar);
		}
			
		if (!(isset($filesuratkeluar))) {
			$filesuratkeluar = '';
		}

		Fr_suratkeluar::where('ids', $request->ids)
						->update([
							'tgl_terima' => date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_terima))),
							'kode_disposisi' => $request->kode_disposisi,
							'perihal' => ($request->perihal ? $request->perihal : ''),
							'tgl_surat' => ($request->tgl_surat ? $request->tgl_surat : ''),
							'no_surat' => ($request->no_surat ? $request->no_surat : ''),
							'asal_surat' => ($request->asal_surat ? $request->asal_surat : ''),
							'ket_lain' => ($request->ket_lain ? $request->ket_lain : ''),
							'kepada' => ($request->kepada ? $request->kepada : ''),
							'no_form_in' => $request->no_form_in,
						]);
		
		if ($filesuratkeluar != '') {
			Fr_suratkeluar::where('ids', $request->ids)
			->update([
				'nm_file' => $filesuratkeluar,
			]);
		}

		return redirect('/kepegawaian/surat keluar')
					->with('message', 'Surat Keluar berhasil diubah')
					->with('msg_num', 1);
	}

	public function formdeletesuratkeluar(Request $request)
	{
		$this->checkSessionTime();
		$filepath = '';
		$filepath .= config('app.savefilesuratkeluar');
		$filepath .= '/' . $request->nm_file;

		Fr_suratkeluar::
				where('ids', $request->ids)
				->delete();


		if ($request->nm_file) {
			unlink($filepath);
		}

		return redirect('/kepegawaian/surat keluar')
					->with('message', 'Surat Keluar berhasil dihapus')
					->with('msg_num', 1);
	}

	// ---------------- SURAT KELUAR ---------------- //

	// -------------------- EKINERJA -------------------- //

	public function entrikinerja(Request $request)
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		$idemp = Auth::user()->id_emp;

		// $laporans = DB::select( DB::raw("
		// 			SELECT *
		// 			from bpadlaporan.dbo.kinerja_data
		// 			where idemp = '$idemp'
		// 			and stat is null
		// 			order by tgl_trans desc
		// 			"));

		$laporans = DB::select( DB::raw("
					SELECT *
					from bpadlaporan.dbo.v_kinerja
					where idemp = '$idemp'
					and stat is null
					order by tgl_trans desc
					"));

		$laporans = json_decode(json_encode($laporans), true);

		return view('pages.bpadkepegawaian.kinerjaentri')
				->with('access', $access)
				->with('laporans', $laporans);
	}

	public function kinerjatambah(Request $request)
	{
		$this->checkSessionTime();

		date_default_timezone_set('Asia/Jakarta');

		if ($request->now_tgl_trans) {
			$now_tgl_trans = date('d/m/Y', strtotime(str_replace('/', '-', $request->now_tgl_trans)));
		} else {
			$now_tgl_trans = date('d/m/Y', strtotime(str_replace('/', '-', now('Asia/Jakarta'))));
		}

		if ($request->now_tipe_hadir) {
			$now_tipe_hadir = $request->now_tipe_hadir;
		} else {
			$now_tipe_hadir = 1;
		}

		if ($request->now_jns_hadir) {
			$now_jns_hadir = $request->now_jns_hadir;
		} else {
			$now_jns_hadir = 'Tepat Waktu (8,5 jam/hari)';
		}

		if ($request->now_lainnya) {
			$now_lainnya = $request->now_lainnya;
		} else {
			$now_lainnya = '';
		}

		return view('pages.bpadkepegawaian.kinerjatambah')
				->with('now_tgl_trans', $now_tgl_trans)
				->with('now_tipe_hadir', $now_tipe_hadir)
				->with('now_jns_hadir', $now_jns_hadir)
				->with('now_lainnya', $now_lainnya);
	}

	public function getaktivitas()
	{
		$idemp = Auth::user()->id_emp;
		// $query = DB::select( DB::raw("
		// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
		// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
		// 			from bpadlaporan.dbo.kinerja_data a
		// 			join bpadlaporan.dbo.kinerja_detail b on b.idemp = a.idemp
		// 			where b.idemp = '$idemp' 
		// 			and a.tgl_trans = b.tgl_trans
		// 			and a.stat is null
		// 			order by b.tgl_trans desc, time1 asc
		// 			"));
		$query = DB::select( DB::raw("
					SELECT *
					from bpadlaporan.dbo.v_kinerja
					where idemp = '$idemp'
					and stat is null
					order by tgl_trans desc
					"));
		$query = json_decode(json_encode($query), true);

		return $query;
	}

	public function forminsertkinerja(Request $request)
	{
		$this->checkSessionTime();

		$idemp = Auth::user()->id_emp;
		$splittgltrans = explode("/", $request->tgl_trans);
		$tgl_trans = $splittgltrans[2] . "-" . $splittgltrans[1] . "-" . $splittgltrans[0];

		$cekkinerja = DB::select( DB::raw("
						SELECT *
						from bpadlaporan.dbo.kinerja_data
						where idemp = '$idemp'
						and tgl_trans = '$tgl_trans'
						"));
		$cekkinerja = json_decode(json_encode($cekkinerja), true);

		if ($request->tipe_hadir == 2) {
			Kinerja_detail::	
				where('idemp', $idemp)
				->where('tgl_trans', $tgl_trans)
				->delete();
		}

		if (count($cekkinerja) == 0) {
			Kinerja_data::insert([
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'idemp' => Auth::user()->id_emp,
				'tgl_trans' => $tgl_trans,
				'tipe_hadir' => $request->tipe_hadir,
				'jns_hadir' => $request->jns_hadir,
				'lainnya' => ($request->lainnya ? $request->lainnya : ''),
				'stat' => null,
				'tipe_hadir_app' => null,
				'jns_hadir_app' => null,
				'catatan_app' => null,
			]);
		} else {
			Kinerja_data::
				where('idemp', $idemp)
				->where('tgl_trans', $tgl_trans)
				->update([
					'sts' => 1,
					'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
					'tgl'       => date('Y-m-d H:i:s'),
					'ip'        => '',
					'logbuat'   => '',
					'idemp' => Auth::user()->id_emp,
					'tgl_trans' => $tgl_trans,
					'tipe_hadir' => $request->tipe_hadir,
					'jns_hadir' => $request->jns_hadir,
					'lainnya' => ($request->lainnya ? $request->lainnya : ''),
					'stat' => null,
					'tipe_hadir_app' => null,
					'jns_hadir_app' => null,
					'catatan_app' => null,
				]);
		}

		return redirect('/kepegawaian/entri kinerja');
	}

	public function formdeletekinerja(Request $request)
	{
		$this->checkSessionTime();

		Kinerja_data::	
			where('idemp', $request->idemp)
			->where('tgl_trans', $request->tgl_trans)
			->delete();

		Kinerja_detail::	
			where('idemp', $request->idemp)
			->where('tgl_trans', $request->tgl_trans)
			->delete();

		return redirect('/kepegawaian/entri kinerja')
					->with('message', 'Data kinerja berhasil dihapus')
					->with('msg_num', 1);
	}

	public function forminsertaktivitas(Request $request)
	{
		$idemp = Auth::user()->id_emp;
		$tgl_trans = $request->tgltrans;

		$cekaktivitas = DB::select( DB::raw("
						SELECT *
						from bpadlaporan.dbo.kinerja_data
						where idemp = '$idemp'
						and tgl_trans = CONVERT(datetime, '$tgl_trans')
						"));
		$cekaktivitas = json_decode(json_encode($cekaktivitas), true);

		if (count($cekaktivitas) == 0) {

			$insertkinerja = [
				'sts' => 1,
				'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
				'tgl'       => date('Y-m-d H:i:s'),
				'ip'        => '',
				'logbuat'   => '',
				'idemp' => Auth::user()->id_emp,
				'tgl_trans' => $request->tgltrans,
				'tipe_hadir' => $request->tipehadir,
				'jns_hadir' => $request->jnshadir,
				'lainnya' => ($request->lainnya ? $request->lainnya : ''),
				'stat' => null,
				'tipe_hadir_app' => null,
				'jns_hadir_app' => null,
				'catatan_app' => null,
			];
			Kinerja_data::insert($insertkinerja);
		}

		if ($request->keterangan) {
			$keterangan = $request->keterangan;
		} else {
			$keterangan = '-';
		}

		$insertaktivitas = [
			'sts' => 1,
			'uname'     => (Auth::user()->usname ? Auth::user()->usname : Auth::user()->id_emp),
			'tgl'       => date('Y-m-d H:i:s'),
			'ip'        => '',
			'logbuat'   => '',
			'idemp' => Auth::user()->id_emp,
			'tgl_trans' => $request->tgltrans,
			'time1' => $request->time1,
			'time2' => $request->time2,
			'uraian' => $request->uraian,
			'keterangan' => $keterangan,
		];

		if (Kinerja_detail::insert($insertaktivitas)) {
			// $query = DB::select( DB::raw("
			// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
			// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
			// 			from bpadlaporan.dbo.kinerja_data a
			// 			join bpadlaporan.dbo.kinerja_detail b on b.idemp = a.idemp
			// 			where b.idemp = '$idemp' 
			// 			and a.tgl_trans = b.tgl_trans
			// 			and a.stat is null
			// 			order by b.tgl_trans desc, time1 asc
			// 			"));
			$query = DB::select( DB::raw("
					SELECT *
					from bpadlaporan.dbo.v_kinerja
					where idemp = '$idemp'
					and stat is null
					order by tgl_trans desc, time1 desc
					"));
			$query = json_decode(json_encode($query), true);

			$body_append = '';
			$now_date = '';
			foreach ($query as $key => $data) {
				if ($data['tipe_hadir'] != 2 && $data['time1'] && $data['time2']) {
					$splittime1 = explode(":", $data['time1']);
					$time1 = $splittime1[0] . ":" . $splittime1[1];

					$splittime2 = explode(":", $data['time2']);
					$time2 = $splittime2[0] . ":" . $splittime2[1];

					$splitdate1 = explode(" ", $data['tgl_trans'])[0];
					$splitdate2 = explode("-", $splitdate1);
					$date = $splitdate2[2] . "-" . $splitdate2[1] . "-" . $splitdate2[0];

					if ($now_date != $data['tgl_trans']) {
						$now_date = $data['tgl_trans'];
						$body_append .= '<tr style="background-color: #f7fafc !important">
											<td colspan="5"><b>TANGGAL: '.$date.'</b></td>
										</tr>';
					}

					$body_append .= '<tr>
										<td>'.$time1.'</td>
										<td>'.$time2.'</td>
										<td>'.$data['uraian'].'</td>
										<td>'.$data['keterangan'].'</td>
										<td>
											<input id="idemp-'.$key.'" type="hidden" value="'.$idemp.'"></input>
											<input id="tgl_trans-'.$key.'" type="hidden" value="'.$data['tgl_trans'].'"></input>
											<input id="time1-'.$key.'" type="hidden" value="'.$data['time1'].'"></input>
											<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn_delete_aktivitas" id="'.$key.'"><i class="fa fa-trash"></i></button></td>
										</td>
									</tr>';
				}
			}
			return json_encode($body_append);
		} else {
			return 0;
		}
	}

	public function formdeleteaktivitas(Request $request)
	{
		$idemp = $request->idemp;
		$tgl_trans = $request->tgltrans;
		$time1 = $request->time1;

		Kinerja_detail::	
			where('idemp', $idemp)
			->where('tgl_trans', $tgl_trans)
			->where('time1', $time1)
			->delete();

		// $query = DB::select( DB::raw("
		// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
		// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
		// 			from bpadlaporan.dbo.kinerja_data a
		// 			join bpadlaporan.dbo.kinerja_detail b on b.idemp = a.idemp
		// 			where b.idemp = '$idemp' 
		// 			and a.tgl_trans = b.tgl_trans
		// 			and a.stat is null
		// 			order by b.tgl_trans desc, time1 asc
		// 			"));
		$query = DB::select( DB::raw("
					SELECT *
					from bpadlaporan.dbo.v_kinerja
					where idemp = '$idemp'
					and stat is null
					"));
		$query = json_decode(json_encode($query), true);

		$body_append = '';
		$now_date = '';
		foreach ($query as $key => $data) {
			if ($data['tipe_hadir'] != 2 && $data['time1'] && $data['time2']) {
				$splittime1 = explode(":", $data['time1']);
				$time1 = $splittime1[0] . ":" . $splittime1[1];

				$splittime2 = explode(":", $data['time2']);
				$time2 = $splittime2[0] . ":" . $splittime2[1];

				$splitdate1 = explode(" ", $data['tgl_trans'])[0];
				$splitdate2 = explode("-", $splitdate1);
				$date = $splitdate2[2] . "-" . $splitdate2[1] . "-" . $splitdate2[0];

				if ($now_date != $data['tgl_trans']) {
					$now_date = $data['tgl_trans'];
					$body_append .= '<tr style="background-color: #f7fafc !important">
										<td colspan="5"><b>TANGGAL: '.$date.'</b></td>
									</tr>';
				}

				$body_append .= '<tr>
									<td>'.$time1.'</td>
									<td>'.$time2.'</td>
									<td>'.$data['uraian'].'</td>
									<td>'.$data['keterangan'].'</td>
									<td>
										<input id="idemp-'.$key.'" type="hidden" value="'.$idemp.'"></input>
										<input id="tgl_trans-'.$key.'" type="hidden" value="'.$data['tgl_trans'].'"></input>
										<input id="time1-'.$key.'" type="hidden" value="'.$data['time1'].'"></input>
										<button type="button" class="btn btn-danger btn-outline btn-circle m-r-5 btn_delete_aktivitas" id="'.$key.'"><i class="fa fa-trash"></i></button></td>
									</td>
								</tr>';
			}
		}
		return json_encode($body_append);
	}

	public function approvekinerja(Request $request)
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($_SESSION['user_data']['idunit']) {
			$idunit = $_SESSION['user_data']['idunit'];
		} else {
			$idunit = '01';
		}

		$pegawais = DB::select( DB::raw("
					SELECT id_emp, nm_emp, nrk_emp FROM bpadlaporan.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpadlaporan.dbo.emp_gol,bpadlaporan.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpadlaporan.dbo.emp_dik,bpadlaporan.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and ked_emp = 'aktif' and tgl_end is null and tbunit.sao like '$idunit%'
					order by nm_emp"));
		$pegawais = json_decode(json_encode($pegawais), true);

		if ($request->now_id_emp) {
			//kalo ada input milih pegawai
			$now_id_emp = $request->now_id_emp;
		} else {
			// kalo gada milih pegawai
			if (Auth::user()->usname) {
				// kalo yg login admin -> ambil id_emp pertama
				$now_id_emp = $pegawais[0]['id_emp'];
			} elseif (Auth::user()->id_emp) {
				// kalo yg login pegawai
				if (strlen($_SESSION['user_data']['idunit']) == 10) {
					// set id_emp sekarang = id_emp pegawai yg login
					$now_id_emp = Auth::user()->id_emp;
				} else {
					// set id_emp sekarang = id_emp pertama dari query list pegawai
					$now_id_emp = $pegawais[0]['id_emp'];
				}
			}
		}

		$laporans = DB::select( DB::raw("
					SELECT kinerja_data.*, emp_data.nm_emp					
					from bpadlaporan.dbo.kinerja_data
					join bpadlaporan.dbo.emp_data on emp_data.id_emp = kinerja_data.idemp
					where idemp = '$now_id_emp'
					and stat is null
					order by tgl_trans desc, nm_emp asc
					"));

		$laporans = json_decode(json_encode($laporans), true);

		return view('pages.bpadkepegawaian.kinerjaapprove')
				->with('access', $access)
				->with('laporans', $laporans)
				->with('pegawais', $pegawais)
				->with('now_id_emp', $now_id_emp);
	}

	public function formapprovekinerja(Request $request)
	{
		$this->checkSessionTime();

		foreach ($request->laporan as $key => $data) {
			$idemp = $request->{'idemp_'.$data};
			Kinerja_data::
				where('idemp', $request->{'idemp_'.$data})
				->where('tgl_trans', $request->{'tgl_trans_'.$data})
				->update([
					'stat' => 1,
					'tipe_hadir_app' => $request->{'tipe_hadir_'.$data},
					'jns_hadir_app' => $request->{'jns_hadir_'.$data},
					'catatan_app' => '',
				]);
		}

		return redirect('/kepegawaian/approve kinerja?now_id_emp='.$idemp)
					->with('message', 'Data kinerja berhasil disetujui')
					->with('msg_num', 1);
	}

	public function getdetailaktivitas(Request $request)
	{
		$idemp = $request->idemp;
		$tgl_trans = $request->tgl_trans;
		// $query = DB::select( DB::raw("
		// 			SELECT a.sts as data_sts, a.tgl as data_tgl, a.idemp as data_idemp, a.tgl_trans as data_tgl_trans, tipe_hadir, jns_hadir, lainnya, stat, tipe_hadir_app, jns_hadir_app, catatan_app,
		// 					b.sts as detail_sts, b.tgl as detail_sts, b.idemp as detail_idemp, b.tgl_trans as detail_tgl_trans, time1, time2, uraian, keterangan
		// 			from bpadlaporan.dbo.kinerja_data a
		// 			join bpadlaporan.dbo.kinerja_detail b on b.idemp = a.idemp
		// 			where b.idemp = '$idemp' 
		// 			and a.tgl_trans = b.tgl_trans
		// 			and a.stat is null
		// 			order by b.tgl_trans desc, time1 asc
		// 			"));
		$query = DB::select( DB::raw("
					SELECT *
					from bpadlaporan.dbo.kinerja_detail
					where idemp = '$idemp'
					and tgl_trans = '$tgl_trans'
					order by time1
					"));
		$query = json_decode(json_encode($query), true);

		return $query;
	}

	public function formapprovekinerjasingle(Request $request)
	{
		$this->checkSessionTime();

		$tgl_trans = date('Y-m-d',strtotime($request->tgl_trans));

		if ($request->catatan_app) {
			$catatan_app = $request->catatan_app;
		} else {
			$catatan_app = '-';
		}

		if ($request->lainnya) {
			Kinerja_data::
			where('idemp', $request->idemp)
			->where('tgl_trans', $tgl_trans)
			->update([
				'stat' => 1,
				'lainnya' => $request->lainnya,
				'tipe_hadir_app' => $request->tipe_hadir,
				'jns_hadir_app' => $request->jns_hadir,
				'catatan_app' => $catatan_app,
			]);
		} else {
			Kinerja_data::
			where('idemp', $request->idemp)
			->where('tgl_trans', $tgl_trans)
			->update([
				'stat' => 1,
				'lainnya' => '',
				'tipe_hadir_app' => $request->tipe_hadir,
				'jns_hadir_app' => $request->jns_hadir,
				'catatan_app' => $catatan_app,
			]);
		}

		return redirect('/kepegawaian/approve kinerja?now_id_emp='.$request->idemp)
					->with('message', 'Data kinerja berhasil disetujui')
					->with('msg_num', 1);
	}

	public function printexcel(Request $request)
	{
		$now_id = $request->id;
		$now_month = $request->month;
		$now_year = $request->year;
		$now_valid = $request->valid;

		$monthName = date('F', mktime(0, 0, 0, $now_month, 10)); // March

		$now_emp = DB::select( DB::raw("  
				SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.sao, tbunit.notes from bpadlaporan.dbo.emp_data as a
				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
				CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
				,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
				and id_emp like '$now_id' AND ked_emp = 'AKTIF'") )[0];
		$now_emp = json_decode(json_encode($now_emp), true);

		$sao_es4 = $now_emp['sao'];
		$now_es4 = DB::select( DB::raw("  
				SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.sao, tbunit.notes from bpadlaporan.dbo.emp_data as a
				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
				CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
				,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
				and idunit like '$sao_es4' AND ked_emp = 'AKTIF'") )[0];
		$now_es4 = json_decode(json_encode($now_es4), true);

		$sao_es3 = $now_es4['sao'];
		$now_es3 = DB::select( DB::raw("  
				SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.child, tbunit.nm_unit, tbunit.sao, tbunit.notes from bpadlaporan.dbo.emp_data as a
				CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
				CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
				,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
				and idunit like '$sao_es3' AND ked_emp = 'AKTIF'") )[0];
		$now_es3 = json_decode(json_encode($now_es3), true);

		$laporans = DB::select( DB::raw("
					SELECT *
					from bpadlaporan.dbo.v_kinerja
					where idemp = '$now_id'
					and stat $now_valid
					and YEAR(tgl_trans) = $now_year
					and MONTH(tgl_trans) = $now_month
					"));
		$laporans = json_decode(json_encode($laporans), true);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A1:E1');
		$sheet->setCellValue('A1', 'LAPORAN KINERJA');
		$sheet->getStyle('A1')->getFont()->setBold( true );
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A2:E2');
		$sheet->setCellValue('A2', 'TENAGA AHLI '.$now_emp['nm_unit'].' BADAN PENGELOLAAN ASET DAERAH');
		$sheet->getStyle('A2')->getFont()->setBold( true );
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A3:E3');
		$sheet->setCellValue('A3', 'BADAN PENGELOLAAN ASET DAERAH (BPAD) PROVINSI DKI JAKARTA '.date('Y'));
		$sheet->getStyle('A3')->getFont()->setBold( true );
		$sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

		$sheet->setCellValue('A5', 'Nama');
		$sheet->setCellValue('A6', 'Tempat Tugas');
		$sheet->setCellValue('A7', 'Periode');

		$sheet->setCellValue('C5', ': '.ucwords(strtolower($now_emp['nm_emp'])));
		$sheet->getStyle('C5')->getFont()->setBold( true );
		$sheet->setCellValue('C6', ': '.ucwords(strtolower($now_emp['nm_unit'])) . ' BPAD Provinsi DKI Jakarta');
		$sheet->setCellValue('C7', ': '.$monthName .' '. $now_year);		
		
		$styleArray = [
		    'font' => [
		        'size' => 12,
		        'name' => 'Trebuchet MS',
		    ]
		];

		$sheet->getStyle('A1:E7')->applyFromArray($styleArray);

		$sheet->setCellValue('A9', 'TANGGAL');
		$sheet->setCellValue('B9', 'AWAL');
		$sheet->setCellValue('C9', 'AKHIR');
		$sheet->setCellValue('D9', 'URAIAN');
		$sheet->setCellValue('E9', 'KETERANGAN');

		$sheet->getStyle('A9')->getFont()->setBold( true );
		$sheet->getStyle('B9')->getFont()->setBold( true );
		$sheet->getStyle('C9')->getFont()->setBold( true );
		$sheet->getStyle('D9')->getFont()->setBold( true );
		$sheet->getStyle('E9')->getFont()->setBold( true );

		$sheet->getStyle('A9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('B9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('C9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('D9')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('E9')->getAlignment()->setHorizontal('center');

		$nowdate = 0;
		$nowrow = 10;
		$rowstart = $nowrow - 1;
		foreach ($laporans as $key => $laporan) {
			if ($nowdate != $laporan['tgl_trans']) {
				$nowdate = $laporan['tgl_trans'];

				if ($now_valid == "= 1") {
					$jns_hadir = $laporan['jns_hadir_app'];
				} else {
					$jns_hadir = $laporan['jns_hadir'];
				}

				if ($laporan['jns_hadir_app'] == 'Lainnya (sebutkan)' || $laporan['jns_hadir'] == 'Lainnya (sebutkan)') {
					$lainnya = " --- " . $laporan['lainnya'];
				} else {
					$lainnya = "";
				}

				$sheet->mergeCells("A".$nowrow.":E".$nowrow);
				$sheet->setCellValue("A".$nowrow, date('D, d-M-Y',strtotime($laporan['tgl_trans'])) . " --- " . $jns_hadir);
				$sheet->getStyle('A'.$nowrow)->getFont()->setBold( true );

				$nowrow++;
			}

			if ($now_valid == "= 1") {
				if ($laporan['tipe_hadir_app'] != 2) {
					$sheet->setCellValue('A'.$nowrow, date('d-M-Y',strtotime($laporan['tgl_trans'])));
					$sheet->setCellValue('B'.$nowrow, date('H:i',strtotime($laporan['time1'])));
					$sheet->setCellValue('C'.$nowrow, date('H:i',strtotime($laporan['time2'])));
					$sheet->setCellValue('D'.$nowrow, $laporan['uraian']);
					$sheet->setCellValue('E'.$nowrow, $laporan['keterangan']);
					$nowrow++;
				}
			} else {
				if ($laporan['tipe_hadir'] != 2) {
					$sheet->setCellValue('A'.$nowrow, date('d-M-Y',strtotime($laporan['tgl_trans'])));
					$sheet->setCellValue('B'.$nowrow, date('H:i',strtotime($laporan['time1'])));
					$sheet->setCellValue('C'.$nowrow, date('H:i',strtotime($laporan['time2'])));
					$sheet->setCellValue('D'.$nowrow, $laporan['uraian']);
					$sheet->setCellValue('E'.$nowrow, $laporan['keterangan']);
					$nowrow++;
				}
			}
		}

		$rowend = $nowrow - 1;

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(8);
		$sheet->getColumnDimension('C')->setWidth(8);
		$sheet->getColumnDimension('D')->setWidth(55);
		$sheet->getColumnDimension('E')->setWidth(30);

		$styleArray = [
		    'borders' => [
		        'allBorders' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		];

		$sheet->getStyle('A'.$rowstart.':E'.$rowend)->applyFromArray($styleArray);

		$nowrow++;
		$sheet->setCellValue('E'.$nowrow, 'Jakarta, _________');

		$nowrow++;
		$rownext = $nowrow + 1;
		$sheet->mergeCells('A'.$nowrow.':C'.$rownext);
		$sheet->setCellValue('A'.$nowrow, strtoupper($now_es4['notes']));
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setVertical('center');

		$nowrow++;
		$sheet->setCellValue('E'.$nowrow, 'TENAGA PENDAMPING');
		$sheet->getStyle('E'.$nowrow)->getAlignment()->setHorizontal('center');

		$nowrow = $nowrow + 4;
		$sheet->setCellValue('D'.$nowrow, 'Mengetahui:');
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');

		$nowrow++;
		$sheet->mergeCells('A'.$nowrow.':C'.$nowrow);
		$sheet->setCellValue('A'.$nowrow, strtoupper($now_es4['nm_emp']));
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A'.$nowrow)->getFont()->setBold( true );
		//-----//
		$rownext = $nowrow + 1;
		$sheet->mergeCells('D'.$nowrow.':D'.$rownext);
		$sheet->setCellValue('D'.$nowrow, strtoupper($now_es3['notes']));
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setVertical('center');
		//-----//
		$sheet->setCellValue('E'.$nowrow, strtoupper($now_emp['nm_emp']));
		$sheet->getStyle('E'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('E'.$nowrow)->getFont()->setBold( true );

		$nowrow++;
		$sheet->setCellValue('A'.$nowrow, 'NIP. '.$now_es4['nip_emp']);
		$sheet->mergeCells('A'.$nowrow.':C'.$nowrow);
		$sheet->getStyle('A'.$nowrow)->getAlignment()->setHorizontal('center');

		$nowrow = $nowrow + 4;
		$sheet->setCellValue('D'.$nowrow, strtoupper($now_es3['nm_emp']));
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setVertical('center');

		$nowrow++;
		$sheet->setCellValue('D'.$nowrow, 'NIP. '.$now_es3['nip_emp']);
		$sheet->getStyle('D'.$nowrow)->getAlignment()->setHorizontal('center');

		$styleArray = [
		    'font' => [
		        'size' => 12,
		        'name' => 'Trebuchet MS',
		    ]
		];
		$sheet->getStyle('A'.($rowend+1).':E'.$nowrow)->applyFromArray($styleArray);

		$filename = 'EKinerja_'.$now_id.'_'.$monthName.$now_year.'.xlsx';

		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	public function printexcelpegawai(Request $request)
	{
		$idunit = $request->unit;
		$kednow = $request->ked;

		$employees = DB::select( DB::raw("  
					SELECT id_emp, nrk_emp, nip_emp, nm_emp, a.idgroup as idgroup, tgl_lahir, jnkel_emp, tgl_join, status_emp, tbjab.idjab, tbjab.idunit, tbunit.nm_unit, tbunit.notes, tbunit.child, d.nm_lok from bpadlaporan.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpadlaporan.dbo.emp_gol,bpadlaporan.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpadlaporan.dbo.emp_dik,bpadlaporan.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and idunit like '$idunit%' AND ked_emp = '$kednow'
					order by idunit asc, nm_emp ASC") );
		$employees = json_decode(json_encode($employees), true);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->mergeCells('A1:I1');
		$sheet->setCellValue('A1', 'DATA PEGAWAI');
		$sheet->getStyle('A1')->getFont()->setBold( true );
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A2:I2');
		$sheet->setCellValue('A2', 'BADAN PENGELOLAAN ASET DAERAH');
		$sheet->getStyle('A2')->getFont()->setBold( true );
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A3:I3');
		$sheet->setCellValue('A3', 'PROVINSI DKI JAKARTA '.date('Y'));
		$sheet->getStyle('A3')->getFont()->setBold( true );
		$sheet->getStyle('A3')->getAlignment()->setHorizontal('center');	

		$styleArray = [
		    'font' => [
		        'size' => 12,
		        'name' => 'Trebuchet MS',
		    ]
		];
		$sheet->getStyle('A1:I5')->applyFromArray($styleArray);

		$sheet->setCellValue('A5', 'NO');
		$sheet->setCellValue('B5', 'ID');
		$sheet->setCellValue('C5', 'NIP');
		$sheet->setCellValue('D5', 'NRK');
		$sheet->setCellValue('E5', 'NAMA');
		$sheet->setCellValue('F5', 'UNIT');
		$sheet->setCellValue('G5', 'LOKASI');
		$sheet->setCellValue('H5', 'TGL LAHIR');
		$sheet->setCellValue('I5', 'STATUS');

		$sheet->getStyle('A5:I5')->getFont()->setBold( true );

		$sheet->getStyle('A5:I5')->getAlignment()->setHorizontal('center');

		$nowrow = 6;
		$rowstart = $nowrow - 1;
		foreach ($employees as $key => $employee) {
			$sheet->setCellValue('A'.$nowrow, $key+1);
			$sheet->setCellValue('B'.$nowrow, $employee['id_emp']);
			$sheet->setCellValue('C'.$nowrow, $employee['nip_emp'] ? '\''.$employee['nip_emp'] : '-' );
			$sheet->setCellValue('D'.$nowrow, $employee['nrk_emp'] ? $employee['nrk_emp'] : '-' );
			$sheet->setCellValue('E'.$nowrow, strtoupper($employee['nm_emp']));
			$sheet->setCellValue('F'.$nowrow, strtoupper($employee['nm_unit']));
			$sheet->setCellValue('G'.$nowrow, $employee['nm_lok']);
			$sheet->setCellValue('H'.$nowrow, date('d-M-Y', strtotime($employee['tgl_lahir'])));
			$sheet->setCellValue('I'.$nowrow, $employee['status_emp']);

			if (strlen($employee['idunit']) < 10) {
				$sheet->getStyle('A'.$nowrow.':I'.$nowrow)->getFont()->setBold( true );
			}

			$nowrow++;
		}

		$rowend = $nowrow - 1;

		$filename = 'Pegawai'.date('Y').'.xlsx';

		// Redirect output to a client's web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		 
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	public function laporankinerja(Request $request)
	{
		$this->checkSessionTime();
		$currentpath = str_replace("%20", " ", $_SERVER['REQUEST_URI']);
		$currentpath = explode("?", $currentpath)[0];
		$thismenu = Sec_menu::where('urlnew', $currentpath)->first('ids');
		$access = $this->checkAccess($_SESSION['user_data']['idgroup'], $thismenu['ids']);

		if ($_SESSION['user_data']['idunit']) {
			$idunit = $_SESSION['user_data']['idunit'];
		} else {
			$idunit = '01';
		}

		$pegawais = DB::select( DB::raw("
					SELECT id_emp, nm_emp, nrk_emp FROM bpadlaporan.dbo.emp_data as a
					CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpadlaporan.dbo.emp_gol,bpadlaporan.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
					CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpadlaporan.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
					CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpadlaporan.dbo.emp_dik,bpadlaporan.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
					CROSS APPLY (SELECT TOP 1 * FROM bpadlaporan.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
					,bpadlaporan.dbo.glo_skpd as b,bpadlaporan.dbo.glo_org_unitkerja as c,bpadlaporan.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1'
					and ked_emp = 'aktif' and tgl_end is null and tbunit.sao like '$idunit%'
					order by nm_emp"));
		$pegawais = json_decode(json_encode($pegawais), true);

		if ($request->now_id_emp) {
			//kalo ada input milih pegawai
			$now_id_emp = $request->now_id_emp;
		} else {
			// kalo gada milih pegawai
			if (Auth::user()->usname) {
				// kalo yg login admin -> ambil id_emp pertama
				$now_id_emp = $pegawais[0]['id_emp'];
			} elseif (Auth::user()->id_emp) {
				// kalo yg login pegawai
				if (strlen($_SESSION['user_data']['idunit']) == 10) {
					// set id_emp sekarang = id_emp pegawai yg login
					$now_id_emp = Auth::user()->id_emp;
				} else {
					// set id_emp sekarang = id_emp pertama dari query list pegawai
					$now_id_emp = $pegawais[0]['id_emp'];
				}
			}
		}

		if ($request->now_month) {
			$now_month = (int)$request->now_month;
		} else {
			$now_month = (int)date('m');
		}

		if ($request->now_year) {
			$now_year = (int)$request->now_year;
		} else {
			$now_year = (int)date('Y');
		}

		if ($request->now_valid) {
			$now_valid = $request->now_valid;
		} else {
			$now_valid = "= 1";
		}

		$laporans = DB::select( DB::raw("
					SELECT *
					from bpadlaporan.dbo.v_kinerja
					where idemp = '$now_id_emp'
					and stat $now_valid
					and YEAR(tgl_trans) = $now_year
					and MONTH(tgl_trans) = $now_month
					"));
		$laporans = json_decode(json_encode($laporans), true);

		return view('pages.bpadkepegawaian.kinerjalaporan')
				->with('access', $access)
				->with('pegawais', $pegawais)
				->with('now_id_emp', $now_id_emp)
				->with('now_month', $now_month)
				->with('now_year', $now_year)
				->with('now_valid', $now_valid)
				->with('laporans', $laporans);
	}

	// -------------------- EKINERJA -------------------- //
}