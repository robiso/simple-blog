<?php

global $Wcms;

include_once("class.SimpleBlog.php");

$SimpleBlog = new SimpleBlog(true);
$SimpleBlog->init();
$SimpleBlog->attach();

?>
