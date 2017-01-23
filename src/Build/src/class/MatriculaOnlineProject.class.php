<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__  . '/CvsCheckout.class.php';
Class MatriculaOnlineProject implements Project{

  const RSYNC_BIN = "/usr/bin/rsync";

  const COMPOSER_BIN = "/usr/local/bin/composer";

  private $Path = null;

  function __construct( $Path ){
    $this->Path = $Path;
  }

  public function buildVersion( $Versao ){
    try {
      $ServerName = "releases";
      if ( gethostname() == "jenkins" ){
        $ServerName = "jenkins";
      }
      FileTools::rmDIr( $this->Path );
      FileTools::checkDir( $this->Path );
      $cmd = self::RSYNC_BIN . " -r -t -s rsync://" . $ServerName . ".dbseller.com.br/memcache/matriculaonline/" . $Versao . "/matricula_online.completo/* " . $this->Path . "/";
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
    $cvs->GetFilesByTags( "matricula_online", $this->Path, $Tags );
  }


  public function init(){

    try {
      FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sUser[ \s]*=[ \s].*', '$sUser = "infra@dbseller.com.br";' );
      FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sPass[ \s]*=[ \s].*', '$sPass = "";' );
      FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sHost[ \s]*=[ \s].*', '$sHost = "smtp.dbseller.com.br";' );
      FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sPort[ \s]*=[ \s].*', '$sPort = "25";' ); 
    } catch (Exception $e) {
      throw $e;
    }

    if ( ! copy( $this->Path . "/libs/db_conn.php.dist", $this->Path . "/libs/db_conn.php" ) ){
      throw new Exception("Falhou ao inicializar libs/db_conn.php.dist", 1);
    }

    if ( ! copy( $this->Path . "/libs/config.mail.php.dist", $this->Path . "/libs/config.mail.php" ) ){
      throw new Exception("Falhou ao inicializar libs/config.mail.php", 1);
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
