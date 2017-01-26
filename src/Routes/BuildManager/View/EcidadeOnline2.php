<?php

class EcidadeOnline2{
  private $buildName;
  public function __construct( $buildName, $Method  ){
    $this->buildName = $buildName;
    echo $this->getInfo();
  }

  private function getInfo(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/EcidadeOnline2_ports.conf');
    return 'http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort;
  }

}

 ?>
