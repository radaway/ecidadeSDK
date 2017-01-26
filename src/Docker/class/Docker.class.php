<?php
require_once __DIR__ . '/../../Ssh/class/Ssh.class.php';
require_once __DIR__ . '/../../Ssh/config/config.php';

class Docker{

  public function status( $port ){
    $Config = new SshConfig();
    $ssh = new Ssh( $Config->SshHost, $Config->SshPort, $Config->SshUser, $Config->SshPass );
    $cmd = "docker ps | grep '0.0.0.0:" . $port . "' | awk '{print \$1}' | head -n 1";

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao buscar informações do docker", 1);
    }

    if ( $ssh->out == "" ){
      return false;
    }

    $cmd = "docker ps | grep '0.0.0.0:" . $port . "' | grep Up | awk '{print \$1}' | head -n 1";

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao buscar informações do docker", 1);
    }

    if ( $ssh->out != "" ){
      return true;
    }
    return false;
  }

  public function start( $docker, $dir, $port ){
    $Config = new SshConfig();
    $ssh = new Ssh( $Config->SshHost, $Config->SshPort, $Config->SshUser, $Config->SshPass );

    $cmd = "docker images | grep '" . $docker . "' | awk '{print \$1}' | head -n 1";

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao buscar informações do docker", 1);
    }

    if ( $ssh->out == "" ){
      throw new Exception("Não encontrou docker informado!", 1);
    }

    $cmd = "if [ -d '" . $dir . "' ]; then echo 1; else echo 0; fi";

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao buscar informações do diretorio", 1);
    }

    if ( $ssh->out == "" OR $ssh->out == 0 ){
      throw new Exception("Não encontrou o diretório informado!", 1);
    }

    $cmd = "netstat -anp | grep ':" . $port . "'";

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao buscar informações da porta", 1);
    }

    if ( $ssh->out != "" ){
      throw new Exception("Porta informada já está em uso!", 1);
    }

    $cmd = "docker run -t -d -v " . $dir . ":/var/www/html -p " . $port . ":80 " . $docker . " /root/scripts/start.sh";

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao iniciar docker", 1);
    }

    try {
      $estado = self::status( $port );
    } catch (Exception $e) {
      throw new Exception( $e->getMessage() , 1 );
    }

    if ( $estado ){
      return true;
    }
    return false;
  }

  public function stop( $port ){
    $Config = new SshConfig();
    $ssh = new Ssh( $Config->SshHost, $Config->SshPort, $Config->SshUser, $Config->SshPass );

    $cmd = "docker ps -a | grep '0.0.0.0:" . $port .  "' | head -n 1 | awk '{print $1}'";

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao buscar informações do docker", 1);
    }

    $docker_id = trim( $ssh->out );

    if ( $docker_id == "" ){
      return true;
    }

    $cmd = "docker kill " . $docker_id;

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao encerrar docker", 1);
    }

    $cmd  = "docker rm " . $docker_id;

    if ( ! $ssh->exec( $cmd ) ){
      throw new Exception("Falhou ao encerrar docker", 1);
    }

    try {
      $estado = self::status( $port );
    } catch (Exception $e) {
      throw new Exception( $e->getMessage() , 1 );
    }

    if ( ! $estado ){
      return true;
    }
    return false;
  }

}
?>
