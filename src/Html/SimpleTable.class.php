<?php

class SimpleTable{

  private $name;
  private $html;

  public function __construct( $name ){

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

  public function addLine( $line ){
    $this->html .= '<tr>';
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
