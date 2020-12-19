<?php

phpinfo();
$dbh = new PDO('mysql:dbname=catalogue;host=host.docker.internal','root','my-secret-pw');
var_dump($dbh);
