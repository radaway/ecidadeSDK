<?php

class Ecidade{
  private $buildName;
  public function __construct( $buildName, $Method  ){
    $this->buildName = $buildName;
    echo $this->getInfo();
  }

  private function getInfo(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf');
    return '<br/>http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '<br/>';
  }
}

 ?>
