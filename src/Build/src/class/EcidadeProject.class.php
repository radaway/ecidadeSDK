<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__ . '/FileTools.class.php';
require_once __DIR__ . '/Bash.class.php';
require_once __DIR__ . '/../../../GitLab/config/config.php';
require_once __DIR__ . '/../../../Docker/class/DockerList.class.php';
require_once __DIR__ . '/../../../Docker/class/DockerStop.class.php';
Class EcidadeProject implements Project{

  const COMPOSER_BIN = "/usr/local/bin/composer";

  const GIT_BIN = "/usr/bin/git";

  private $Path = null;

  function __construct( $Path ){
    $this->Path = $Path;
    echo $this->dockerStop();
  }

  private function dockerStop(){
    $msg = "Serviço docker encerrado!\n";
    try {
      $dockerL = new DockerList();
      $dockerId = $dockerL->getDockerByDir( $this->Path );
      if ( $dockerId == null ){
        return $msg;
      }
      $dockerS = new DockerStop();
      $dockerS->killById( $dockerId );
      $dockerS->deleteById( $dockerId );
    } catch (Exception $e) {
      $msg  = "Falha ao encerrar docker!\n" . $e->getMessage();
    }
    return $msg;
  }

  private function distInitialize(){
    if( ! copy( $this->Path . "/libs/db_conn.php.dist", $this->Path . "/libs/db_conn.php" ) ){
      throw new Exception("Falhou ao inicializar libs/db_conn.php", 1);
    }
    if( ! copy( $this->Path . "/libs/config.mail.php.dist", $this->Path . "/libs/config.mail.php" ) ){
      throw new Exception("Falhou ao inicializar libs/config.mail.php", 1);
    }
    if( ! copy( $this->Path . "/libs/db_cubo_bi_config.php.dist", $this->Path . "/libs/db_cubo_bi_config.php" ) ){
      throw new Exception("Falhou ao inicializar libs/db_cubo_bi_config.php", 1);
    }
    if( ! copy( $this->Path . "/config/require_extensions.xml.dist", $this->Path . "/config/require_extensions.xml" ) ){
      throw new Exception("Falhou ao inicializar /config/require_extensions.xml", 1);
    }
    if( ! copy( $this->Path . "/config/config.php.dist", $this->Path . "/config/config.php" ) ){
      throw new Exception("Falhou ao inicializar /config/config.php", 1);
    }
    if( ! copy( $this->Path . "/config/plugins.json.dist", $this->Path . "/config/plugins.json" ) ){
      throw new Exception("Falhou ao inicializar /config/plugins.json", 1);
    }
    if( ! copy( $this->Path . "/config/pcasp.txt.dist", $this->Path . "/config/pcasp.txt" ) ){
      throw new Exception("Falhou ao inicializar config/pcasp.txt", 1);
    }
    if( ! copy( $this->Path . "/integracao_externa/sigcorp/libs/db_config.ini.dist", $this->Path . "/integracao_externa/sigcorp/libs/db_config.ini" ) ){
      throw new Exception("Falhou ao inicializar integracao_externa/sigcorp/libs/db_config.ini", 1);
    }
    if( ! copy( $this->Path . "/integracao_externa/webiss/libs/db_config.ini.dist", $this->Path . "/integracao_externa/webiss/libs/db_config.ini" ) ){
      throw new Exception("Falhou ao inicializar integracao_externa/webiss/libs/db_config.ini", 1);
    }
    if( ! copy( $this->Path . "/integracao_externa/portal_transparencia/libs/db_config.ini.dist", $this->Path . "/integracao_externa/portal_transparencia/libs/db_config.ini" ) ){
      throw new Exception("Falhou ao inicializar integracao_externa/portal_transparencia/libs/db_config.ini", 1);
    }
    if( ! copy( $this->Path . "/integracao_externa/portal_transparencia/libs/config.ini.dist", $this->Path . "/integracao_externa/portal_transparencia/libs/config.ini" ) ){
      throw new Exception("Falhou ao inicializar integracao_externa/portal_transparencia/libs/config.ini", 1);
    }
    if( ! copy( $this->Path . "/integracao_externa/gissonline/lib/db_config.ini.dist", $this->Path . "/integracao_externa/gissonline/lib/db_config.ini" ) ){
      throw new Exception("Falhou ao inicializar integracao_externa/gissonline/lib/db_config.ini", 1);
    }
    if( ! copy( $this->Path . "/integracao_externa/debitos/lib/debitos.conf.dist", $this->Path . "/integracao_externa/debitos/lib/debitos.conf" ) ){
      throw new Exception("Falhou ao inicializar integracao_externa/debitos/lib/debitos.conf", 1);
    }
    if( ! copy( $this->Path . "/integracao_externa/ged/libs/configuracao_ged.ini.dist", $this->Path . "/integracao_externa/ged/libs/configuracao_ged.ini" ) ){
      throw new Exception("Falhou ao inicializar integracao_externa/ged/libs/configuracao_ged.ini", 1);
    }
  }

  private function parseConfigDefault(){
    FileTools::strReplace( $this->Path . "/libs/db_conn.php", '$DB_VALIDA_REQUISITOS[ \s]*=[ \s].*;', '$DB_VALIDA_REQUISITOS = false;' );
    //FileTools::strReplace( $this->Path . "/libs/db_conn.php", '$DB_NETSTAT[ \s]*=[ \s].*;', '$DB_NETSTAT = "netstat";\ninclude_once("/etc/dbseller/array_servidores.php");' );
  }

  private function iniFolders(){
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
  }

  public function buildVersion( $Versao ){
    try {
      FileTools::rmDIr( $this->Path );
      FileTools::checkDir( $this->Path );
      $Config = new GitLabConfig();
      echo " ------------------ CLONANDO --------------------\n";
      $cmd = self::GIT_BIN . " clone http://" . $Config->GitUser . ":" . $Config->GitKey . "@" . $Config->GitUrl . "/e-cidade/" . $Versao.".git " . $this->Path;
      Bash::exec( $cmd );

      $cmd = self::GIT_BIN . " clone http://" . $Config->GitUser . ":" . $Config->GitKey . "@" . $Config->GitUrl . "/EcidadePlugins/extension.git " . $this->Path . "/extension";
      Bash::exec( $cmd );
      $cdir = getcwd();
      chdir( $this->Path . "/extension" );
      FileTools::checkDir( $this->Path . "/extension/log" );
      exec( self::COMPOSER_BIN . " install --no-dev" );
      echo "------------ INSTALANDO EXTENSÃO DESKTOP --------\n";
      exec( "bin/modification/unpack package/v3-install/modifications/003.xml");
	    exec( "bin/modification/install dbportal-v3-install-3" );
      exec( "bin/extension/pack Desktop" );
      exec( "bin/extension/unpack desktop-package.tar.gz" );
      exec( "bin/extension/install Desktop" );

      chdir( $cdir );

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
      $this->iniFolders();
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
