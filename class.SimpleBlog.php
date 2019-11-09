<?php

global $Wcms;

class SimpleBlog
{

    public $slug = "blog";

    private $Wcms;

    private $db;

    private $dbPath = __DIR__ . "/simpleblog.json";

    private $dateFormat = "d F Y";

    private $path = [""];

    private $active = false;

    public function __construct($load)
    {
        if ($load) {
            global $Wcms;
            $this->Wcms =& $Wcms;
        }
    }

    public function init(): void
    {
        $this->db = $this->getDb();
    }

    private function getDb(): stdClass
    {
        if (!file_exists($this->dbPath)) {
            file_put_contents($this->dbPath, json_encode([
                "title" => "Blog",
                "posts" => [
                    "hello-world" => [
                        "title" => "Hello, World!",
                        "description" => "This blog post and the first paragraph is the short snippet.",
                        "date" => time(),
                        "body" => "This is the full blog post content. Here's some more example text. Consectetur adipisicing elit. Quidem nesciunt voluptas tempore vero, porro reprehenderit cum provident eum sapiente voluptate veritatis, iure libero, fugiat iste soluta repellendus aliquid impedit alias."
                    ]
                ]
            ], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        return json_decode(file_get_contents($this->dbPath));
    }

    public function attach(): void
    {
        $this->Wcms->addListener('menu', [$this, "menuListener"]);
        $this->Wcms->addListener('page', [$this, "pageListener"]);
        $this->Wcms->addListener('css', [$this, "startListener"]);
        $this->Wcms->addListener('js', [$this, "jsListener"]);

        $pathTest = explode('-', $this->Wcms->currentPage);
        if (array_shift($pathTest) === $this->slug) {
            global $Wcms;
            $Wcms->headerResponseDefault = false;
            $Wcms->headerResponse = 'HTTP/1.0 200 OK';
        }
    }

    private function save(): void
    {
        file_put_contents($this->dbPath,
            json_encode($this->db, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function set(): void
    {
        $numArgs = func_num_args();
        $args = func_get_args();

        switch ($numArgs) {
            case 2:
                $this->db->{$args[0]} = $args[1];
                break;
            case 3:
                $this->db->{$args[0]}->{$args[1]} = $args[2];
                break;
            case 4:
                $this->db->{$args[0]}->{$args[1]}->{$args[2]} = $args[3];
                break;
            case 5:
                $this->db->{$args[0]}->{$args[1]}->{$args[2]}->{$args[3]} = $args[4];
                break;
        }
        $this->save();
    }

    public function get()
    {
        $numArgs = func_num_args();
        $args = func_get_args();
        switch ($numArgs) {
            case 1:
                return $this->db->{$args[0]};
            case 2:
                return $this->db->{$args[0]}->{$args[1]};
            case 3:
                return $this->db->{$args[0]}->{$args[1]}->{$args[2]};
            case 4:
                return $this->db->{$args[0]}->{$args[1]}->{$args[2]}->{$args[3]};
            case 5:
                return $this->db->{$args[0]}->{$args[1]}->{$args[2]}->{$args[3]}->{$args[4]};
        }
    }

    public function startListener(array $args): array
    {
        // This code redides here instead of in init() because currentPage is empty there.
        // This is the first location where currentPage is set
        $path = explode("-", $this->Wcms->currentPage);
        if (array_shift($path) == $this->slug) {
            $this->active = true;
            $this->path = $path ? implode("-", $path) : [""];
        }

        if ($this->active) {
            // Remove page doesn't exist notice on blog pages
            if (isset($_SESSION['alert']['info'])) {
                foreach ($_SESSION['alert']['info'] as $i => $v) {
                    if ($v['message'] === '<b>This page (' . $this->Wcms->currentPage . ') doesn\'t exist.</b> Click inside the content below to create it.') {
                        unset($_SESSION['alert']['info'][$i]);
                    }
                }
            }
        }

        $args[0] .= "<link rel='stylesheet' href='{$this->Wcms->url('plugins/simple-blog/css/blog.css')}'>";
        return $args;
    }

    public function jsListener(array $args): array
    {
        $args[0] .= "<script src='{$this->Wcms->url('plugins/simple-blog/js/blog.js')}'></script>";
        return $args;
    }

    public function menuListener(array $args): array
    {
        // Add blog menu item
        $extra = $this->active ? 'active ' : '';

        $args[0] .= <<<HTML
        <li class="{$extra}nav-item">
            <a class="nav-link" href="{$this->Wcms->url($this->slug)}">Blog</a>
        </li>
HTML;

        return $args;
    }

    public function pageListener(array $args): array
    {
        $args = $this->setMetaTags($args);

        if ($this->active) {
            switch ($this->path[0]) {
                case '':
                    // Start rendering homepage
                    $args[0] = "";

                    if ($this->Wcms->loggedIn) {
                        $args[0] = "<div class='text-right'><a href='#' class='btn btn-info' onclick='blog.new(); return false;'><span class='glyphicon glyphicon-plus-sign'></span> Create new post</a></div>";
                    }

                    $args[0] .= <<<HTML
                    <h1>{$this->db->title}</h1>
HTML;

                    // Little inline reversing
                    foreach (array_reverse((array)$this->db->posts) as $slug => $post) {
                        $date = date($this->dateFormat, $post->date);

                        $args[0] .= <<<HTML
                        <div class="post card">
                            <h3>{$post->title}</h3>
                            <div class="meta">
                                <div class="row">
                                    <div class="col-sm-12 text-right"><small>{$date}</small></div>
                                </div>
                            </div>
                            <p class="description">{$post->description}</p>
                            <a href="{$this->Wcms->url($this->slug . '/' . $slug)}" class="text-right">Read more</a>
                        </div>
HTML;
                    }
                    break;

                default:
                    if (isset($this->db->posts->{$this->path})) {
                        // Display post
                        $post = $this->db->posts->{$this->path};
                        $date = date($this->dateFormat, $post->date);

                        $edit = "";
                        $description = "";
                        $delete = "";
                        if ($this->Wcms->loggedIn) {
                            $edit = ' contenteditable="true" onblur="blog.save(this)"';
                            $description = "<div class='description' $edit>{$post->description}</div><br>";
                            $delete = " &nbsp; &bull; &nbsp; <a href='{$this->Wcms->url('plugins/simple-blog/delete.php')}?page={$this->path}&token={$this->Wcms->getToken()}' onclick='return confirm(\"Are you sure you want to delete this post?\")'>Delete</a>";
                        }

                        $args[0] = <<<HTML

                        <div class="post">
                            <h1 class="title" $edit>{$post->title}</h1>
                            <p class="meta">{$date}{$delete}</p>
                            $description
                            <div class="body text-justify" $edit>
                                {$post->body}
                            </div>
                        </div>
                        <br /><br />
                        <div class="text-left">
                            <a href="./" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-chevron-left small"></span> Back to all posts</a>
                        </div>
HTML;
                    } else {
                        // Display 404 (unless it's admin, then it's never a 404)
                        $args[0] = $this->Wcms->get('pages', '404')->content;

                        header("HTTP/1.0 404 Not Found");
                    }
                    break;
            }
        }

        return $args;
    }

    private function setMetaTags(array $args): array
    {
        $subPage = str_replace($this->slug . '-', '', strtolower($this->Wcms->currentPage));
        if ((($subPage !== $this->slug && isset($this->db->posts->{$subPage})) || $subPage === $this->slug)
            && isset($args[1])
            && ($args[1] === 'title' || $args[1] === 'description' || $args[1] === 'keywords')
        ) {
            $args[0] = isset($this->db->posts->{$subPage})
                ? $this->db->posts->{$subPage}->{$args[1] === 'keywords' ? 'description' : $args[1]}
                : $this->db->title;

            if ($args[1] === 'keywords') {
                $args[0] = str_replace(' ', ', ', $args[0]);
            }
        }

        return $args;
    }

}

?>
