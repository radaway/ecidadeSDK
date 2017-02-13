<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__ . '/../../../GitLab/config/config.php';
Class TransparenciaProject implements Project{

  const COMPOSER_BIN = "/usr/local/bin/composer";

  const GIT_BIN = "/usr/bin/git";

  private $Path = null;

  function __construct( $Path ){
    $this->Path = $Path;
    echo $this->dockerStop();
  }

  private function dockerStop(){
    $msg = "Serviço docker encerrado!\n";
    try {
      $dockerL = new DockerList();
      $dockerId = $dockerL->getDockerByDir( $this->Path );
      if ( $dockerId == null ){
        return $msg;
      }
      $dockerS = new DockerStop();
      $dockerS->killById( $dockerId );
      $dockerS->deleteById( $dockerId );
    } catch (Exception $e) {
      $msg  = "Falha ao encerrar docker!\n" . $e->getMessage();
    }
    return $msg;
  }

  private function distInitialize(){
    if ( ! copy( $this->Path . "/app/config/database.php.dist", $this->Path . "/app/config/database.php" ) ){
      throw new Exception("Falhou ao inicializar app/config/database.php", 1);
    }

    if ( is_file( $this->Path . "/app/config/routes.php.dist" ) ){
      if ( ! copy( $this->Path . "/app/config/routes.php.dist", $this->Path . "/app/config/routes.php" ) ){
        throw new Exception("Falhou ao inicializar app/config/routes.php", 1);
      }
    }
  }
  private function parseConfigDefault(){
    return true;
  }

  public function buildVersion( $Versao ){
    try {
      FileTools::rmDIr( $this->Path );
      FileTools::checkDir( $this->Path );
      $Config = new GitLabConfig();
      echo " ------------------ CLONANDO --------------------\n";
      $cmd = self::GIT_BIN . " clone http://" . $Config->GitUser . ":" . $Config->GitKey . "@" . $Config->GitUrl . "/transparencia/" . $Versao.".git " . $this->Path;
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function checkoutTag( $Tag ){
    $saveDir = getcwd();
    chdir( $this->Path );
    exec( self::GIT_BIN . " checkout " . $Tag );
    chdir( $saveDir );
  }


  public function init(){

    try {
      $this->distInitialize();
      $this->parseConfigDefault();
    } catch (Exception $e) {
      throw $e;
    }


    if ( is_file( $this->Path . "/composer.json" ) ){
      $saveDir = getcwd();
      chdir( $this->Path );
      exec( self::COMPOSER_BIN . " install --no-dev" );
      chdir( $saveDir );
    }

  }

}
?>
