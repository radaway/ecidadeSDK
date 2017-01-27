<?php
require_once __DIR__ . '/DockerRun.class.php';

$docker = new DockerRun( "ubuntu:16.04" );
$docker->addVolume( "/var/www/builds", "/var/www/html" );
$docker->bindPort( "20000", "80" );
$docker->addDns( "8.8.8.8" );
$docker->addDns( "8.8.4.4" );
$docker->addDnsSearch( "local" );
$docker->addCmd( "/bin/bash" );
$docker->dockerRun();

 ?>
