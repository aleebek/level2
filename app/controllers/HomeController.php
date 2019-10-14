<?php
namespace App\controllers;

use App\QueryBuilder;
use Delight\Auth\Auth;
use Exception;
use League\Plates\Engine;
use PDO;
use Faker\Factory;
use JasonGrimes\Paginator;

class HomeController {

    protected $templates;

    protected $auth;

    protected $db;

    public function __construct(QueryBuilder $qb, Engine $engine, Auth $auth)
    {
        $this->templates = $engine;
        $this->auth = $auth;
        $this->db = $qb;

    }


    public function index()
    {


        if (!$this->auth->isLoggedIn()) {
            header("Location: /login");
            exit();
        }

        $totalItems = $this->db->rowsCount('posts');
        $itemsPerPage = 10;
        $currentPage = $_GET['page'] ?? 1;
        $urlPattern = '?page=(:num)';

        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

        $posts = $this->db->getAllPosts($itemsPerPage, $currentPage);

        // Render a template
        echo $this->templates->render('homepage', ['posts' => $posts, 'paginator' => $paginator ]);
    }

    public function newComment()
    {
        if (!$this->auth->isLoggedIn()) {
            header("Location: /login");
            exit();
        }

        if (empty($_REQUEST['text'])) {
            flash()->error('Text is required');
            header("Location: /");
            exit();
        }

        $this->db->insert('posts', [
            'user_id' => $_SESSION['auth_user_id'],
            'text' => $_POST['text'],
            'date' => date('Y-m-d')
        ]);

        flash()->success('Comment added');
        header("Location: /");
        exit();
    }

    public function about($vars) 
    {
        try {
            withdraw($vars['amount']);
        } catch (Exception $th) {
            flash()->error($th->getMessage());
        }
        // Render a template
        echo $this->templates->render('about', ['name' => 'Jonathan']);
    }

    public function register()
    {
        

        try {
            $userId = $this->auth->register('ali@ali.ali', 'asdfgh123', 'ali', function ($selector, $token) {
                echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email)';
            });
        
            echo 'We have signed up a new user with the ID ' . $userId;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }




}
