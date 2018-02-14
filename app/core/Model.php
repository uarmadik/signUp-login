<?php


namespace app\core;
use PDO;

class Model
{

    protected $config = [
        'host'    => 'localhost',
        'db'      => 'test_db',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf-8',
    ];

    /**
     * @return PDO
     */
    protected function getDbConnection()
    {
        $dsn = 'mysql:host=' . $this->config['host'] . ';dbname=' . $this->config['db'] . ';"charset="' . $this->config['charset'] . '"';


        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {

            return new PDO($dsn, $this->config['user'], $this->config['pass']);

        } catch (PDOException $e) {

            die('Error connection: ' . $e->getMessage());
        }

    }

}