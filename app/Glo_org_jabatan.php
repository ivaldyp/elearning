<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_org_jabatan extends Model
{
    protected $connection = 'sqlsrv';
    // protected $primaryKey = "id_emp"; 
    protected $table = "glo_org_jabatan";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
