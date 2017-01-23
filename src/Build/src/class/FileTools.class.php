<?php
require_once  __DIR__ . '/Bash.class.php';
Class FileTools{

  const SED_BIN = "/bin/sed";

  public static function rmDIr( $Dir ){

    if ( is_array( $Dir ) ){
      foreach ($Dir as $dir) {
        self::rmDir( $dir );
      }
      return true;
    }

    if ( ! is_dir( $Dir ) ){
      return;
    }
    $files = array_diff( scandir( $Dir ), array( '.', '..' ) );
    foreach ($files as $file) {
      if ( is_dir( $Dir . "/" . $file ) ) {
        self::rmDIr( $Dir . "/" . $file );
        continue;
      }
      unlink("$Dir/$file");
    }
    return rmdir($Dir);
  }

  public static function checkDIr( $Dir ){
    if ( is_array( $Dir ) ){
      foreach ($Dir as $dir) {
        self::checkDIr( $dir );
      }
      return true;
    }

    if ( ! is_dir( $Dir ) ){
      if ( ! mkdir( $Dir, 0777, true ) ){
        throw new Exception("Verifique permissões do diretório:" . $Dir , 1);
      }
    }
    if ( ! is_writable( $Dir ) ){
      throw new Exception("Verifique permissões do diretório:" . $Dir , 1);
    }
  }

  public static function compressFolder( $Dir, $OutPutFile ){
    try {
      $pd = new PharData( $OutPutFile );
      $pd->buildFromDirectory( $Dir );
    } catch (PharException $e) {
      throw new Exception( $e->getMessage() , 1);
    }
  }

  public static function getMd5Sum( $File ){
    return md5_file( $File );
  }

  public static function recursive_copy( $src , $dst ) {
    if ( ! is_dir( $src ) ){
      throw new Exception("Origem informada não é um diretorio", 1);
    }
    $dir = opendir( $src );

    self::checkDIr( $dst );

    while(false !== ( $file = readdir($dir)) ) {
      if (( $file != '.' ) && ( $file != '..' )) {
        if ( is_dir($src . '/' . $file) ) {
          self::recursive_copy($src . '/' . $file,$dst . '/' . $file);
        }
        else {
          copy($src . '/' . $file,$dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

  public static function strReplace( $File, $OldData, $NewData ){
    if ( ! is_file( $File ) ){
      throw new Exception("Não é um arquivo válido", 1);
    }
    $OldData = str_replace( "'", "\\x27", $OldData );
    $NewData = str_replace( "'", "\\x27", $NewData );
    $cmd = self::SED_BIN . " -i 's|" . $OldData . "|" . $NewData . "|g' " . $File;
    exec( $cmd, $retorno, $erro );
  }

  public static function tee( $File, $String ){
    $String = str_replace( '"', '\"', $String );
    $cmd = 'echo "' . $String . '" | tee -a ' . $File;
    try {
      Bash::exec( $cmd );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 1);
    }

  }

}
?>
