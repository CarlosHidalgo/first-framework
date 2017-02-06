<?php

// +-----------------------------------------------------------------------+
// PSR4 Namespace
// +-----------------------------------------------------------------------+
$directory = __DIR__; 
include_once $directory.'/Psr4AutoloaderClass.php';

$loader = new Psr4AutoloaderClass();

$loader->register();
$loader->addNamespace('App\\', $directory);
$loader->addNamespace('App\\', realpath($directory.'/routing/') );
$loader->addNamespace('App\\', realpath($directory.'/database/') );
$loader->addNamespace('App\\', realpath($directory.'/template/') );
$loader->addNamespace('App\\', realpath($directory.'/log/') );
$loader->addNamespace('Security\\', realpath($directory.'/security/') );
$loader->addNamespace('Exceptions\\', realpath($directory.'/exceptions/') );
$loader->addNamespace('Socket\\', realpath($directory.'/socket/') );
$loader->addNamespace('Controllers\\', realpath($directory.'/../controllers/') );
$loader->addNamespace('Models\\', realpath($directory.'/../models/') );
$loader->addNamespace('Views\\', realpath($directory.'/../views/') );
$loader->addNamespace('Views\\Core', realpath($directory.'/../views/core/') );
$loader->addNamespace('Views\\Users', realpath($directory.'/../views/users/') );
$loader->addNamespace('Views\\Auctions', realpath($directory.'/../views/auctions/') );
$loader->addNamespace('Views\\Products', realpath($directory.'/../views/products/') );
$loader->addNamespace('Namshi\\JOSE\\', realpath($directory.'/../lib/Namshi/JOSE/') );
$loader->addNamespace('JsonMapper\\', realpath($directory.'/../lib/JsonMapper/') );

?>