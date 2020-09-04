<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dat_ttd extends Model
{
    protected $connection = 'sqlsrv';
    // protected $primaryKey = "id_emp"; 
    protected $table = "dat_ttd";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
