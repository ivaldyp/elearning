<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_org_statusemp extends Model
{
    protected $connection = 'sqlsrv';
    // protected $primaryKey = "id_emp"; 
    protected $table = "glo_org_statusemp";
    
    public $incrementing = 'false';
    public $timestamps = false;//
}
