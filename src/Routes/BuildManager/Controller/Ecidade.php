<?php
require_once __DIR__ . '/../../../Docker/class/DockerRun.class.php';
require_once __DIR__ . '/../../../Docker/class/DockerList.class.php';
require_once __DIR__ . '/../../../Docker/class/DockerStop.class.php';
class Ecidade{

  private $buildName;

  public function __construct( $buildName, $Method ){
    $this->buildName = $buildName;
    echo $this->$Method();
  }

  private function dockerStop(){
    $dockerL = new DockerList();
    $dockerId = $dockerL->getDockerByDir( '/var/www/builds/' . $this->buildName . '/builds/Ecidade' );
    if ( $dockerId != null ){
      return true;
    }

    $dockerS = new DockerStop();
    $dockerS->killById( $dockerId );
    $dockerS->deleteById( $dockerId );
    $msg = "Serviço parado";
    return $msg;
  }

  private function dockerStart(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf');
    $dockerPort = trim( $dockerPort );
    $docker = new DockerRun( "apache_ecidade" );
    $docker->addVolume( '/var/www/builds/' . $this->buildName . '/builds/Ecidade', "/var/www/html" );
    $docker->bindPort( $dockerPort, "80" );
    $docker->addDns( "8.8.8.8" );
    $docker->addDns( "8.8.4.4" );
    $docker->addDnsSearch( "local" );
    $docker->addCmd( "/root/scripts/start.sh" );
    $doc_id = $docker->dockerRun();
    $msg = 'Serviço docker iniciado ' . $doc_id;
    return $msg;
  }
}
?>
