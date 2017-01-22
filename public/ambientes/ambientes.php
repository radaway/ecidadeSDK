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
