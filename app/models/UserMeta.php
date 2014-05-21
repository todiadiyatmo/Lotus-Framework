<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserMeta extends Eloquent{

	public $timestamps = false;

	protected $table = "usermeta";

	protected $primaryKey = 'umeta_id';

}