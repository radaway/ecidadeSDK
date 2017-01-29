<?php
require_once __DIR__ . '/../../../Html/SimpleTable.class.php';
require_once __DIR__ . '/InfoDefault.php';
class Ecidade{
  private $buildName;
  public function __construct( $buildName, $Method  ){
    $this->buildName = $buildName;
    echo $this->getInfo();
  }

  private function getInfo(){
    $info = new InfoDefault();
    return $info->getInfo( "Ecidade" );
  }
}
?>
