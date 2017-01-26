<?php

class BuildInfo{

    private $Size = 10;

    public function __construct( $BuildName, $Method ){
      switch ($Method) {
        case 'info':
          echo $this->info( $BuildName );
          break;
        default:
          echo "OPCAO INVALIDA";
          break;
      }
    }


    private function info( $BuildName ){
      $html = '<div class="container" align="center">
      <br /><div class="col-' . $this->Size . '">
      ';

      $lsDir = array_diff( scandir( '/var/www/builds/' . $BuildName . '/builds' ), array( '.', '..' ) );
      foreach ($lsDir as $value) {
        if ( ! is_dir( '/var/www/builds/' . $BuildName . '/builds/' . $value ) ){
          continue;
        }
        $html .= '<div class="card card-default">
        <div class="card-header">
        ';
        $html .= '<h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#' . $value . '" class="">
        ' . $value . '</a>
        </h4
        </div>
        <div class="card-body"></div></div>';
      }
      $html .= '</div></div>';
      return $html;

    }

}

 ?>
