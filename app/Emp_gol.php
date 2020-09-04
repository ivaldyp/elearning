<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emp_gol extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "emp_gol";
    
    public $incrementing = 'false';
    public $timestamps = false;

    public function gol()
    {
        return $this->hasOne('App\Glo_org_golongan', 'gol', 'idgol')->orderBy('gol');
    }
}
