<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emp_jab extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "emp_jab";
    
    public $incrementing = 'false';
    public $timestamps = false;

    public function jabatan()
    {
        return $this->hasOne('App\Glo_org_jabatan', 'jabatan', 'idjab');
    }

    public function lokasi()
    {
        return $this->hasOne('App\Glo_org_lokasi', 'kd_lok', 'idlok');
    }

    public function unit()
    {
        return $this->hasOne('App\Glo_org_unitkerja', 'kd_unit', 'idunit');
    }
}
