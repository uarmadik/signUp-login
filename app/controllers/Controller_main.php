<?php


namespace app\controllers;

use app\core\Controller;
use app\core\View;
use app\models\Model_article;
use app\models\Model_user;


class Controller_main extends Controller
{


    public function index()
    {
        if (!$this->isLogged()) {

            header('Location: /login');

        } else {

            $userData = $this->getUserDataCookie();
            if ((isset($userData) && is_numeric($userData['userId']))) {

                $model = new Model_user();
                $user = $model->getUserById($userData['userId']);
                $seances = $model->getSeances($userData['userId']);
            }

            $view = new View();
            $view->generate('main_view', ['user' => $user, 'seances' => $seances]);
        }

    }

}