<?php
require_once __DIR__ . '/../../../Docker/class/Docker.class.php';

class Ecidade{

  private $buildName;

  public function __construct( $buildName, $Method ){
    $this->buildName = $buildName;
    echo $this->$Method();
  }

  private function dockerStop(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf' );
    $dockerPort = trim( $dockerPort );
    try {
      Docker::stop( $dockerPort );
    } catch (Exception $e) {
      return $e->getMessage();
    }
    $msg = 'Serviço docker parado';
    return $msg;
  }

  private function dockerStart(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf');
    $dockerPort = trim( $dockerPort );
    //try {
      Docker::start( 'apache_ecidade', '/var/www/builds/' . $this->buildName . '/builds/Ecidade', $dockerPort );
    //} catch (Exception $e) {
      //return $e->getMessage();
    //}
    $msg = 'Serviço docker iniciado';
    return $msg;
  }
}
?>
