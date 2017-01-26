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
    require_once __DIR__ . '/../../../Jenkins/class/Jobs.class.php';
    $erro = true;
    $msg = "";

    if( $_POST['grupo'] == 'selecione' ){
      $msg = "Informe grupo de projeto!";
      return json_encode( array( 'erro' => $erro, 'msg' => $msg ) );
    }

    if( $_POST['projeto'] == 'selecione' ){
      $msg = "Informe projeto!";
      return json_encode( array( 'erro' => $erro, 'msg' => $msg ) );
    }

    if( trim( $_POST['nome'] ) == '' ){
      $msg = "Informe nome para build!";
      return json_encode( array( 'erro' => $erro, 'msg' => $msg ) );
    }

    switch ($_POST['grupo']) {
      case 'e-cidade':
        $grupo = "Ecidade";
        break;
      case 'e-cidadeonline':
        $grupo = "EcidadeOnline";
        break;
      case 'e-cidadeonline2':
        $grupo = "EcidadeOnline2";
        break;
      case 'matriculaonline':
        $grupo = "MatriculaOnline";
        break;
      case 'portaldoaluno':
        $grupo = "PortalDoAluno";
        break;
      case 'transparencia':
        $grupo = "Transparencia";
        break;
      default:
        $msg = "Grupo de Projeto inválido!";
        return json_encode( array( 'erro' => $erro, 'msg' => $msg ) );
        break;
    }
    $nome = preg_replace('/[^A-Za-z0-9]/', '', $_POST['nome']);

    try {
      $job = new Jobs( "nova_build" );
      $job->addParameter( "GRUPO", $grupo );
      $job->addParameter( "PROJETO", $_POST['projeto'] );
      $job->addParameter( "NOME", $nome );
      $msg = $job->build();
    } catch (Exception $e) {
      $msg = $e->getMessage();
      return json_encode( array( 'erro' => $erro, 'msg' => $msg ) );
    }
    $erro = false;
    $msg = "Solicitação de build enviada";
    return json_encode( array( 'erro' => $erro, 'msg' => $msg ) );
  }

}
?>
