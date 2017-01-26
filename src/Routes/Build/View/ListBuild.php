<?php
require_once __DIR__ . '/../../../Html/SimpleTable.class.php';
require_once __DIR__ . '/../../../GitLab/class/Groups.class.php';

class ListBuild{

  public function __construct( $func ){
    echo $this->$func();
  }

  private function List(){
    $table = new SimpleTable( 'Table' );
    $table->addHead( array( 'Build', 'Acessar' ) );
    $lsDir = array_diff( scandir( '/var/www/builds' ), array( '.', '..' ) );
    foreach ($lsDir as $value) {
      $button = '<button type="button" class="btn btn-default btn-xs" onclick="javascript:openTab(\'build/' . $value . '\')">
				<span class="fa fa-external-link"></span> ' . $value . ' </button>';
      $table->addLine( array( $value, $button ) );
    }

    $script = '<script type="text/javascript">
function openTab(url){
  window.open(url,\'_blank\');
}
</script>';

    return $table->print() . $script;
  }

}

?>
