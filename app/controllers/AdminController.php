<?php
namespace App\controllers;

use App\QueryBuilder;
use Delight\Auth\Auth;
use Exception;
use League\Plates\Engine;
use PDO;
use Faker\Factory;
use JasonGrimes\Paginator;

class AdminController {

    protected $templates;

    protected $auth;

    protected $db;

    public function __construct(Engine $engine, Auth $auth, QueryBuilder $qb)
    {
        $this->templates = $engine;

        $this->auth = $auth;

        $this->db = $qb;

    }

    protected function checkAdmin() {
        if (!$this->auth->isLoggedIn()) {
            header("Location: /login");
            exit();
        }
        if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) {
            header("Location: /");
            exit();
        }
    }


    public function admin()
    {
        //$this->auth->logout();
        $this->checkAdmin();

        $totalItems = $this->db->rowsCount('posts');
        $itemsPerPage = 10;
        $currentPage = $_GET['page'] ?? 1;
        $urlPattern = '?page=(:num)';

        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

        $posts = $this->db->getAllPosts($itemsPerPage, $currentPage);

        d($this->auth->isLoggedIn(), $_SESSION, $posts);
        // Render a template
        echo $this->templates->render('admin/admin', ['posts' => $posts, 'paginator' => $paginator ]);



    }

    public function allow()
    {
        $this->checkAdmin();
        $id = $_GET['id'];
        $this->db->update('posts',$id, ['hidden' => 0]);
        header("Location: /admin");
        exit();
    }

    public function hide()
    {
        $this->checkAdmin();
        $id = $_GET['id'];
        $this->db->update('posts', $id, ['hidden' => 1]);
        header("Location: /admin");
        exit();
    }

    public function delete()
    {
        $this->checkAdmin();
        $id = $_GET['id'];
        $this->db->delete('posts', $id);
        header("Location: /admin");
        exit();
    }




}

// $db = new QueryBuilder();

// $posts=$db->getAll('posts');

// var_dump($posts);
