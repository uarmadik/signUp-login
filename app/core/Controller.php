<?php

namespace app\core;

use app\models\Model_user;

abstract class Controller
{
    protected $salt = 'sugar';

    public $error_msg = '';

    public function __construct()
    {
        if (isset($_SESSION['error_msg'])) {

            $this->error_msg = $_SESSION['error_msg'];
            unset($_SESSION['error_msg']);

        } else {

            $this->error_msg = '';
        }
    }

    protected function isLogged()
    {
        $userData     = $this->getUserDataCookie();
        $rememberHash = $this->getRememberHash();

        if (!(isset($userData) && isset($rememberHash))) {

            return false;
        }

        $seanceId = (INT) $userData['seanceId'];
        $userId = (INT) $userData['userId'];

        if ($seanceId && $userId) {

            $model = new Model_user();
            $hashDb = $model->getCookieHash($seanceId, $userId);

            if (!($rememberHash === $hashDb)) {

                return false;
            } else {

                $presentHash = $this->getPresentHash($userId);
                return ($presentHash === $hashDb);
            }
        }

        return false;
    }

    protected function getPresentHash($userId)
    {
        $userIp = $_SERVER['REMOTE_ADDR'];

        if (!isset($userIp)) {
            throw new \Exception('Can not detect IP');
        }

        return md5($this->salt . $userId . $userIp . $this->salt);
    }

    protected function getRememberHash()
    {
        return $_COOKIE['remember'];
    }

    protected function getUserDataCookie()
    {
        return (isset($_COOKIE['userData'])) ? json_decode($_COOKIE['userData'], true) : false;
    }
}