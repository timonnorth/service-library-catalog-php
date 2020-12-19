<?php

use \Phpmig\Adapter;

$container = new ArrayObject();

$dbh = new PDO('mysql:dbname=catalogue;host=host.docker.internal','root','my-secret-pw');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$container['db'] = $dbh;

$container['phpmig.adapter'] = new Adapter\PDO\Sql($dbh, 'migrations');

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

return $container;
