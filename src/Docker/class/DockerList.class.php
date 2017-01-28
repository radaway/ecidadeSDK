<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/DockerRequest.php';
class DockerList extends DockerRequest{

  private $config;

  public function __construct() {
  }

  public function getDockerByDir( $dir ){
      $request = null;
    try {
      $request = $this->containerRequest( null, 'lits' );
    } catch (Exception $e) {
      throw new Exception("Falha ao listar containers", 1);
    }
    $id = null;
    foreach ( $request as $docker ) {
      foreach ( $docker->Mounts as $mnt ) {
        if( $mnt->Source == $dir ){
          return $docker->Id;
        }
      }
    }
    return $id;
  }



}
?>
