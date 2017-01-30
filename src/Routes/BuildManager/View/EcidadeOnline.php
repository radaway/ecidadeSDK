<?php
require_once __DIR__ . '/InfoDefault.php';
class EcidadeOnline{
  private $buildName;
  public function __construct( $buildName, $Method  ){
    $this->buildName = $buildName;
    echo $this->getInfo();
  }
  private function getInfo(){
    $info = new InfoDefault( $this->buildName );
    return $info->getInfo( "EcidadeOnline" );
  }
}
?>
