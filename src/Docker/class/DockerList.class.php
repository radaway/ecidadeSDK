<?php
require_once __DIR__ . '/../config/config.php';
class DockerList{

  private $config;

  public function __construct() {
    $this->config = new DockerConfig();
  }

  public function getDockerByDir( $dir ){
    $ch = curl_init($this->config->socket . '/containers/json');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_POSTFIELDS, null);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json' )
    );
    $result = curl_exec($ch);
    $id = null;
    foreach (json_decode( $result ) as $value) {
      if( $value->Mounts[0]->Source == $dir ){
        $id = $value->Id;
        break;
      }
    }
    return $id;
  }
}
?>
