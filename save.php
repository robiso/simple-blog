<?php

define("PHPUNIT_TESTING", true);

include_once("../../index.php");

$Wcms = new Wcms();
$Wcms->init();

$SimpleBlog = new SimpleBlog(false);
$SimpleBlog->init();

if(!$Wcms->loggedIn) die("Please login first.");

if(!isset($_POST["key"], $_POST["value"], $_POST["page"])) die("Please specify key and value");

$key = preg_replace("#[^a-z]#", "", $_POST["key"]);
$page = preg_replace("#[^a-z0-9-]#", "", $_POST["page"]);
$value = $_POST["value"];

if(empty($key) || empty($page) || empty($value)) die("Please specify all the fields");

$SimpleBlog->set("posts", $page, $key, $value);

?>
