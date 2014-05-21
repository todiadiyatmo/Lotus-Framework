<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class PostMeta extends Eloquent{

	public $timestamps = false;

	protected $table = "postmeta";

	protected $primaryKey = 'meta_id';

}