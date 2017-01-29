<?php
require_once __DIR__ . '/InfoDefault.php';
class Ecidade{
  private $buildName;
  public function __construct( $buildName, $Method  ){
    $this->buildName = $buildName;
    echo $this->getInfo();
  }

  private function getInfo(){
    $info = new InfoDefault($this->buildName);
    return $info->getInfo( "Ecidade" );
  }
}
?>
