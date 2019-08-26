<?php

define("PHPUNIT_TESTING", true);

include_once("../../index.php");

$Wcms = new Wcms();
$Wcms->init();

$SimpleBlog = new SimpleBlog(false);
$SimpleBlog->init();

if(!$Wcms->loggedIn
    && $_SESSION['token'] === $_POST['token']
    && $Wcms->hashVerify($_POST['token']))
    die("Please login first.");

if(!isset($_POST["page"])) die("Please specify key and value");

$slug = $Wcms->slugify($_POST["page"]);

if(empty($slug)) die("Please specify all the fields");

$posts = (array)$SimpleBlog->get("posts");

$posts[$slug] = [
    "title" => htmlspecialchars($_POST['page'], ENT_QUOTES),
    "description" => "This is a new blog post.",
    "date" => time(),
    "body" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem nesciunt voluptas tempore vero, porro reprehenderit cum provident eum sapiente voluptate veritatis, iure libero, fugiat iste soluta repellendus aliquid impedit alias."
];

$SimpleBlog->set("posts", $posts);

echo $SimpleBlog->slug . "/" . $slug;

?>
