<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__ . '/../../../GitLab/config/config.php';
Class PortalDoServidorProject implements Project{

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
      $cmd = self::GIT_BIN . " clone http://releases.dbseller.com.br/git_portal_servidor " . $this->Path;
      echo "Executando:" . $cmd . "\n";
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function checkoutTag( $Tag ){
    $cmd = self::GIT_BIN . " checkout " . $Tag;
    try {
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function init(){

    try {
      FileTools::strReplace( $this->Path . "/.env.dist", 'MAIL_HOST=.*', 'MAIL_HOST=smtp.dbseller.com.br' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'MAIL_PORT=.*', 'MAIL_PORT=25' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'MAIL_USERNAME=.*', 'MAIL_USERNAME=' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'MAIL_PASSWORD=.*', 'MAIL_PSSWORD=' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'POSTGRES_DB_HOST=.*', 'POSTGRES_DB_HOST=' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'POSTGRES_DB_DATABASE=.*', 'POSTGRES_DB_DATABASE=' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'POSTGRES_DB_USERNAME=.*', 'POSTGRES_DB_USERNAME=' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'POSTGRES_DB_PASSWORD=.*', 'POSTGRES_DB_PASSWORD=' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'POSTGRES_DB_PORT=.*', 'POSTGRES_DB_PORT=' );
      FileTools::strReplace( $this->Path . "/.env.dist", 'URL_WEBSERVICE_ECIDADE=.*', 'URL_WEBSERVICE_ECIDADE=' );
    } catch (Exception $e) {
      throw $e;
    }

    if( ! copy( $this->Path . "/.env.dist", $this->Path . "/.env" ) ){
      throw new Exception("Falhou ao inicializar .env", 1);
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
