<?php

class Route{

  private $_url = array();
  private $_method = array();

  public function add( $url, $method = null ){
    $this->_url[] = trim( $url );
    if ( $method != null ){
      $this->_method[] = $method;
    }
  }

  public function get(){
    $urlRequest = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/';
    foreach ($this->_url as $key => $value) {
      if( preg_match("#\d*$value$#", $urlRequest) ){
        require_once __DIR__ . '/' . $this->_method[$key] . '/'. $this->_method[$key] .'.php';
        new $this->_method[$key]();
        return false;
      }
    }
    echo "bugou";
  }
}

?>
