<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__ . '/../../../GitLab/config/config.php';
require_once __DIR__ . '/../../../Smtp/config/config.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
Class PortalDoAlunoProject implements Project{

  const COMPOSER_BIN = "/usr/local/bin/composer";

  const GIT_BIN = "/usr/bin/git";

  private $Path = null;

  function __construct( $Path ){
    $this->Path = $Path;
  }

  private function distInitialize(){
    if ( ! copy( $this->Path . "/app/config/database.php.dist", $this->Path . "/app/config/database.php" ) ){
      throw new Exception("Falhou ao inicializar app/config/database.php", 1);
    }
    if ( ! copy( $this->Path . "/app/config/bootstrap.php.dist", $this->Path . "/app/config/bootstrap.php" ) ){
      throw new Exception("Falhou ao inicializar app/config/bootstrap.php", 1);
    }
    if ( ! copy( $this->Path . "/app/plugins/cms/config/bootstrap.php.dist", $this->Path . "/app/plugins/cms/config/bootstrap.php" ) ){
      throw new Exception("Falhou ao inicializar app/plugins/cms/config/bootstrap.php", 1);
    }
  }

  private function parseConfigDefault(){
    $Config = new SmtpConfig();
    FileTools::strReplace( $this->Path . "/app/config/bootstrap.php", 'Configure::write("Email.remetente",.*', 'Configure::write("Email.remetente", "' . $Config->SmtpUser . '");' );
    FileTools::strReplace( $this->Path . "/app/config/bootstrap.php", "'port'[ \s]*=>[ \s].*", "'port' => '" . $Config->SmtpPort . "'," );
    $Host = $Config->SmtpHost;
    if ( $Config->SSL ){
      $Host = "ssl://" . $Host;
    }
    FileTools::strReplace( $this->Path . "/app/config/bootstrap.php", "'host'[ \s]*=>[ \s].*", "'host' => '" . $Host . "'," );
    FileTools::strReplace( $this->Path . "/app/config/bootstrap.php", "'username'[ \s]*=>[ \s].*", "'username' => '" . $Config->SmtpUser . "'," );
    FileTools::strReplace( $this->Path . "/app/config/bootstrap.php", "'password'[ \s]*=>[ \s].*", "'password' => '" . $Config->SmtpPass . "'," );
    //ajusta pois o login criado no sql é admin
    FileTools::strReplace( $this->Path . "/app/plugins/cms/config/bootstrap.php", 'admin@dbseller.com.br', 'admin' );
  }

  public function buildVersion( $Versao ){
    try {
      FileTools::rmDIr( $this->Path );
      FileTools::checkDir( $this->Path );
      $Config = new GitLabConfig();
      echo " ------------------ CLONANDO --------------------";
      $cmd = self::GIT_BIN . " clone http://" . $Config->GitUser . ":" . $Config->GitKey . "@" . $Config->GitUrl . "/portaldoaluno/" . $Versao.".git " . $this->Path;
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
