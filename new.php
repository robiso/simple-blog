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

if(!isset($_POST["page"])) die("Please specify key and value");

$slug = $Wcms->slugify($_POST["page"]);

if(empty($slug)) die("Please specify all the fields");

$posts = (array)$SimpleBlog->get("posts");

// Prevent duplicate slugs
$originalSlug = $slug;
$counter = 1;
while (isset($posts[$slug])) {
    $slug = $originalSlug . '-' . $counter;
    $counter++;
}

$posts[$slug] = [
    "title" => htmlspecialchars($_POST['page'], ENT_QUOTES),
    "description" => "This blog post and the first paragraph is the short snippet.",
    "keywords" => "#your, #keywords #here",
    "date" => time(),
    "body" => "This is the full blog post content. Here's some more example text. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem nesciunt voluptas tempore vero, porro reprehenderit cum provident eum sapiente voluptate veritatis, iure libero, fugiat iste soluta repellendus aliquid impedit alias."
];

$SimpleBlog->set("posts", $posts);

echo $SimpleBlog->slug . "/" . $slug;

?>