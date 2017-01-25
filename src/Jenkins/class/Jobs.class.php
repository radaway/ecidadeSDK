<?php
require_once __DIR__ . '/../config/config.php';
class Jobs{

  private $JobName;
  private $Parameters = array();

  public function __construct( $JobName ){
    $this->JobName = $JobName;
  }

  public function addParameter( $Key, $Value){
    $Key = trim( $Key );
    $this->Parameters[$Key] = $Value;
  }

  public function build(){
    $Config = new JenkinsConfig();
    $url = 'http://' . $Config->JenkinsHost . '/job/' . $this->JobName . '/buildWithParameters';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->Parameters));
    // $output contains the output string
    $output = curl_exec($ch);
    // close curl resource to free up system resources
    curl_close($ch);
    return $output;
  }
}
?>
