<?php
require_once __DIR__ . '/../../../Html/SimpleTable.class.php';
class Ecidade{
  private $buildName;
  public function __construct( $buildName, $Method  ){
    $this->buildName = $buildName;
    echo $this->getInfo();
  }

  private function getInfo(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf');
    $dockerPort = trim( $dockerPort );
    $table = new SimpleTable( 'Table' );
    $table->addHead( array( 'Serviço', 'Acesso' ) );
    $table->addline( array( 'e-cidade', '<a href="http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '" target="_blank">http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '</a>' ) );
    $table->addline( array( 'git', 'http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_git' ) );
    $table->addline( array( 'ssh', '<a href="http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_ssh" target="_blank">http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_ssh</a>' ) );

    $dockerButtons = '<button type="button" class="btn btn-danger" onclick="javascript:DockerStop(' . $dockerPort . ')">
     <i class="fa fa-stop"></i> Stop
     </button>
     <button type="button" class="btn btn-success" onclick="javascript:DockerStart(\'' . $this->buildName . '\', \'apache_ecidade\', \'' . $dockerPort . '\')">
    <i class="fa fa-play"></i> Start
  </button>';
      $table->addline( array( 'docker', $dockerButtons ) );
    return $table->print();

  }
}

 ?>
