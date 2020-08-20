<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dat_materi extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "dat_materi";
    public $timestamps = false;
}
