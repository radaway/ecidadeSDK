<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__  . '/CvsCheckout.class.php';
Class TransparenciaProject implements Project{

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
      $cmd = self::RSYNC_BIN . " -r -t -s rsync://" . $ServerName . ".dbseller.com.br/memcache/transparencia/" . $Versao . "/ecidade_transparencia.completo/* " . $this->Path . "/";
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
    $cvs->GetFilesByTags( "ecidade_transparencia", $this->Path, $Tags );
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
