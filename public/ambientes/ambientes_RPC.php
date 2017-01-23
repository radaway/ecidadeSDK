<?php

function getProjects( $Grupo ){
  require_once __DIR__ . '/../../src/GitLab/class/Groups.class.php';
  $Grupos = new GitLabGroups();
  $retorno = '<option value="selecione">Selecione</option>';
  if( $Grupo == "selecione" ){
    return $retorno;
  }
  foreach ( $Grupos->getProjects( $Grupo ) as $Project){
    $retorno .= '<option value="' . $Project->path . '">' . $Project->path . '</option>';
  }
  return $retorno;
}

switch ($_POST['action']) {
  case 'getProjects':
    echo getProjects( $_POST['grupo'] );
    break;

  default:
    # code...
    break;
}

?>
