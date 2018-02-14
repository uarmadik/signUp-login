<?php

session_start();

error_reporting( E_ALL );
require_once '../vendor/autoload.php';

use app\core\Route;

echo 'test';

try {

    Route::start();

} catch (Exception $e) {

    echo $e->getMessage();
}

echo 'test 2';