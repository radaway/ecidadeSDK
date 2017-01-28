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
    if ( ! exec($cmd, $out, $erro) ){
      if ( $erro > 0 ){
          throw new Exception("Falha na requisição", 1);
      }
    }
    return true;
  }

  function getJobs(){
    $Config = new JenkinsConfig();
    $job_output = array();
    $job_url = 'http://' . $Config->JenkinsHost . '/job/' .  $this->JobName . '/api/json';
    $job_content = json_decode( file_get_contents( $job_url ) );
    foreach ( $job_content->builds as $build ) {
      $build_url = $build->url . 'api/json?depth=2';
      $build_content = json_decode( file_get_contents( $build_url ) );

      foreach ($build_content->actions as $action) {
        if (isset($action->parameters) && count($action->parameters)) {
          $job_param = array();
          foreach ($action->parameters as $param) {
            $job_param[$param->name] = $param->value;
          }
          $job_output[$build->number]['params'] = $job_param;
        }
      }

    }
    return $job_output;
  }

}
?>
