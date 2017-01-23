<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__  . '/CvsCheckout.class.php';
Class PortalDoAlunoProject implements Project{

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
      $cmd = self::RSYNC_BIN . " -r -t -s rsync://" . $ServerName . ".dbseller.com.br/memcache/portaldoaluno/" . $Versao . "/portalaluno.completo/* " . $this->Path . "/";
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
      $cvs->GetFilesByTags( "portalaluno", $this->Path, $Tags );
    }


    public function init(){

      try {
        FileTools::strReplace( $this->Path . "/app/config/bootstrap.php.dist", 'Configure::write("Email.remetente",.*', 'Configure::write("Email.remetente", "infra@dbseller.com.br");' );
        FileTools::strReplace( $this->Path . "/app/config/bootstrap.php.dist", "'port'[ \s]*=>[ \s].*", "'port' => '25'," );
        FileTools::strReplace( $this->Path . "/app/config/bootstrap.php.dist", "'host'[ \s]*=>[ \s].*", "'host' => 'smtp.dbseller.com.br'," );
        FileTools::strReplace( $this->Path . "/app/plugins/cms/config/bootstrap.php", 'admin@dbseller.com.br', 'admin' );
      } catch (Exception $e) {
        throw $e;
      }

      if ( ! copy( $this->Path . "/app/config/database.php.dist", $this->Path . "/app/config/database.php" ) ){
        throw new Exception("Falhou ao inicializar app/config/database.php", 1);
      }
      if ( ! copy( $this->Path . "/app/config/bootstrap.php.dist", $this->Path . "/app/config/bootstrap.php" ) ){
        throw new Exception("Falhou ao inicializar app/config/bootstrap.php", 1);
      }

      if ( ! copy( $this->Path . "/app/plugins/cms/config/bootstrap.php.dist", $this->Path . "/app/plugins/cms/config/bootstrap.php" ) ){
        throw new Exception("Falhou ao inicializar app/plugins/cms/config/bootstrap.php", 1);
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
