<?php

class SimpleTable{

  private $name;
  private $html;
  private $line;

  public function __construct( $name ){
    $this->line = array();
    $this->name = $name;
    $this->html = '<div class="table-responsive">
  <table class="table">';
  }

  public function addHead( $heads ){
    $this->html .= '<thead>
      <tr>';
    foreach ($heads as  $value) {
      $this->html .= '<th>' . $value . '</th>';
    }
    $this->html .= '</tr>
    </thead><tbody>';
  }

  public function addCollum( $col ){
    $this->line[] = $col;
  }

  public function addButton( $value, $icon, $function ){
    $col = '<button type="button" class="btn btn-default btn-sm" onclick="' . $function .  '">
      <span class="fa fa-' . $icon . '"></span> ' . $value . ' </button>';
    $this->line[] = $col;
  }

  public function addLine( $line = array() ){
    $this->html .= '<tr>';
    if( empty( $line ) ){
      $line = $this->line;
      $this->line = array();
    }
    foreach ($line as $value) {
      $this->html .= '<td>' . $value . '</td>';
    }
    $this->html .= '</tr>';
  }

  public function print(){
    $this->html .= '</tbody></table></div></div>';
    return $this->html;
  }


}

 ?>
