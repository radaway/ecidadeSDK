<?php
Class HtmlForm{

  private $Html;
  private $Name;
  private $Titulo;
  private $Size;
  private $Head;
  private $Script;

  public function __construct( $Name, $Size = 8 ){
    $this->Html = null;
    $this->Name = $Name;
    $this->Titulo = null;
    $this->Size = $Size;
    $this->Head = false;
    $this->Script = "";
  }

  private function initForm(){
    if ( $this->Html != null ){return true;}
    $this->Html = '<div class="container" align="center">
    <br /><div class="col-' . $this->Size . '">
    ';
    $this->Html .= '<div class="alert alert-info" id="return_html_' . $this->Name;
    $this->Html .= '" style="display: none;"></div>
    ';
    $this->Html .= '<form name="form_' . $this->Name . '" id="form_' . $this->Name . '">
    ';
    return true;
  }

  private function getLabel( $Label, $Size = 3 ){
    return '<label for="example-text-input" class="col-' . $Size . ' col-form-label">' . $Label . '</label>
    ';
  }

  private function getInputBase( $BaseType, $Name, $Size = 6 ){
    return '<div class="col-' . $Size . '">
    <input class="form-control" type="' . $BaseType . '" name="' . $Name . '" id="' . $Name . '" />
    </div>
    ';
  }

  private function getSelectBase( $Name, $Values, $Size = 6, $Multiple = false ){
    $Mult = " ";
    if ( $Multiple ){
      $Mult = " multiple ";
    }
    $Retorno = '<div class="col-' . $Size . '">
    ';
    $Retorno .= '<select' . $Mult . 'class="form-control" name="' . $Name . '" id="' . $Name . '">
    ';
    foreach ($Values as $key => $value) {
      $Retorno .= '<option value="' . $key . '">' . $value . '</option>
      ';
    }
    $Retorno .= '</select>
    </div>
    ';
    return $Retorno;
  }

  private function getDivBase(){
    return '<div class="form-group row">
    ';
  }

  public function addSubmit( $Value, $Controller, $Method){
    $this->Html .= $this->getDivBase();
    $this->Html .= '<div class="col-8">
    <button class="btn btn-secondary" type="button" onclick="javascript:formSubmit' . $this->Name . '( \'' . $Controller . '\', \'' . $Method . '\', this );">
      <span class="fa fa-check" aria-hidden="true"></span> ' . $Value . ' </button>
    </div></div>
    ';
    $this->Script .= '<script type="text/javascript">
function formSubmit' . $this->Name . '( Ctrl, Method, Button ){
    $(Button).attr("disabled","disabled");
    $("#return_html_' . $this->Name .'").html(\'Carregando...\');
  	$("#return_html_' . $this->Name .'").removeAttr("style", "display:none;").fadeIn();
    var crt = { ctrl: Ctrl, method: Method };
    var formData = $("#form_' . $this->Name . '").serialize() + "&" + $.param(crt);
    $.ajax({
	     url: \'' . $_SERVER['REQUEST_URI'] . '\',
	     data: formData,
	     type: \'POST\',
	     dataType: \'json\',
	     success: function(data) {
		      $("#return_html_' . $this->Name .'").html(data.msg);
		      $("#return_html_' . $this->Name .'").delay(10000).fadeOut();
          $(Button).removeAttr("disabled","disabled");
	     }
});
}
</script>';
  }

  public function addHead( $Titulo ){
    if ( $this->Html != null ){return true;}
    $this->Head = true;
    $this->Html = '<div class="container" align="center">
    <br /><div class="col-' . $this->Size . '">
    ';

    $this->Html .= '<div class="card card-default">
    <div class="card-header">
    ';
    $this->Html .= '<strong>' . $Titulo . '</strong>
    </div>
    <div class="card-body">
    ';
    $this->Html .= '<br /><div class="alert alert-info" id="return_html_' . $this->Name;
    $this->Html .= '" style="display: none;"></div>
    ';
    $this->Html .= '<form name="' . $this->Name . '" id="form_' . $this->Name . '">
    ';
  }

  public function addText( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'text', $Name );
    $this->Html .= '</div>
    ';
  }

  public function addSelect( $Name, $Label, $Values ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getSelectBase( $Name, $Values );
    $this->Html .= '</div>
    ';
  }

  public function addButton( $Name, $Label ){
    $this->initForm();
    $this->Html .= '</div>';
  }

  public function addCheckBox( $Name, $Label ){
    $this->initForm();
  }

  public function addSearch( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'search', $Name );
    $this->Html .= '</div>';
  }

  public function addEmail( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'email', $Name );
    $this->Html .= '</div>';
  }

  public function addUrl( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'url', $Name );
    $this->Html .= '</div>';
  }

  public function addTel( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'tel', $Name );
    $this->Html .= '</div>';
  }

  public function addPassword( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'password', $Name );
    $this->Html .= '</div>';
  }

  public function addNumber( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'number', $Name );
    $this->Html .= '</div>';
  }

  public function addDateTime( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'datetime-local', $Name );
    $this->Html .= '</div>';
  }

  public function addDate( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'date', $Name );
    $this->Html .= '</div>';
  }

  public function addMonth( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'month', $Name );
    $this->Html .= '</div>';
  }

  public function addWeek( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'week', $Name );
    $this->Html .= '</div>';
  }

  public function addTime( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'time', $Name );
    $this->Html .= '</div>';
  }

  public function addColor( $Name, $Label ){
    $this->initForm();
    $this->Html .= $this->getDivBase();
    $this->Html .= $this->getLabel( $Label );
    $this->Html .= $this->getInputBase( 'color', $Name );
    $this->Html .= '</div>';
  }

  public function print(){
    if ( $this->Head ){
      $this->Html .= '</div>';
    }
    $this->Html .= '</form></div>';
    return $this->Html . $this->Script;
  }

}
?>
