<?php
require_once __DIR__  . '/FileTools.class.php';
require_once __DIR__  . '/ArrayTools.class.php';
Class CvsCheckout{

  const BUILD_DIR = '/tmp/CvsCheckout_tmp';

  const CVS_BIN = "/usr/bin/cvs";
  const CUT_BIN = "/usr/bin/cut";
  const SED_BIN = "/bin/sed";
  const RM_BIN  = "/bin/rm";
  const FIND_BIN = "/usr/bin/find";
  const EGREP_BIN = "/bin/egrep";

  private $CvsRoot = null;
  private $CheckoutList = array();

  //

  function __construct( $CvsRoot, $User, $Password ){
    try {
      FileTools::checkDir( self::BUILD_DIR );
    } catch (Exception $e) {
      throw $e;
    }
    $CvsType = ":pserver:" . $User . ":" . $Password . "@";
    if ( ! filter_var('http://' . $CvsRoot, FILTER_VALIDATE_URL) ){
      $CvsType = ":local:";
    }
    $this->CvsRoot =  $CvsType  . $CvsRoot;
    putenv("HOME=" . self::BUILD_DIR );
    putenv("CVSROOT=" . $this->CvsRoot);
  }

  private function getBasePath(){
    $BasePath = explode( ":", $this->CvsRoot );
    $BasePath = end( $BasePath );
    $BasePath = trim( $BasePath );
    if( $BasePath{0} != "/" ){
      for ( $i = 0; $i < strlen( $BasePath ); $i++ ){
        if ( $BasePath{$i} == "/" ){
          $Tmp_BasePath = substr( $BasePath, $i, strlen( $BasePath ) - $i  );
          break;
        }
      }
      if( $i == strlen( $BasePath ) ){
        throw new Exception("Falha ao ajustar Base Path", 1);
      }
      $BasePath = $Tmp_BasePath;
    }
    return $BasePath;
  }

  //

  private function executarShell( $cmd ){
    if ( ! exec( self::CVS_BIN . " login && " . $cmd, $retorno, $erro ) ){
      throw new Exception("Falhou en executar: " . $cmd , 1);
    }
    if ( $erro > 0 ){
      throw new Exception("Saída d erro: -> " . $erro . " | Mensagem -> " . $retorno[0], 1);
    }
    return $retorno;
  }

  private function getFileList( $Modulo, $Tags ){
    $Lista = array();
    foreach ($Tags as $Tag) {
      try {
        $tmp = self::executarShell( self::CVS_BIN . " -Q -q rlog -R -N -S -r" . trim( $Tag ) . " " . trim( $Modulo ) . " | " . self::CUT_BIN . " -d ',' -f 1 | " . self::SED_BIN . " 's|" . self::getBasePath() . "/" . $Modulo .  "/||g'");
        $Lista = array_merge( $Lista, $tmp );
      } catch (Exception $e) {
        throw $e;
      }
    }
    $ReturnLista = array();
    foreach ($Lista as  $arquivo) {
      if ( preg_match('/Logging in to :/', $arquivo ) ){
        continue;
      }
      if ( in_array( $arquivo, $ReturnLista ) ){
        continue;
      }
      $ReturnLista[] = $arquivo;
    }
    return $ReturnLista;
  }

  function getGrepTagsString( $Tags ){
    $TagsGrepString = "";
    foreach ($Tags as $Tag) {
      $TagsGrepString .= trim( $Tag ) . "|";
    }
    $TagsGrepString = substr( $TagsGrepString, 0, strlen( $TagsGrepString ) - 1  );
    return $TagsGrepString;
  }

  function sortRevisionList( $arquivo, $Revision ){
    $RevisionList = array();
    foreach ($Revision as $rev) {
      if ( preg_match('/Logging in to :/', $rev ) ){
        continue;
      }
      $tmp_array = array();
      $tmp_array["file"] = trim( $arquivo );
      $tmp = explode( ":", trim( $rev ) );
      $tmp_array['tag'] = reset( $tmp );
      $tmp_array['revision'] = end( $tmp );
      $RevisionList[] = $tmp_array;
    }
    $RevisionList = ArrayTools::array_orderby( $RevisionList, 'revision', SORT_DESC );
    return $RevisionList;
  }

  private function buildTagRevisionList( $Modulo, $Tags ){
    if ( trim( $Modulo ) == "" ){
      throw new Exception("Não foi informado módulo!", 1);
    }
    if ( ! is_array( $Tags ) ){
      throw new Exception("Tags devem ser um array!", 1);
    }
    $this->CheckoutList = array();
    foreach (self::getFileList( $Modulo, $Tags ) as  $arquivo) {
      $cmd = self::CVS_BIN . " -Q -q rlog " . trim( $Modulo ) . "/" . trim( $arquivo ) . " | " . self::EGREP_BIN;
      $cmd .= " '[A-Za-z0-9]: [0-9].[0-9]{1,5}' | " . self::EGREP_BIN . " -v date | " . self::EGREP_BIN;
      $cmd .= " -v head | " . self::SED_BIN . " 's| ||g' | " . self::EGREP_BIN . " '(" . self::getGrepTagsString( $Tags ) . ")'";
      try {
        $Revision = self::executarShell( $cmd );
      } catch (Exception $e) {
        throw $e;
      }
      $this->CheckoutList = array_merge( $this->CheckoutList, self::sortRevisionList( $arquivo, $Revision ) );
    }
    if( empty( $this->CheckoutList ) ){
      throw new Exception("Nenhum arquivo encontrado para TAG:"  . self::getGrepTagsString( $Tags ) . " Do Modulo:" . $Modulo , 1);
    }
  }


  private function checkoutFileByRevision( $Modulo, $File, $Revision, $Dir ){
    $SavePath = getcwd();
    $FilePath = pathinfo( $Dir . "/" . $File );
    $checkoutDir = explode( '/', $FilePath['dirname'] );
    $checkoutDir = end( $checkoutDir );
    try {
      FileTools::checkDir( $FilePath['dirname'] );
    } catch (Exception $e) {
      throw $e;
    }
    chdir( $FilePath['dirname'] . "/.." );
    try {
      $cmd = self::CVS_BIN . " checkout -P -r " . $Revision . " -d " . $checkoutDir . " " . $Modulo . "/" . $File;
      self::executarShell( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
    chdir( $SavePath );
  }

  public function checkout( $Modulo, $Dir, $Tag = null ){
    if ( ! isset( $Modulo ) ) {
      throw new Exception("Informe o Modulo!", 1);
    }
    $Rtag = " ";
    if ( $Tag != null ){
      $Rtag = " -r " . $Tag . " ";
    }
    try {
      FileTools::rmDIr( $Dir );
      FileTools::checkDir( $Dir );
      $SavePath = getcwd();
      $dir_info = pathinfo( $Dir );
      chdir( $dir_info['dirname'] . "/" . $dir_info['basename'] . "/.." );
      self::executarShell( self::CVS_BIN . " -q checkout" . $Rtag . "-d '" . $dir_info['basename'] . "' " . $Modulo );
      chdir( $SavePath );
      $cmd = 'for remove in $(' . self::FIND_BIN . ' "' . $Dir . '" -type d -name "CVS"); do ' . self::RM_BIN . ' -rf $remove; done';
      self::executarShell( $cmd );
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function GetFilesByTags( $Modulo, $Dir, $Tags ){
    $CheckoutList = array();
    try {
      FileTools::checkDir( $Dir );
      self::buildTagRevisionList( $Modulo, $Tags );
    } catch (Exception $e) {
      throw $e;
    }

    $SaveCheckoutFile = "";
    foreach ($this->CheckoutList as $CheckoutFile) {
      if ( $SaveCheckoutFile != $CheckoutFile['file'] ){
        echo "Executando checkout: " . $CheckoutFile['file'] . " -- Revisão: " . $CheckoutFile['revision'] . " -- TAG: " . $CheckoutFile['tag'] . "\n";
        try {
          if ( file_exists( $Dir . "/" . $CheckoutFile['file'] ) ){
            unlink( $Dir . "/" . $CheckoutFile['file'] );
          }
          self::checkoutFileByRevision( $Modulo, $CheckoutFile['file'], $CheckoutFile['revision'], $Dir );
        } catch (Exception $e) {
          throw $e;
        }
        $SaveCheckoutFile = $CheckoutFile['file'];
        continue;
      }
      echo "Aviso! Arquivo " . $CheckoutFile['file'] . " -- Revisão:" . $CheckoutFile['revision'] . " -- TAG:" . $CheckoutFile['tag'] . " Não executado checkout!\n";
    }
    try {
      $cmd = 'for remove in $(' . self::FIND_BIN . ' "' . $Dir . '" -type d -name "CVS"); do ' . self::RM_BIN . ' -rf $remove; done';
      self::executarShell( $cmd );
    } catch (Exception $e) {
      throw $e;

    }

  }


}

?>
