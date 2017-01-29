<?php
require_once __DIR__ . '/../../../Html/Form.class.php';
require_once __DIR__ . '/../../../Html/SimpleTable.class.php';
require_once __DIR__ . '/../../../GitLab/class/Groups.class.php';
require_once __DIR__ . '/../../../Jenkins/class/Jobs.class.php';

class NewBuild{

  public function __construct( $func ){
    echo $this->$func();
  }

  private function new(){
    $Grupos = new GitLabGroups();
    $select = array();
    $select['selecione'] = "Selecione";
    foreach ( $Grupos->getGroups() as $Group ) {
      $select[$Group->name] = $Group->name;
    }

    $Form = new HtmlForm('Build');
    $Form->addHead( "Nova Build" );
    $Form->addSelect( "grupo", "Grupo de Projeto", $select );
    $Form->addSelect( "projeto", "Projeto", array( 'selecione'=>'Selecione' ) );
    $Form->addText( "nome", "Nome" );
    $Form->addSubmit( "Gerar", "NewBuild", "NewBuild", "reloadBuildList();" );
    $retorno = $Form->print();
    $retorno .= '<div id="buildList">' . $this->buildList() . '</div>';
    $script = '<script type="text/javascript">
    $("#grupo").on("change",function() {
      if ($("#grupo").val() != "selecione") {
        var grupo = $("#grupo").val();
        $("#grupo").attr("disabled", true);
        $("#projeto").attr("disabled", true);
        $.ajax({
          data: {ctrl: "NewBuild", method: "getProjects", grupo: grupo},
          type: "POST",
          url: "' . $_SERVER['REQUEST_URI'] . '",
          success: function (html) {
            $("#projeto").html(html);
            $("#grupo").removeAttr("disabled", "disabled");
            $("#projeto").removeAttr("disabled", "disabled");
          }
        });
      } else {
        $("#projeto").html("<option value=\"selecione\">Selecione</option>");
      }
    });
    function reloadBuildList(){
      $.ajax({
        data: {view: "NewBuild", method: "buildList"},
        type: "POST",
        url: "' . $_SERVER['REQUEST_URI'] . '",
        success: function(html){
           $("#buildList").html(html);
        }
      });
    }
    </script>
    ';
    return $retorno . $script;
  }

  public function buildList(){
    $table = new SimpleTable( 'Builds' );
    $table->addHead( array( 'Id', 'Nome', 'Grupo', 'Projeto', 'Estado', 'Acesso', 'Log' ) );
    $job = new Jobs( "nova_build" );
    foreach ($job->getJobs() as $key => $value) {
      $table->addCollum( $key );
      $table->addCollum( $value['params']['NOME'] );
      $table->addCollum( $value['params']['GRUPO'] );
      $table->addCollum( $value['params']['PROJETO'] );
      $table->addCollum( $value['status'] );
      $table->addButton( '', 'external-link', 'window.open(\'build/' . $value['params']['NOME'] .'\',\'_blank\')' );
      $table->addButton( '', 'file-text-o', 'window.open(\'' . $value['url'] .'console\',\'_blank\')' );
      $table->addLine();
    }
    return $table->print();
  }

}

?>
