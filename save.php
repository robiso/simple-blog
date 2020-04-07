<?php

define("PHPUNIT_TESTING", true);

include_once("../../index.php");

$Wcms = new Wcms();
$Wcms->init();

$SimpleBlog = new SimpleBlog(false);
$SimpleBlog->init();

if(!$Wcms->loggedIn
    || $_SESSION['token'] !== $_GET['token']
    || !$Wcms->hashVerify($_GET['token']))
    die("Access denied.");

if(!isset($_POST["key"], $_POST["value"], $_POST["page"])) die("Please specify key and value");

$key = $Wcms->slugify($_POST["key"]);
$page = $Wcms->slugify($_POST["page"]);
$value = $_POST["value"];

if(empty($key) || empty($page) || empty($value)) die("Please specify all the fields");

$SimpleBlog->set("posts", $page, $key, $value);

?>
