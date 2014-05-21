<?php

use Illuminate\Database\Capsule\Manager as Capsule;  

$capsule = new Capsule;

$capsule->addConnection(array(
	'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'glints',
    'username'  => 'root',
    'password'  => '',
    'prefix'    => 'wp_',
    'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
));

$capsule->bootEloquent();

