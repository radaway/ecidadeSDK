<html>
<head>
  <title></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="Expires" CONTENT="0">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link type="text/css" href="css/bootstrap.min.css" rel="stylesheet" media="screen"  />
  <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.js"></script>
</head>
<body>
<div style="width: 98%;" class="container" id="exibicao" align="center"></div>

<!-- exibe aba -->
<script type="text/javascript">
  function exibir(load_url){
    $("#exibicao").html('<br /><div align="center">Carregando!!!</div>');
    $.ajax({
      type: "get",
 		  url: load_url,
 		  success: function(html){
 		     $("#exibicao").html(html);
 		  }
 		});
  }
</script>

<!-- carga inicial -->
<script type="text/javascript">
	$(document).ready( function() {
		  exibir('ambientes/ambientes.php');
  });
</script>
</body>
</html>
