<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dat_laporan extends Model
{
    protected $connection = 'sqlsrv';
    // protected $primaryKey = "id_emp"; 
    protected $table = "dat_laporan";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
