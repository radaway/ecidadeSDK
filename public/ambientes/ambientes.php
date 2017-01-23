<?php
require_once __DIR__ . '/../../src/Html/Form.class.php';
require_once __DIR__ . '/../../src/GitLab/class/Groups.class.php';

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
echo $Form->print();
 ?>

 <script type="text/javascript">
 $("#grupo").on("change",function() {
   if ($("#grupo").val() != "selecione") {
     var grupo = $("#grupo").val();
     var action = "getProjects";
     $("#grupo").attr("disabled", true);
     $("#projeto").attr("disabled", true);
     $.ajax({
       data: {grupo: grupo, action: action},
       type: 'POST',
       url: "ambientes/ambientes_RPC.php",
       success: function (html) {
         $("#projeto").html(html);
         $("#grupo").removeAttr("disabled", "disabled");
         $("#projeto").removeAttr("disabled", "disabled");
       }
     });
   } else {
     $("#projeto").html("<option value='selecione'>Selecione</option>");
   }
 });
 </script>
