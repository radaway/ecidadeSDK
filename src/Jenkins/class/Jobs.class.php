<?php
require_once __DIR__ . '/../config/config.php';
class Jobs{

  private $JobName;
  private $Parametes = array();

  public function __construct( $JobName ){
    $this->JobName = $JobName;
    $Config = new JenkinsConfig();
    if ( ! is_file( __DIR__ . '/../jenkins-cli.jar' ) ){
      if ( ! file_put_contents( __DIR__ . '/../jenkins-cli.jar', file_get_contents( 'http://' . $Config->JenkinsHost . '/jnlpJars/jenkins-cli.jar' ) ) ){
        throw new Exception("Não encontrou jenkins-cli.jar!");
      }
    }
  }

  public function addParameter( $Key, $Value){
    $Key = trim( $Key );
    $this->Parametes[$Key] = $Value;
  }

  public function build(){
    $Config = new JenkinsConfig();
    $cmd = 'java -jar '. __DIR__ . '/../jenkins-cli.jar -s ';
    $cmd .= 'http://' . $Config->JenkinsHost . ' build ' . $this->JobName;
    $cmd .= ' --username ' . $Config->JenkinsUser . ' --password "' . $Config->JenkinsKey . '" ';

    foreach ($this->Parametes as $key => $value) {
      $cmd .= ' -p ' . $key . '="' . $value . '"';
    }
    exec($cmd, $out);
    return $out;
  }
}
?>
