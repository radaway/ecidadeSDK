<?php
require_once __DIR__ . '/../config/config.php';

Class GitLabGroups {
  private $Config;
  public function __construct(){
    $this->Config = new GitLabConfig();
  }

  public function getGroups(){
    $url = $this->Config->GitUrl . "/api/v3/groups/";
    $url .= "?private_token=" . $this->Config->GitKey;
    $retorno = file_get_contents( $url );
    if ( $retorno === false ) {
      throw new Exception("Falha ao requisitar " . $url , 1);
    }
    return json_decode( $retorno );
  }

  public function getProjects( $group ){
    $url = $this->Config->GitUrl . "/api/v3/groups/";
    $url .= $group . "/?private_token=" . $this->Config->GitKey;
    $retorno = file_get_contents( $url );
    if ( $retorno === false ) {
      throw new Exception("Falha ao requisitar " . $url , 1);
    }
    $retorno = json_decode( $retorno );
    return $retorno->projects;
  }
}
?>
