<?php
require_once __DIR__ . '/../../../Html/SimpleTable.class.php';
class Ecidade{
  private $buildName;
  public function __construct( $buildName, $Method  ){
    $this->buildName = $buildName;
    echo $this->getInfo();
  }

  private function getInfo(){
    $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/Ecidade_ports.conf');
    $dockerPort = trim( $dockerPort );

    $base = '<div class="alert alert-info" id="Ecidade_retorno" style="display: none;"></div>';

    $table = new SimpleTable( 'Table' );
    $table->addHead( array( 'Serviço', 'Acesso' ) );
    $table->addline( array( 'e-cidade', '<a href="http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '" target="_blank">http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '</a>' ) );
    $table->addline( array( 'git', 'git clone http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_git ' . $this->buildName ) );
    $table->addline( array( 'ssh', '<a href="http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_ssh" target="_blank">http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_ssh</a>' ) );

    $dockerButtons = '<div class="btn-group btn-group-sm" role="group" aria-label="">
    <button type="button" class="btn btn-danger" onclick="javascript:EcidadeDocker(\'dockerStop\')">
     <i class="fa fa-stop"></i> Stop
     </button>
     <button type="button" class="btn btn-success" onclick="javascript:EcidadeDocker(\'dockerStart\')">
    <i class="fa fa-play"></i> Start
  </button></div>';
      $table->addline( array( 'docker', $dockerButtons ) );
      $script = '<script type="text/javascript">
      function EcidadeDocker( Method ){
        $("#Ecidade_retorno").html(\'Carregando...\');
      	$("#Ecidade_retorno").removeAttr("style", "display:none;").fadeIn();
        $.ajax({
          data: {ctrl: \'Ecidade\', method: Method },
          type: "POST",
          url: "' . $_SERVER['REQUEST_URI'] . '",
          success: function(html) {
            $("#Ecidade_retorno").html(html);
  		      $("#Ecidade_retorno").delay(10000).fadeOut();
          }
        });
      }
      ';

    return $base . $table->print() . $script;
  }
}
?>
