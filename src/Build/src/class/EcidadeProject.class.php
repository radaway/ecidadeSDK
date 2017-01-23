<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__ . '/FileTools.class.php';
require_once __DIR__ . '/Bash.class.php';
require_once __DIR__ . '/CvsCheckout.class.php';
require_once __DIR__ . '/../../../GitLab/config/config.php';
Class EcidadeProject implements Project{

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
      $cmd = self::GIT_BIN . " clone http://infra.libresolucoes:halegria@" . $Config->GitUrl . "/e-cidade/" . $Versao.".git " . $this->Path;
      echo "Executando:" . $cmd . "\n";
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

    public function checkoutTag( $Tag ){
    }

    public function init(){

      try {
        FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sHost[ \s]*=[ \s].*;', '$sHost = "smtp.dbseller.com.br";' );
        FileTools::strReplace( $this->Path . "/libs/config.mail.php.dist", '$sPort[ \s]*=[ \s].*;', '$sPort = "25";' );
        FileTools::strReplace( $this->Path . "/libs/db_conn.php.dist", '$DB_VALIDA_REQUISITOS[ \s]*=[ \s].*;', '$DB_VALIDA_REQUISITOS = false;' );
        FileTools::strReplace( $this->Path . "/libs/db_conn.php.dist", '$DB_NETSTAT[ \s]*=[ \s].*;', '$DB_NETSTAT = "netstat";\ninclude_once("/etc/dbseller/array_servidores.php");' );
      } catch (Exception $e) {
        throw $e;
      }

      if( ! copy( $this->Path . "/libs/db_conn.php.dist", $this->Path . "/libs/db_conn.php" ) ){
        throw new Exception("Falhou ao inicializar libs/db_conn.php", 1);
      }
      if( ! copy( $this->Path . "/libs/config.mail.php.dist", $this->Path . "/libs/config.mail.php" ) ){
        throw new Exception("Falhou ao inicializar libs/config.mail.php", 1);
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
