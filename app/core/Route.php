<?php

namespace app\core;


class Route
{
    public static function start()
    {
        $routes = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        $controller_name = (!empty($routes[1])) ? 'Controller_' . $routes[1] : 'Controller_main';
        $action_name = (!empty($routes[2]))     ? $routes[2] : 'index';
        $parameter = (!empty($routes[3]))       ? $routes[3] : null;

        if (file_exists('../app/controllers/'. $controller_name . '.php')){
            $controller_file_name = 'app\controllers\\' . $controller_name;

            if ($controller_name == 'Controller_parser') {

                include_once $_SERVER['DOCUMENT_ROOT'] . '/lib/phpQuery-onefile.php';
            }

            $init = new $controller_file_name();
        } else {

            self::ErrorPage404();
        }

        if ($parameter && (method_exists($init, $action_name))) {
            $init->$action_name($parameter);
        } elseif (method_exists($init, $action_name)){

            $init->$action_name();
        } else {

            self::ErrorPage404();
        }

    }

    static function ErrorPage404()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        header('Location:' . $host . '404');
    }
}