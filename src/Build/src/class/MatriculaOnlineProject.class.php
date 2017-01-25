<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__ . '/../../../GitLab/config/config.php';
Class MatriculaOnlineProject implements Project{

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
      $cmd = self::GIT_BIN . " clone http://infra.libresolucoes:halegria@" . $Config->GitUrl . "/matriculaonline/" . $Versao.".git " . $this->Path;
      echo "Executando:" . $cmd . "\n";
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function checkoutTag( $Tags ){
  }


  public function init(){

    try {
      FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sUser[ \s]*=[ \s].*', '$sUser = "infra@dbseller.com.br";' );
      FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sPass[ \s]*=[ \s].*', '$sPass = "";' );
      FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sHost[ \s]*=[ \s].*', '$sHost = "smtp.dbseller.com.br";' );
      FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sPort[ \s]*=[ \s].*', '$sPort = "25";' );
    } catch (Exception $e) {
      throw $e;
    }

    if ( ! copy( $this->Path . "/libs/db_conn.php.dist", $this->Path . "/libs/db_conn.php" ) ){
      throw new Exception("Falhou ao inicializar libs/db_conn.php.dist", 1);
    }

    ///if ( ! copy( $this->Path . "/libs/config.mail.php.dist", $this->Path . "/libs/config.mail.php" ) ){
      //throw new Exception("Falhou ao inicializar libs/config.mail.php", 1);
    //}

    if ( is_file( $this->Path . "/composer.json" ) ){
      $saveDir = getcwd();
      chdir( $this->Path );
      exec( self::COMPOSER_BIN . " install --no-dev" );
      chdir( $saveDir );
    }

  }

}
?>
