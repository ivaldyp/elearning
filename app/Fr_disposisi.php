<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fr_disposisi extends Model
{
	protected $connection = 'sqlsrv';
	// protected $primaryKey = "ids"; 
	protected $table = "fr_disposisi";
	
	// public $incrementing = 'false';
	public $timestamps = false;
}
