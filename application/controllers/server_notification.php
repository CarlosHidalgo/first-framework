<?php

use App\Configuration;
include_once realpath(__DIR__.'/../fw/LoaderClass.php');

Configuration::init();

$obj = new \Controllers\NotificationServer();
$obj->start();

?>