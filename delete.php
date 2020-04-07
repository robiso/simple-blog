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

if(!isset($_GET["page"])) die("Please specify key and value");

$slug = $Wcms->slugify($_GET["page"]);

if(empty($slug)) die("Please specify all the fields");

$posts = (array)$SimpleBlog->get("posts");

if(isset($posts[$slug]))
    unset($posts[$slug]);

$SimpleBlog->set("posts", $posts);

header("location: " . $Wcms->url("../../{$SimpleBlog->slug}"));

?>
