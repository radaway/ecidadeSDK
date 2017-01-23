<?php
interface Project {
    public function __construct( $path );
    public function buildVersion( $Versao );
    public function checkoutTag( $Tags );
    public function init();
}
?>
