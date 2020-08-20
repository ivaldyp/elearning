<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sec_student extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "sec_student";
    public $timestamps = false;
}
