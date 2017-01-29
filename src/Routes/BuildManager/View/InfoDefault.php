<?php

class InfoDefault{

public function getInfo( $folder ){
  $dockerPort = file_get_contents( '/var/www/builds/' . $this->buildName . '/builds/' . $folder . '_ports.conf');
  $dockerPort = trim( $dockerPort );

  $base = '<div class="alert alert-info" id="' . $folder . '_retorno" style="display: none;"></div>';

  $table = new SimpleTable( 'Table' );
  $table->addHead( array( 'Serviço', 'Acesso' ) );
  $table->addline( array( $folder, '<a href="http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '" target="_blank">http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '</a>' ) );
  $table->addline( array( 'git', 'git clone http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_git ' . $this->buildName ) );
  $table->addline( array( 'ssh', '<a href="http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_ssh" target="_blank">http://' . $_SERVER['SERVER_NAME'] . ':' . $dockerPort . '/_ssh</a>' ) );

  $dockerButtons = '<div class="btn-group btn-group-sm" role="group" aria-label="">
  <button type="button" class="btn btn-danger" onclick="javascript:' . $folder . 'Docker(\'dockerStop\')">
   <i class="fa fa-stop"></i> Stop
   </button>
   <button type="button" class="btn btn-success" onclick="javascript:' . $folder . 'Docker(\'dockerStart\')">
  <i class="fa fa-play"></i> Start
</button></div>';
    $table->addline( array( 'docker', $dockerButtons ) );
    $script = '<script type="text/javascript">
    function ' . $folder . 'Docker( Method ){
      $("#Ecidade_retorno").html(\'Carregando...\');
      $("#Ecidade_retorno").removeAttr("style", "display:none;").fadeIn();
      $.ajax({
        data: {ctrl: \'' . $folder . '\', method: Method },
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
