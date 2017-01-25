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
  }

  public function buildVersion( $Versao ){
    try {
      FileTools::rmDIr( $this->Path );
      FileTools::checkDir( $this->Path );
      $Config = new GitLabConfig();
      $cmd = self::GIT_BIN . " clone http://infra.libresolucoes:halegria@" . $Config->GitUrl . "/transparencia/" . $Versao.".git " . $this->Path;
      echo "Executando:" . $cmd . "\n";
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function checkoutTag( $Tags ){
  }


  public function init(){

    if ( ! copy( $this->Path . "/app/config/database.php.dist", $this->Path . "/app/config/database.php" ) ){
      throw new Exception("Falhou ao inicializar app/config/database.php", 1);
    }

    if ( is_file( $this->Path . "/app/config/routes.php.dist" ) ){
      if ( ! copy( $this->Path . "/app/config/routes.php.dist", $this->Path . "/app/config/routes.php" ) ){
        throw new Exception("Falhou ao inicializar app/config/routes.php", 1);
      }
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
