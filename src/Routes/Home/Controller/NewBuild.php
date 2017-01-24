<?php
class NewBuild{

  public function __construct(){
    switch ($_POST['method']) {
      case 'getProjects':
        echo $this->getProjects( $_POST['grupo'] );
        break;
      case 'NewBuild':
        echo $this->newBuild();
        break;
      default:
        # code...
        break;
    }
  }

  private function getProjects( $Grupo ){
    require_once __DIR__ . '/../../../GitLab/class/Groups.class.php';
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

  private function newBuild(){
    $erro = true;
    $msg = "";
    return json_encode( array( 'erro' => $erro, 'msg' => $msg ) );
  }

}
?>
