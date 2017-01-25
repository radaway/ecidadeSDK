<?php
require_once __DIR__ . '/../../../Html/SimpleTable.class.php';
require_once __DIR__ . '/../../../GitLab/class/Groups.class.php';

class NewBuild{

  public function __construct( $func ){
    echo $this->$func();
  }

  private function List(){
    $table = new SimpleTable();
    $table->addHead( array( 'Build', 'Acessar' ) );
    $lsDir = array_diff( scandir( '/var/www/builds' ), array( '.', '..' ) );
    foreach ($lsDir as $value) {
      $table->addLine( array( $value, $value ) );
    }
    return $table->print();
  }

}

?>
