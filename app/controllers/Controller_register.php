<?php


namespace app\controllers;

use app\core\Controller;
use app\core\View;
use app\models\Model_article;
use app\models\Model_user;


class Controller_register extends Controller
{
    const RECAPTCHA = [
        'url'     => 'https://www.google.com/recaptcha/api/siteverify',
        'private' => '6LdX7EUUAAAAAPKkQL5jH8da5PR4jLcUKDGOKTh7',
        'secret'  => '6LdX7EUUAAAAAPJHZUmeW9HCzS8kazd3zM7ffKEF'
    ];

    public function index()
    {

        if (!empty($_POST) && $this->validationCapcha()) {

            $userName     = $_POST['name'];
            $userEmail    = $_POST['email'];
            $userPassword = $_POST['password'];


            $model = new Model_user();

            $existUserName = $model->getUserByName($userName);

            if ($existUserName) {

                $_SESSION['error_msg'] = 'User name ' . $userName . ' already exist!';
                header('Location: /register');
                exit;
            }

            $existUserEmail = $model->getUserByEmail($userEmail);

            if ($existUserEmail) {

                $_SESSION['error_msg'] = 'Email ' . $userEmail . ' already exist!';
                header('Location: /register');
                exit;
            }




            $model->addUser($userName, $userEmail, $userPassword);

            header('Location: /login');
            exit;

        }

        $view = new View();
        $view->generate('sign-up_view', ['error_msg' => $this->error_msg]);
    }

    protected function validationCapcha()
    {
        $captcha = $_POST['g-recaptcha-response'];
        if(!$captcha) {

            $_SESSION['error_msg'] = 'Captcha not found!';
            header('Location: /register');
            return false;
        } else {

            $secretKey = self::RECAPTCHA['secret'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $response = file_get_contents(self::RECAPTCHA['url'].'?secret='.$secretKey.'&response='.$captcha.'&remoteip='.$ip);
            $result = json_decode($response, true);
            if (intval($result['success']) !== 1) {

                return false;
            }

            return true;
        }
    }
}