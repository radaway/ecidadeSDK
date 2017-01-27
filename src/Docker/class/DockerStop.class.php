<?php
require_once __DIR__ . '/../config/config.php';
class DockerStop{

  public function killById( $id ){
    $url = $this->config->socket . '/containers/' . $id . '/kill';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, null);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json', )
    );
    $result = curl_exec($ch);
    return $result;
  }

  public function deleteById( $id ){
    $url = $this->config->socket . '/containers/' . $id;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, null);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json', )
    );
    $result = curl_exec($ch);
    return $result;
  }

}


?>
