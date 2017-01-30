<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/DockerRequest.class.php';
class DockerStop extends DockerRequest{
  public function stopById( $idC ){
    $result = null;
    try {
      $result = $this->containerRequest( $idC, 'stop' );
    } catch (Exception $e) {
      throw new Exception("Falha ao parar o docker " . $idC, 1);
    }
    return $result;
  }
  public function killById( $idC ){
    $result = null;
    try {
      $result = $this->containerRequest( $idC, 'kill' );
    } catch (Exception $e) {
      throw new Exception("Falha ao finalizar o docker " . $idC, 1);
    }
    return $result;
  }
  public function deleteById( $idC ){
    $result = null;
    try {
      $result = $this->containerRequest( $idC, 'delete' );
    } catch (Exception $e) {
      throw new Exception("Falha ao remover imagem " . $idC, 1);
    }
    return $result;
  }

  public function deleteAllStoped(){
    $result = null;
    try {
      $result = $this->containerRequest( null, 'prune' );
    } catch (Exception $e) {
      throw new Exception("Falha ao remover imagem " . $idC, 1);
    }
    return $result;
  }

}
?>
