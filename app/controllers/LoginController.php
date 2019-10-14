<?php
namespace App\controllers;

use App\QueryBuilder;
use Delight\Auth\Auth;
use Exception;
use League\Plates\Engine;
use PDO;
use Faker\Factory;
use SimpleMail;
use Valitron\Validator;
use \Tamtamchik\SimpleFlash\Flash;

class LoginController {

    protected $templates;
    protected $v;
    protected $auth;
    protected $qb;

    public function __construct(Engine $engine, QueryBuilder $qb, Auth $auth, Validator $v)
    {
        $this->templates = $engine;
        $this->v = $v;
        $this->qb = $qb;
        $this->auth = $auth;

    }

    //showLoginForm
    public function showLoginForm()
    {
        if ($this->auth->isLoggedIn()) {
            header("Location: /");
            exit();
        }
        echo $this->templates->render('auth/login');
    }
    //login
    public function login()
    {
        $this->v->rule('required', ['email', 'password']);

        if(!$this->v->validate()) {

            // Errors
            foreach ($this->v->errors() as $error) {
                flash()->error($error);
            }

            header("Location: /login");
            exit();
        }

        try {
            $this->auth->login($_POST['email'], $_POST['password']);
            header("Location: /");
            exit();
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error('Wrong email address');

        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error('Wrong password');

        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            flash()->error('Email not verified');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error('Too many requests');
        }



        header("Location: /login");
        exit();
    }
    //logout
    public function logout()
    {
        $this->auth->logOut();
        header("Location: /login");
        exit();

    }
    //showRegisterForm
    public function showRegistrationForm()
    {
        echo $this->templates->render('auth/registration');
    }
    //register
    public function registration()
    {

        $this->v->rule('required', ['password', 'passwordConfirmation']);
        $this->v->rule('equals', 'password', 'passwordConfirmation');
        if(!$this->v->validate()) {
            // Errors
            foreach ($this->v->errors() as $error) {
                flash()->error($error);
            }

            header("Location: /profile");
            exit();
        }

        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['email'], function ($selector, $token) {

                SimpleMail::make()
                    ->setTo($_POST['email'], $_POST['email'])
                    ->setFrom('noreply@alibek.zzz.com.ua', 'Admin')
                    ->setSubject('confirmation')
                    ->setMessage('https://www.example.com/verify_email?selector=' . \urlencode($selector) . '&token=' . \urlencode($token))
                    ->send();


            });


            flash()->success('We have signed up a new user with the ID: ' . $userId);
            header("Location: /login");
            exit();


        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            flash()->error('Invalid email address');

        }
        catch (\Delight\Auth\InvalidPasswordException $e) {

            flash()->error('Invalid password');

        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            flash()->error('User already exists');

        }
        catch (\Delight\Auth\TooManyRequestsException $e) {

            flash()->error('Too many requests');

        }
        header("Location: /register");
        exit();
    }


    public function showProfile()
    {

        $user = $this->qb->getOne('users', $_SESSION['auth_user_id']);
        d($_SESSION, $user['image']);
        echo $this->templates->render('profile/profile', ['email' => $user['email'], 'image' => $user['image'], 'username' => $user['username']]);
    }

    public function editProfile()
    {
        $this->v->rule('required', ['email', 'name']);
        $this->v->rule('email', 'email');
        if(!$this->v->validate()) {
            // Errors
            foreach ($this->v->errors() as $error) {
                flash()->error($error);
            }

            header("Location: /profile");
            exit();
        }

        if (strcmp($_POST['email'], $_SESSION['auth_email'])){
            try {
                $this->auth->changeEmail($_POST['email'], function ($selector, $token) {
                    echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email to the *new* address)';
                });

                flash()->success('The change will take effect as soon as the new email address has been confirmed');
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Invalid email address');
                header("Location: /profile");
                exit();
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                flash()->error('Email address already exists');
                header("Location: /profile");
                exit();
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                flash()->error('Account not verified');
                header("Location: /profile");
                exit();
            }
            catch (\Delight\Auth\NotLoggedInException $e) {
                flash()->error('Not logged in');
                header("Location: /profile");
                exit();
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                flash()->error('Too many requests');
                header("Location: /profile");
                exit();
            }
        }


        if (empty($_FILES['image']['tmp_name'])) {

            $this->qb->update('users', $_SESSION['auth_user_id'], ['username' => $_REQUEST['name']]);

        } else {
            $image = 'img/'.uniqid().'.jpg';
            move_uploaded_file($_FILES['image']['tmp_name'], $image);
            $this->qb->update('users', $_SESSION['auth_user_id'], ['username' => $_REQUEST['name'], 'image' => $image]);

        }
        flash()->success('Profile updated');
        header("Location: /profile");
        exit();
    }

    public function changePassword()
    {
        $this->v->rule('required', ['oldPassword', 'newPassword', 'passwordConfirmation']);
        $this->v->rule('email', 'email');
        $this->v->rule('equals', 'newPassword', 'passwordConfirmation');
        if(!$this->v->validate()) {
            // Errors
            foreach ($this->v->errors() as $error) {
                flash()->error($error);
            }

            header("Location: /profile");
            exit();
        }
        try {
            $this->auth->changePassword($_POST['oldPassword'], $_POST['newPassword']);
            flash()->success('Password has been changed');

        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            flash()->error('Not logged in');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            flash()->error('Invalid password(s)');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error('Too many requests');
        }

        header("Location: /profile");
        exit();
    }

    public function email_verification() {
        try {
            $this->auth->confirmEmail($_GET['selector'], $_GET['token']);
            flash()->success('Email address has been verified');
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            flash()->error('Invalid token');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            flash()->error('Token expired');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            flash()->error('Email address already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            flash()->error('Too many requests');
        }
        header("Location: /login");
        exit();


    }



}