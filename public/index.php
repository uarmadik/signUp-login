<?php

session_start();

require_once '../vendor/autoload.php';


use app\core\Route;

try {

    Route::start();

} catch (Exception $e) {

    echo $e->getMessage();
}

