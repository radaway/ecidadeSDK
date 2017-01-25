<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__ . '/../../../GitLab/config/config.php';
Class EcidadeOnlineProject implements Project{

  const COMPOSER_BIN = "/usr/local/bin/composer";

  const GIT_BIN = "/usr/bin/git";

  private $Path = null;

  function __construct( $Path ){
    $this->Path = $Path;
  }

  private function distInitialize(){
    if( ! copy( $this->Path . "/libs/db_conn.php.dist", $this->Path . "/libs/db_conn.php" ) ){
      throw new Exception("Falhou ao inicializar libs/db_conn.php", 1);
    }
    if( ! copy( $this->Path . "/libs/config.mail.php.dist", $this->Path . "/libs/config.mail.php" ) ){
      throw new Exception("Falhou ao inicializar libs/config.mail.php", 1);
    }
  }
  private function parseConfigDefault(){
    $Config = new GitLabConfig();
    FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sHost[ \s]*=[ \s].*;', '$sHost = "' . $Config->SmtpHost. '";' );
    FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sPort[ \s]*=[ \s].*;', '$sPort = "' . $Config->SmtpPort . '";' );
    FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sUser[ \s]*=[ \s].*;', '$sUser = "' . $Config->SmtpUser . '";' );
    FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sPass[ \s]*=[ \s].*;', '$sPass = "' . $Config->SmtpPass . '";' );
    $ssl = "";
    if( $Config->SmtpSSL ){
      $ssl = "ssl";
    }
    FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$sSslt[ \s]*=[ \s].*;', '$sSslt = "' . $ssl . '";' );
    FileTools::strReplace( $this->Path . "/libs/config.mail.php", '$bAuth[ \s]*=[ \s].*;', '$bAuth = true;' );
  }
  private function iniFolder(){
    FileTools::rmDir( $this->Path . "/tmp" );
    FileTools::checkDir( $this->Path . "/tmp" );
  }

  public function buildVersion( $Versao ){
    try {
      FileTools::rmDIr( $this->Path );
      FileTools::checkDir( $this->Path );
      $Config = new GitLabConfig();
      echo " ------------------ CLONANDO --------------------\n";
      $cmd = self::GIT_BIN . " clone http://" . $Config->GitUser . ":" . $Config->GitKey . "@" . $Config->GitUrl . "/e-cidadeonline/" . $Versao.".git " . $this->Path;
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
      $this->iniFolder();
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
