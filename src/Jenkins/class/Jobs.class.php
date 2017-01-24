<?php

class Jobs{

  private $JobName;
  private $Parametes = array();

  public function __construct( $JobName ){
    $this->JobName = $JobName;
  }

  public function addParameter( $Key, $Value){
    $Key = trim( $Key );
    $this->Parametes[$Key] = $Value;
  }

  public function build(){

  }
}
?>
