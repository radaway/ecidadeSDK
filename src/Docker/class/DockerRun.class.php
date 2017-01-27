<?php

require_once __DIR__ . '/../config/config.php';

class DockerRun{

  private $config;
  private $jsonPost;
  private $image = null;
  private $jsonPort = array();
  private $jsonPortB = array();
  private $jsonVol = array();
  private $jsonCmd = array();
  private $dns = array();
  private $dnsSearch = null;
  private $containerId = null;

  public function __construct( $dockerImage ) {
    $this->config = new DockerConfig();
    $this->image = $dockerImage;
  }

  public function addCmd( $cmd = "/bin/bash" ){
    $this->jsonCmd[] = $cmd ;
  }

  public function bindPort( $hostPort, $dockerPort, $trasp = "tcp" ){
    $tmpArr[] = array( "HostIP" => "0.0.0.0", "HostPort" => $hostPort  );
    $this->jsonPort[$dockerPort . "/" . $trasp] =  $tmpArr;
    $this->jsonPortB = array( $dockerPort . "/" . $trasp => new stdClass() );
  }

  public function addVolume( $volOrig, $volDest ){
    $this->jsonVol[] = $volOrig . ":" . $volDest;
  }

  public function addDns( $dns ){
    $this->dns[] = $dns;
  }

  public function addDnsSearch( $name ){
    $this->dnsSearch[] = $name;
  }

  private function makeConfig(){
    $this->jsonPost = array();
    $this->jsonPost['Image'] = $this->image;
    $this->jsonPost['Tty'] = true;
    $this->jsonPost['AttachStdin'] = true;
    $this->jsonPost['Cmd'] = $this->jsonCmd;
    $this->jsonPost['ExposedPorts'] = $this->jsonPortB;
    $tpmHostConfigjsonPost['Binds'] = $this->jsonVol;
    $tpmHostConfigjsonPost['PortBindings'] = $this->jsonPort;
    $tpmHostConfigjsonPost['Dns'] = $this->dns;
    $tpmHostConfigjsonPost['DnsSearch'] = $this->dnsSearch;
    $tpmHostConfigjsonPost['PublishAllPorts'] = true;
    $this->jsonPost['HostConfig'] = $tpmHostConfigjsonPost;
  }

  private function sendApi(){
    $this->makeConfig();
    $data_string = json_encode($this->jsonPost);    
    $ch = curl_init($this->config->socket . '/containers/create');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data_string) )
    );
    $result = curl_exec($ch);
    print_r( json_decode( $result ) );
    return $result;
  }

  private function startDocker(){
    $contId = $this->sendApi();
    $contId = json_decode( $contId );
    $this->containerId = $contId->Id;
    $jsonStart = array();
    //$jsonStart['Id'] =  $this->containerId;
    //$jsonStart['PortBindings'] = $this->jsonPort;
    //$data_string = json_encode($jsonStart);
    $url = $this->config->socket . '/containers/' . $contId->Id . '/start';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, null);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json', )
    );
    $result = curl_exec($ch);
    return $result;
  }

  public function dockerRun(){
    $this->startDocker();
    return $this->containerId;
  }

}
?>
