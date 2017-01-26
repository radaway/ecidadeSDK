<?php

class BuildManager{

  public function __construct( $buildName ){
    $lsDir = array_diff( scandir( '/var/www/builds/' . $buildName . '/builds'), array( '.', '..' ) );
    print_r($lsDir);
  }

}

 ?>
