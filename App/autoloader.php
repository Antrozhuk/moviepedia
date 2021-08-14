<?php

namespace App;

use Core\Database;
use Core\Router;

class autoloader

{

    public $router;

    public static $database;

    public static function init()
    {
        require 'consts.php';
        $router = new Router();
        static::$database = new Database();
        $router->launch();
    }

}