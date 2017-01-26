<?php

class BuildManager{

  public function __construct( $buildName ){
    if (isset( $_POST['view'] )){
      if ( is_file( __DIR__ . "/View/" . $_POST['view'] .".php" ) ){
        require_once __DIR__ . "/View/" . $_POST['view'] .".php";
        return new $_POST['view']( $buildName,  $_POST['method'] );
      }
    }
    if (isset( $_POST['ctrl'] )){
      if ( is_file( __DIR__ . "/Controller/" . $_POST['ctrl'] .".php" ) ){
        require_once __DIR__ . "/Controller/" . $_POST['ctrl'] .".php";
        return new $_POST['ctrl']( $buildName, $_POST['method'] );
      }
    }
    echo $this->indexPage();
  }

  private function indexPage(){
    $html = '<html>
      <head>
        <title>e-cidade SDK</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="Expires" CONTENT="0">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/font-awesome.min.css">
        <link type="text/css" href="/css/bootstrap.min.css" rel="stylesheet" media="screen"  />
        <script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.js"></script>
      </head>
      <body>
        <div style="width: 98%;" class="container" id="exibicao" align="center"></div>
        <script type="text/javascript">
          function LoadView(View, Method){
            $("#exibicao").html(\'<br /><div align="center">Carregando!!!</div>\');
            $.ajax({
              data: {view: View, method: Method},
              type: "POST",
         		  url: "' . $_SERVER['REQUEST_URI'] . '",
         		  success: function(html){
         		     $("#exibicao").html(html);
         		  }
         		});
          }
        </script>
        <script type="text/javascript">
        	$(document).ready( function() {
        		  LoadView(\'BuildInfo\', \'info\');
          });
        </script>
      </body>
    </html>';
    return $html;
  }

}

 ?>
