<?php
require_once __DIR__ . '/../interface/Project.interface.php';
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/Bash.class.php';
require_once __DIR__  . '/CvsCheckout.class.php';

Class EcidadeOnline2Project implements Project{

  const COMPOSER_BIN = "/usr/local/bin/composer";

  const GIT_BIN = "/usr/bin/git";

  private $Path = null;
  private $Versao = null;

  function __construct( $Path ){
    $this->Path = $Path;
  }

  public function buildVersion( $Versao ){
    try {
      FileTools::rmDIr( $this->Path );
      FileTools::checkDir( $this->Path );
      $Config = new GitLabConfig();
      $cmd = self::GIT_BIN . " clone http://infra.libresolucoes:halegria@" . $Config->GitUrl . "/e-cidadeonline2/" . $Versao.".git " . $this->Path;
      echo "Executando:" . $cmd . "\n";
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function checkoutTag( $Tags ){
    $cmd = self::GIT_BIN . " checkout " . $Tag;
    try {
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }


  public function init(){

    try {

      FileTools::tee( $this->Path . "/application/configs/application.ini.dist", '\n[development : production]' );
      FileTools::tee( $this->Path . "/application/configs/application.ini.dist", 'phpSettings.display_errors = 1' );
      FileTools::tee( $this->Path . "/application/configs/application.ini.dist", 'phpSettings.display_startup_errors = 1' );
      FileTools::tee( $this->Path . "/application/configs/application.ini.dist", 'resources.frontController.params.displayExceptions = 1' );

      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'webservice.cliente.user[ \s]*=[ \s].*', 'webservice.cliente.user = "c4ca4238a0b923820dcc509a6f75849b"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'settings.application.cache[ \s]*=[ \s].*', 'settings.application.cache = "' . $this->Versao . '"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'ecidadeonline2.versao[ \s]*=[ \s].*', 'ecidadeonline2.versao = "' . $this->Versao . '"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'resources.mail.transport.host[ \s]*=[ \s].*', 'resources.mail.transport.host = "smtp.gmail.com"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'resources.mail.transport.password[ \s]*=[ \s].*', 'resources.mail.transport.password = "1234nfreleases"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'resources.mail.transport.port[ \s]*=[ \s].*', 'resources.mail.transport.port = "587"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'resources.mail.transport.username[ \s]*=[ \s].*', 'resources.mail.transport.username = "nfreleases@dbseller.com.br"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'resources.mail.defaultFrom.email[ \s]*=[ \s].*', 'resources.mail.defaultFrom.email = "nfreleases@dbseller.com.br"' );
      FileTools::strReplace( $this->Path . "/application/configs/application.ini.dist", 'resources.mail.defaultFrom.name[ \s]*=[ \s].*', 'resources.mail.defaultFrom.name = "NFS-e DESENVOLVIMENTO"' );

      FileTools::strReplace( $this->Path . "/application/configs/webservice-ecidade.ini.dist", 'DB_id_usuario[ \s]*=[ \s].*', 'DB_id_usuario = "1";' );
      FileTools::strReplace( $this->Path . "/application/configs/webservice-ecidade.ini.dist", 'DB_login[ \s]*=[ \s].*', 'DB_login = "dbseller";' );
      FileTools::strReplace( $this->Path . "/application/configs/webservice-ecidade.ini.dist", 'DB_ip[ \s]*=[ \s].*', 'DB_ip = "127.0.0.1";' );
      FileTools::strReplace( $this->Path . "/application/configs/webservice-ecidade.ini.dist", 'SERVER_ADDR[ \s]*=[ \s].*', 'SERVER_ADDR = "127.0.0.1";' );
      FileTools::strReplace( $this->Path . "/application/configs/webservice-ecidade.ini.dist", 'SERVER_PORT[ \s]*=[ \s].*', 'SERVER_PORT = "80";' );
      FileTools::strReplace( $this->Path . "/application/configs/webservice-ecidade.ini.dist", 'SERVER_ADMIN[ \s]*=[ \s].*', 'SERVER_ADMIN = "infra@dbseller.com.br";' );
      FileTools::strReplace( $this->Path . "/application/configs/webservice-ecidade.ini.dist", 'HTTP_HOST[ \s]*=[ \s].*', 'HTTP_HOST = "localhost";' );

    } catch (Exception $e) {
      throw $e;
    }

    if ( ! copy( $this->Path . "/application/configs/application.ini.dist", $this->Path . "/application/configs/application.ini" ) ){
      throw new Exception("Falhou ao inicializar application/configs/application.ini", 1);
    }
    if ( ! copy( $this->Path . "/application/configs/webservice-ecidade.ini.dist", $this->Path . "/application/configs/webservice-ecidade.ini" ) ){
      throw new Exception("Falhou ao inicializar application/configs/webservice-ecidade.ini", 1);
    }

    if ( is_file( $this->Path . "/public/webservice/wsdlValidations/producao/modelo1.wsdl.dist" ) ){
      if( ! copy( $this->Path . "/public/webservice/wsdlValidations/producao/modelo1.wsdl.dist", $this->Path . "/public/webservice/wsdlValidations/producao/modelo1.wsdl" ) ){
        throw new Exception("Falhou ao inicializar public/webservice/wsdlValidations/producao/modelo1.wsdl", 1);
      }
    }

    if ( is_file( $this->Path . "/public/webservice/wsdlValidations/modelo1.wsdl.dist" ) ){
      if( ! copy( $this->Path . "/public/webservice/wsdlValidations/modelo1.wsdl.dist", $this->Path . "/public/webservice/wsdlValidations/modelo1.wsdl" ) ){
        throw new Exception("Falhou ao inicializar public/webservice/wsdlValidations/modelo1.wsdl", 1);
      }
    }

    if ( ! copy( $this->Path . "/public/webservice/wsdlValidations/homologacao/modelo1.wsdl.dist", $this->Path . "/public/webservice/wsdlValidations/homologacao/modelo1.wsdl.dist" ) ){
      throw new Exception("Falhou ao inicializar public/webservice/wsdlValidations/homologacao/modelo1.wsdl", 1);
    }

    if ( ! copy( $this->Path . "/public/webservice/wsdlValidations/integracao/modelo1.wsdl.dist", $this->Path . "/public/webservice/wsdlValidations/integracao/modelo1.wsdl" ) ){
      throw new Exception("Falhou ao inicializar public/webservice/wsdlValidations/integracao/modelo1.wsdl", 1);
    }

    try {
      FileTools::rmDir( $this->Path . "/application/data/Proxy" );
      FileTools::checkDir( $this->Path . "/application/data/Proxy" );
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
