<?php
require_once __DIR__ . '/../src/GitLab/class/Groups.class.php';
$Grupos = new GitLabGroups();
foreach ( $Grupos->getGroups() as $Group ) {
  foreach ( $Grupos->getProjects( $Group->name ) as $Project){
     print_r( $Project );
  }
}
?>
