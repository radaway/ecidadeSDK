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
      $html = '<div align="center">
      <br /><div class="col-' . $this->Size . '">
      ';

      $lsDir = array_diff( scandir( '/var/www/builds/' . $BuildName . '/builds' ), array( '.', '..' ) );
      foreach ($lsDir as $value) {
        if ( ! is_dir( '/var/www/builds/' . $BuildName . '/builds/' . $value ) ){
          continue;
        }
        if( is_file( '/var/www/builds/' . $BuildName . '/builds/' . $value . '_lock') ){
          continue;
        }
        $html .= '<div class="card card-default">
        <div class="card-header">
        ';
        $html .= '<h4 class="card-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#' . $value . '" class="">
        ' . $value . '</a>
        </h4 >
        </div><div id="' . $value . '" class="panel-collapse collapse">
        <div class="card-body">' . $value . '</div></div></div><br />';
      }
      $html .= '</div></div>';
      $script = '<script type="text/javascript">
      $(function(){
        $(\'a[data-toggle="collapse"]\').on(\'click\',function(){
				var objectID=$(this).attr(\'href\');
				if($(objectID).hasClass(\'in\')){
          $(objectID).collapse(\'hide\');
				}else{
          $(objectID).collapse(\'show\');
          LoadInfo( objectID.substr(1), \'getInfo\', objectID );
				}
        });
		  });
    </script>
    <script type="text/javascript">
      function LoadInfo(View, Method, objectID){
        $(objectID).html("<br /><div align=\"center\">Carregando!!!</div>");
        $.ajax({
          data: {view: View, method: Method},
          type: "POST",
     		  url: "' . $_SERVER['REQUEST_URI'] . '",
     		  success: function(html){
     		     $(objectID).html(html);
     		  }
     		});
      }
    </script>';
      return $html . $script;
    }

}

 ?>
