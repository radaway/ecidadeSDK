<?php
require_once __DIR__ . '/../../../Docker/class/Docker.class.php';
class Ecidade{

  private $buildName;

  public function __construct( $buildName, $Method ){
    $this->buildName = $buildName;
    echo $this->buildName;
    //switch ($Method) {
      //case 'dockerStop':
        //echo $this->dockerStop();
        //break;
      //case 'dockerStart':
      //  echo $this->dockerStart();
        //break;
      //default:
      //  echo "bugou";
      //  break;
  //  }
  }

  private function dockerStop(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf');
    $dockerPort = trim( $dockerPort );
    try {
      $docker = new Docker();
      $docker->stop( $dockerPort );
    } catch (Exception $e) {
      return $e->getMessage();
    }
    return 'Serviço docker parado';
  }

  private function dockerStart(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf');
    $dockerPort = trim( $dockerPort );
    try {
      $docker = new Docker();
      $docker->start( 'apache_ecidade', '/var/www/builds/' . $this->buildName . '/builds/Ecidade', $dockerPort );
    } catch (Exception $e) {
      return $e->getMessage();
    }
    return 'Serviço docker iniciado';
  }
}
?>
