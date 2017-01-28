<?php
class DockerJsonCreate{

private $json = null;
private $jsonPort = array();
private $jsonPortB = array();
private $jsonVol = array();
private $jsonCmd = array();
private $dns = array();
private $dnsSearch = null;
private $image;


public function __construct( $image ){
  $this->image = $image;
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
  $this->json = array();
  $this->json['Image'] = $this->image;
  $this->json['Tty'] = true;
  $this->json['AttachStdin'] = true;
  $this->json['Cmd'] = $this->jsonCmd;
  $this->json['ExposedPorts'] = $this->jsonPortB;
  $tpmHostConfigjsonPost['PortBindings'] = $this->jsonPort;
  $tpmHostConfigjsonPost['Binds'] = $this->jsonVol;
  $tpmHostConfigjsonPost['Dns'] = $this->dns;
  $tpmHostConfigjsonPost['DnsSearch'] = $this->dnsSearch;
  $tpmHostConfigjsonPost['PublishAllPorts'] = true;
  $this->json['HostConfig'] = $tpmHostConfigjsonPost;
}

public function getArray(){
  $this->makeConfig();
  return $this->json;
}

public function getJson(){
  $this->makeConfig();
  return json_encode( $this->json );
}

}

 ?>
