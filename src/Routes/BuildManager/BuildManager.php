<?php

class BuildManager{

  public function __construct( $buildName ){
    $lsDir = array_diff( scandir( '/var/www/builds/' . $buildName), array( '.', '..' ) );
    print_r($lsDir);   
  }

}

 ?>
