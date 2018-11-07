<?php
//include init
require_once('../class/init.php');

//log user out
$member->logout();
header('Location: index.php'); 

?>