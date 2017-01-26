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
        $html .= '<h4 class="card-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#' . $value . '" class="">
        ' . $value . '</a>
        </h4 >
        </div><div id="' . $value . '" class="panel-collapse collapse">
        <div class="card-body">' . $value . '</div></div></div>';
      }
      $html .= '</div></div>';
      $script = '<script type="text/javascript">
      $(function () {
                    $(\'a[data-toggle="collapse"]\').on(\'click\',function(){

				var objectID=$(this).attr(\'href\');

				if($(objectID).hasClass(\'in\'))
				{
                                    $(objectID).collapse(\'hide\');
				}

				else{
                                    $(objectID).collapse(\'show\');
				}
                    });


                    $(\'#expandAll\').on(\'click\',function(){

                        $(\'a[data-toggle="collapse"]\').each(function(){
                            var objectID=$(this).attr(\'href\');
                            if($(objectID).hasClass(\'in\')===false)
                            {
                                 $(objectID).collapse(\'show\');
                            }
                        });
                    });

                    $(\'#collapseAll\').on(\'click\',function(){

                        $(\'a[data-toggle="collapse"]\').each(function(){
                            var objectID=$(this).attr(\'href\');
                            $(objectID).collapse(\'hide\');
                        });
                    });

		});
    </script>';
      return $html . $script;

    }

}

 ?>
