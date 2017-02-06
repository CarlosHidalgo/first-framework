<?php

include_once realpath(__DIR__.'/../fw/LoaderClass.php');

\App\Configuration::init();

$obj = new \Controllers\AuctionServerWS(\App\Router::getDomain(), \App\Configuration::getGeneralConfigs()['webSocketPort']);
echo \App\Router::getDomain();
$obj->start();

?>