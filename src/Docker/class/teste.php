<?php
require_once __DIR__ . '/DockerList.class.php';

$docker = new DockerList();
$saida = $docker->getDockerByDir( '/var/www/builds' );
print_r( $saida );

 ?>
