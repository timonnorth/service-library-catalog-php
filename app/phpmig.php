<?php

use \Phpmig\Adapter;

$container = new ArrayObject();

$dbh = new PDO(
    sprintf('mysql:dbname=%s;host=%s', getenv('MYSQL_DBNAME'), getenv('MYSQL_HOST')),
    getenv('MYSQL_USER'),
    getenv('MYSQL_PASSWORD')
);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$container['db'] = $dbh;

$container['phpmig.adapter'] = new Adapter\PDO\Sql($dbh, 'migrations');

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

return $container;
