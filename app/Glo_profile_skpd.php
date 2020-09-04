<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_profile_skpd extends Model
{
    protected $connection = 'sqlsrv3';
    // protected $primaryKey = "id_emp"; 
    protected $table = "glo_profile_skpd";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
