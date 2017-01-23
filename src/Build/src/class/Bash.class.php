<?php
Class Bash{
  public static function exec( $cmd ){
    if ( ! exec( $cmd, $retorno, $erro ) ){
      if ( $erro > 0 ){
        throw new Exception("Saída d erro: -> " . $erro . " | Mensagem -> " . $retorno[0], 1);
      }
    }    
    return $retorno;
  }
}
?>
