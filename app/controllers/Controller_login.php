<?php


namespace app\controllers;

use app\core\View;
use app\models\Model_article;
use app\models\Model_user;
use Detection\MobileDetect;
use app\core\Controller;
use yii\helpers\Url;

class Controller_login extends Controller
{


    public function index()
    {
        if ($this->isLogged()) {

            $_SESSION['error_msg'] = 'User already is logged!';
            header('Location: /');

        } elseif((!empty($_POST['name'])) && (!empty($_POST['password']))) {

            $userName     = $_POST['name'];
            $userPassword = $_POST['password'];

            if ($this->loginUser($userName, $userPassword)) {

                header('Location: /');
                exit;
            }
        }

        $view = new View();
        $view->generate('login_view', ['error_msg' => $this->error_msg]);
    }

    protected function loginUser($userName, $userPassword)
    {
        $model = new Model_user();

        if (filter_var($userName, FILTER_VALIDATE_EMAIL)) {

            $user = $model->getUserByEmail($userName);

        } else {

            $user = $model->getUserByName($userName);
        }

        if (!$user) {

            $_SESSION['error_msg'] = 'Unknown user name/email ' . $userName;
            header('Location: /login');
            exit;
        }

        if (!$model->comparePassword($userPassword, $user['password'])) {

            // passwords are not equel!
            $_SESSION['error_msg'] = 'Incorrect password!';
            header('Location: /login');
            exit;
        }

        // create cookie

        $userId = $user['id'];
        $userIp = $_SERVER['REMOTE_ADDR'];

        // detect device
        $deviceType = $this->detectDevice();
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $cookieStr = md5($this->salt . $userId . $userIp . $this->salt);

        setcookie('remember', $cookieStr, time() + 60*60*24);

        // add seance to DB
        $seanceId = $model->addSeance($cookieStr, $deviceType, $userAgent, $userId);

        $userData = ['userId' => $userId, 'seanceId' => $seanceId];
        setcookie('userData', json_encode($userData), time() + 60*60*24);

        return true;

    }

    public function logout()
    {
        if (!$this->isLogged()) {

            $_SESSION['error_msg'] = 'You should login before!';
            header('Location: /login');
        }

        $id = $_GET['id'];
        if ((isset($id)) && (is_numeric($id))) {

            $userData = $this->getUserDataCookie();
            $userId = $userData['userId'];

            $this->deleteSeance($id, $userId);

            unset($_COOKIE['userData']);
            unset($_COOKIE['remember']);
            setcookie('userData', null, - 1);
            setcookie('remember', null, - 1);

            header('Location: /');
            exit;
        }

    }

    protected function deleteSeance($seanceId, $userId)
    {
        $model = new Model_user();
        $model->deleteSeance($seanceId, $userId);

        return true;
    }

    protected function detectDevice()
    {
        $detect = new MobileDetect();

        $deviceType = 'unknown';

            // Если мобильное устройство (телефон или планшет).
        if ( $detect->isMobile() ) {

            $deviceType = 'mobile';
        }

            // Если планшет
        if( $detect->isTablet() ){

            $deviceType = 'tablet';
        }

            // Если не планшет и не мобильное устройство
        if( !$detect->isMobile() && !$detect->isTablet() ){

            $deviceType = 'computer';
        }

        return $deviceType;
    }
}