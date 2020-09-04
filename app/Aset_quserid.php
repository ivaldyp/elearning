<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aset_quserid extends Model
{
    protected $connection = 'sqlsrv3';
    // protected $primaryKey = "id_emp"; 
    protected $table = "aset_quserid";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
