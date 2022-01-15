<?php

define("PHPUNIT_TESTING", true);

include_once("../../index.php");

$Wcms = new Wcms();
$Wcms->init();

$SimpleBlog = new SimpleBlog(false);
$SimpleBlog->init();

$requestToken = $_POST['token'] ?? $_GET['token'] ?? null;
if(!$Wcms->loggedIn
    || $_SESSION['token'] !== $requestToken
    || !$Wcms->hashVerify($requestToken))
    die("Please login first.");

if(!isset($_POST["key"], $_POST["value"], $_POST["page"])) die("Please specify key and value");
if(!isset($_POST["page"])) die("Please specify key and value");

$key = $Wcms->slugify($_POST["key"]);
$slug = $Wcms->slugify($_POST["page"]);
$page = $Wcms->slugify($_POST["page"]);
$value = $_POST["value"];

if(empty($key) || empty($page) || empty($value)) die("Please specify all the fields");
if(empty($slug)) die("Please specify all the fields");


$SimpleBlog->set("posts", $page, $key, $value);

?>
