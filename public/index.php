<?php
require_once __DIR__ . "/../src/Routes/Route.php";
$Route = new Route();
$Route->add('/', 'Home');
$Route->add('/build', 'Build');
$Route->add('/build/:1', 'BuildManager');
$Route->get();
?>
