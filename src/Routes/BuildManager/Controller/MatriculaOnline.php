<?php
require_once __DIR__ . '/../../../Docker/class/DockerJsonCreate.class.php';
require_once __DIR__ . '/../../../Docker/class/DockerRun.class.php';
require_once __DIR__ . '/../../../Docker/class/DockerList.class.php';
require_once __DIR__ . '/../../../Docker/class/DockerStop.class.php';
class MatriculaOnline{

  private $buildName;

  public function __construct( $buildName, $Method ){
    $this->buildName = $buildName;
    echo $this->$Method();
  }

  private function dockerStop(){
    $msg = "Serviço docker encerrado!";
    try {
      $dockerL = new DockerList();
      $dockerId = $dockerL->getDockerByDir( '/var/www/builds/' . $this->buildName . '/builds/MatriculaOnline' );
      if ( $dockerId == null ){
        return $msg;
      }
      $dockerS = new DockerStop();
      $dockerS->killById( $dockerId );
      $dockerS->deleteById( $dockerId );
    } catch (Exception $e) {
      $msg  = "Falha ao encerrar docker!" . $e->getMessage();
    }
    return $msg;
  }

  private function dockerStart(){
    $msg = 'Serviço docker iniciado!';
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/MatriculaOnline_ports.conf');
    $dockerPort = trim( $dockerPort );
    if( $dockerPort == '' ){
      $msg = 'Falha ao configurar porta do serviço!';
      return $msg;
    }
    try {
      $dockerL = new DockerList();
      $dockerId = $dockerL->getDockerByDir( '/var/www/builds/' . $this->buildName . '/builds/MatriculaOnline' );
      if ( $dockerId != null ){
        $msg = "Docker já está em execução";
        return $msg;
      }
      $dockerJson = new DockerJsonCreate( "apache_MatriculaOnline" );
      $dockerJson->addVolume( '/var/www/builds/' . $this->buildName . '/builds/MatriculaOnline', "/var/www/html" );
      $dockerJson->bindPort( $dockerPort, "80" );
      $dockerJson->addDns( "8.8.8.8" );
      $dockerJson->addDns( "8.8.4.4" );
      $dockerJson->addDnsSearch( "local" );
      $dockerJson->addCmd( "/root/scripts/start.sh" );
      $docker = new DockerRun();
      $dockerId = $docker->create( $dockerJson->getJson() );
      $docker->start( $dockerId );
    } catch (Exception $e) {
      $msg = 'Falha ao inicializar serviço docker!' . $e->getMessage();
    }
    return $msg;
  }
}
?>
