<?php

use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;
use League\Plates\Engine;
use Valitron\Validator;

if( !session_id() ) @session_start();
ob_start();
require '../vendor/autoload.php';

$builder = new DI\ContainerBuilder();

$builder->addDefinitions([
    Engine::class => function() {
        return new Engine('../app/views');
    },
    PDO::class => function() {
        return new PDO("mysql:host=localhost;dbname=app;charset=utf8", "root");
    },
    Auth::class => function($container) {
        return new Auth($container->get('PDO'),null,null,false);
    },
    QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },
    Validator::class => function() {
        return new Validator($_POST);
    }


]);
$container = $builder->build();



 $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
     $r->addRoute('GET', '/admin', ['App\controllers\AdminController','admin']);

     $r->addRoute('GET', '/admin/allow', ['App\controllers\AdminController','allow']);

     $r->addRoute('GET', '/admin/hide', ['App\controllers\AdminController','hide']);

     $r->addRoute('GET', '/admin/delete', ['App\controllers\AdminController','delete']);

     $r->addRoute('GET', '/', ['App\controllers\HomeController','index']);

     $r->addRoute('POST', '/post', ['App\controllers\HomeController','newComment']);
    
     $r->addRoute('GET', '/about/{amount:\d+}', ['App\controllers\HomeController','about']);
    
     $r->addRoute('GET', '/register', ['App\controllers\LoginController','showRegistrationForm']);

     $r->addRoute('POST', '/register', ['App\controllers\LoginController','registration']);

     $r->addRoute('GET', '/verify_email', ['App\controllers\LoginController','email_verification']);

     $r->addRoute('GET', '/login', ['App\controllers\LoginController','showLoginForm']);

     $r->addRoute('GET', '/logout', ['App\controllers\LoginController','logout']);

     $r->addRoute('GET', '/profile', ['App\controllers\LoginController','showProfile']);

     $r->addRoute('POST', '/profile', ['App\controllers\LoginController','editProfile']);

     $r->addRoute('POST', '/password', ['App\controllers\LoginController','changePassword']);

     $r->addRoute('POST', '/login', ['App\controllers\LoginController','login']);
    
     $r->addRoute('GET', '/verification', ['App\controllers\HomeController','email_verification']);
 });

 // Fetch method and URI from somewhere
 $httpMethod = $_SERVER['REQUEST_METHOD'];
 $uri = $_SERVER['REQUEST_URI'];

 // Strip query string (?foo=bar) and decode URI
 if (false !== $pos = strpos($uri, '?')) {
     $uri = substr($uri, 0, $pos);
 }
 $uri = rawurldecode($uri);

 $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
 switch ($routeInfo[0]) {
     case FastRoute\Dispatcher::NOT_FOUND:
         // ... 404 Not Found
         echo 404;
         break;
     case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
         $allowedMethods = $routeInfo[1];
         // ... 405 Method Not Allowed
         echo 405;
         break;
     case FastRoute\Dispatcher::FOUND:
         $handler = $routeInfo[1];
         $vars = $routeInfo[2];

//         $controller = new $handler[0];
         $container->call($routeInfo[1], $routeInfo[2]);
//         call_user_func([$controller, $handler[1]],$vars);
         
         // ... call $handler with $vars
         break;
 }