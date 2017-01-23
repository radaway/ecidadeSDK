<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__  . '/CvsCheckout.class.php';
Class EcidadeNiteroiProject implements Project{

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
      $cmd = self::GIT_BIN . " clone http://releases.dbseller.com.br/git_ecidade_niteroi " . $this->Path;
      echo "Executando:" . $cmd . "\n";
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function checkoutTag( $Tags ){
    if ( ! is_array( $Tags ) ){
      $Tags = array( trim( $Tags ) );
    }
    $cvs = new CvsCheckout( "cvs.dbseller.com.br:2401/home/cvs", "dbintegracao", "halegria" );
    $cvs->GetFilesByTags( "dbportal_prj", $this->Path, $Tags );
  }

  public function init(){

    try {
      FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sHost[ \s]*=[ \s].*;', '$sHost = "smtp.dbseller.com.br";' );
      FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sPort[ \s]*=[ \s].*;', '$sPort = "25";' );
      FileTools::strReplace( $this->Path . "/libs/db_conn.php.dist", '$DB_VALIDA_REQUISITOS[ \s]*=[ \s].*;', '$DB_VALIDA_REQUISITOS = false;' );
      FileTools::strReplace( $this->Path . "/libs/db_conn.php.dist", '$DB_NETSTAT[ \s]*=[ \s].*;', '$DB_NETSTAT = "netstat";\ninclude_once("/etc/dbseller/array_servidores.php");' );
      FileTools::strReplace( $this->Path . "/config/plugins.json.dist", '"senha".*"', '"senha" : "halegria"' );
      FileTools::strReplace( $this->Path . "/.htaccess", 'php_value.*', '' );
    } catch (Exception $e) {
      throw $e;
    }

    if( ! copy( $this->Path . "/libs/db_conn.php.dist", $this->Path . "/libs/db_conn.php" ) ){
      throw new Exception("Falhou ao inicializar libs/db_conn.php", 1);
    }
    if( ! copy( $this->Path . "/libs/config.mail.php.dist", $this->Path . "/libs/config.mail.php" ) ){
      throw new Exception("Falhou ao inicializar libs/config.mail.php", 1);
    }

    if( ! copy( $this->Path . "/config/require_extensions.xml.dist", $this->Path . "/config/require_extensions.xml" ) ){
      throw new Exception("Falhou ao inicializar config/require_extensions.xml", 1);
    }

    if( ! copy( $this->Path . "/config/plugins.json.dist", $this->Path . "/config/plugins.json" ) ){
      throw new Exception("Falhou ao inicializar config/plugins.json", 1);
    }

    try {
      $dirlist = array( $this->Path . "/plugins",
      $this->Path . "/modification/xml",
      $this->Path . "/modification/cache",
      $this->Path . "/modification/log",
      $this->Path . "/cache/forms",
      $this->Path . "/cache/menus",
      $this->Path . "/cache/preferencias",
      $this->Path . "/tmp" );
      FileTools::rmDir( $dirlist );
      FileTools::checkDir( $dirlist );
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