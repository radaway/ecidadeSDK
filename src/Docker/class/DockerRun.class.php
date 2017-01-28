<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/DockerRequest.class.php';
class DockerRun extends DockerRequest{
  public function create( $dockerJson ){
    try {
      $result = $this->containerRequest( null, 'create', $dockerJson );
    } catch (Exception $e) {
      throw new Exception("Falha ao finalizar o docker " . $idC, 1);
    }
    return $result->Id;
  }

  public function start( $idC ){
    try {
      $result = $this->containerRequest( $idC, 'start' );
    } catch (Exception $e) {
      throw new Exception("Falha ao inicializar o docker " . $idC, 1);
    }
    return $result;
  }
}
?>
