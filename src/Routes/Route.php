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
    $PathInfo = pathinfo($_SERVER['SCRIPT_NAME']);
    if( strlen( $PathInfo['dirname'] ) > 1 ){
        $urlRequest = str_replace( $PathInfo['dirname'], '', $urlRequest );
    }
    if( strlen( $urlRequest ) > 1 ){
        $urlRequest = rtrim($urlRequest, '/');
    }
    foreach ($this->_url as $key => $value) {
      if( preg_match("#\d*$value$#", $urlRequest) ){
        require_once __DIR__ . '/' . $this->_method[$key] . '/'. $this->_method[$key] .'.php';
        new $this->_method[$key]();
        return false;
      }

      if ( preg_match( "#:#", $value ) ){
        $tmpString = preg_split( "#:[0-9]#", $value );
        $pregString = "";
        foreach ($tmpString as $val) {
          $val = rtrim($val, '/');
          if ( $val == '' ){
            $pregString .= '/*';
            continue;
          }
          $pregString .= $val;
        }        
        if( preg_match("#$pregString#", $urlRequest) ){
          $pregString = explode('/', $urlRequest );
          require_once __DIR__ . '/' . $this->_method[$key] . '/'. $this->_method[$key] .'.php';
          new $this->_method[$key]( $pregString[2] );
          return false;
        }
      }
    }
    echo "bugou" . strlen($urlRequest) . $urlRequest ;
    print_r(pathinfo($_SERVER['SCRIPT_NAME']));
  }
}

?>
