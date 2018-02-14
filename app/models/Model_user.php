<?php

namespace app\models;

use app\core\Model;
use PDO;
use Exception;

class Model_user extends Model
{
    public function index()
    {
        $connection = $this->getDbConnection();
        $data = $connection->query('SELECT * FROM `users`');

        return $data;

    }

    public function getUserById($id)
    {
        $connection = $this->getDbConnection();
        $stmt = $connection->prepare('SELECT * FROM `users` WHERE (`id` = ?)');
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByName($userName)
    {
        if (!$this->validateUserName($userName)) {

            $_SESSION['error_msg'] = 'User name must be from 2 symbol to 8 symbol!';
            header('Location: /');
            exit;
        }

        $connection = $this->getDbConnection();
        $stmt = $connection->prepare('SELECT * FROM `users` WHERE (`user_name` = ?)');
        $stmt->execute([$userName]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function getUserByEmail($userEmail)
    {
        if (!$this->validateUserEmail($userEmail)) {

            $_SESSION['error_msg'] = 'Email is not valid!';
            header('Location: /');
            exit;
        }

        $connection = $this->getDbConnection();
        $stmt = $connection->prepare('SELECT * FROM `users` WHERE (`user_email` = ?)');
        $stmt->execute([$userEmail]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addUser($userName, $userEmail, $userPassword)
    {
        $userName = $this->validateUserName($userName);
        $userEmail = $this->validateUserEmail($userEmail);
        $userPassword = $this->validateUserPassword($userPassword);


        if (!($userName && $userEmail && $userPassword)) {

            $_SESSION['error_msg'] = 'Validation error!';
            header('Location: /');
            exit;
        }

        $userPassword = password_hash($userPassword, PASSWORD_BCRYPT);

        $connection = $this->getDbConnection();
        $stmt = $connection->prepare(
            'INSERT INTO test_db.`users` VALUES ( NULL, :user_name, :user_email, :user_password)'
        );
        $stmt->execute([
            'user_name'     => $userName,
            'user_email'    => $userEmail,
            'user_password' => $userPassword
        ]);
    }

    /**
     * @param $userName
     * @return bool|string
     *
     * string $userName must be from 2 symbol to 8 symbol
     */
    protected function validateUserName($userName)
    {
        $name = trim($userName);

        $nameLenght = strlen($name);

        return ($nameLenght >= 2 && $nameLenght <= 8) ? $userName : false;
    }

    protected function validateUserEmail($userEmail)
    {
        $userEmail = trim($userEmail);

        return filter_var($userEmail, FILTER_VALIDATE_EMAIL);
    }


    protected function validateUserPassword($userPassword)
    {
//        $userPassword = trim($userPassword);

        return trim($userPassword);
    }

    public function comparePassword($password, $hash)
    {
        $password = $this->validateUserPassword($password);

        return password_verify($password, $hash);
    }

    public function getSeances($userId)
    {
        if (!is_numeric($userId)) {
            throw new Exception('Incorrect format parameter userId: ' . $userId);
        }

        $connection = $this->getDbConnection();
        $stmt = $connection->prepare(
            'SELECT * FROM test_db.`seances` WHERE(`user_id` = :user_id)'
        );
        $stmt->execute([ 'user_id' => $userId ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCookieHash($seanceId, $userId)
    {
        $connection = $this->getDbConnection();
        $stmt = $connection->prepare(
            'SELECT `cookie` FROM test_db.`seances` WHERE( `id` = :seance_id AND `user_id` = :user_id)'
        );

        $stmt->execute([
            'seance_id' => $seanceId,
            'user_id' => $userId,
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return (!empty($data)) ? $data['cookie'] : null;
    }

    /**
     * @param $cookie
     * @param $device_type
     * @param $user_agent
     * @param $user_id
     * @return string - Last insert ID
     */
    public function addSeance($cookie, $device_type, $user_agent, $user_id)
    {
        $connection = $this->getDbConnection();
        $stmt = $connection->prepare(
            'INSERT INTO test_db.`seances` VALUES (NULL, :cookie, :device_type, :user_agent, :user_id, NOW())'
        );
        $stmt->execute([
            'cookie' => $cookie,
            'device_type' => $device_type,
            'user_agent' => $user_agent,
            'user_id' => $user_id,
        ]);

        return $connection->lastInsertId();
    }

    public function deleteSeance($seanceId, $userId)
    {
        $connection = $this->getDbConnection();
        $stmt = $connection->prepare(
            'DELETE FROM test_db.`seances` WHERE (`id` = ? AND `user_id` = ?)'
        );
        $stmt->bindValue(1, $seanceId, PDO::PARAM_INT);
        $stmt->bindValue(2, $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}