<?php
require_once __DIR__ . '/../config/config.php';

class DockerRequest{

  private $method;
  private $route;

  public function __construct(){
    $this->method = 'GET';
    $this->route = null;
  }

  protected function configStop( $idC ){
    if ( $idC == null ){ throw new Exception("Não informou id", 1); }
    $this->method = 'POST';
    $this->route = '/containers/' . $idC . '/stop';
  }
  protected function configStart( $idC ){
    if ( $idC == null ){ throw new Exception("Não informou id", 1); }
    $this->method = 'POST';
    $this->route = '/containers/' . $idC . '/start';
  }
  protected function configCreate(){
    $this->method = 'POST';
    $this->route = '/containers/create';
  }
  protected function configKill( $idC ){
    if ( $idC == null ){ throw new Exception("Não informou id", 1); }
    $this->method = 'POST';
    $this->route = '/containers/' . $idC . '/kill';
  }
  protected function configDelete( $idC ){
    if ( $idC == null ){ throw new Exception("Não informou id", 1); }
    $this->method = 'DELETE';
    $this->route = '/containers/' . $idC;
  }
  protected function configlist(){
    $this->method = 'GET';
    $this->route = '/containers/json';
  }


  private function configRoute( $action = null, $idC = null ){
    switch ( $action ) {
      case 'create':
        $this->configCreate();
        break;
      case 'start':
        $this->configStart( $idC );
        break;
      case 'kill':
        $this->configKill( $idC );
        break;
      case 'delete':
        $this-configDelete( $idC );
        break;
      case 'stop':
        $this->configStop( $idC );
        break;
      case 'list':
        $this->configList();
        break;
      default:
        throw new Exception("Rota inválida!", 1);
        break;
    }
  }

  public function containerRequest( $idC = null, $action = null,  $jsonData = null ){
    $header = array();
    $header[] = 'Content-Type: application/json';
    if( $jsonData != null ){
      $header[] = 'Content-Length: ' . strlen($jsonData);
    }
    try {
      $config = new DockerConfig();
      $this->configRoute( $action, $idC );
      $curl = curl_init( $config->socket . $this->route );
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
      $result = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      if( $httpCode != 201 ){
        throw new Exception("Resposta inválida!" . $result . '|' . $httpCode , 1);
      }
    } catch (Exception $e) {
      throw $e;
    }
    return json_decode( $result ) ;
  }

}

?>
