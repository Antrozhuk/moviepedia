<?php

namespace Core;

use App\routs\Routs;

class Router

{
    public function launch()
    {
        if (($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
            $url = substr($_SERVER['REQUEST_URI'], 0, $pos);
        }
        $url = is_null($url) ? $_SERVER['REQUEST_URI'] : $url;

        $route = Routs::ROUTS[$url];

        $route = explode('@', $route);
        if ($route[0]) {
            $controllerName = array_shift($route);
            $actionName = array_shift($route);
        } else {
            $controllerName = 'Error';
            $actionName = 'error404';
        }

        require_once ROOTPATH . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
        $controllerName = "Controllers\\" . $controllerName;
        $controller = new $controllerName();
        return $controller->$actionName();
    }

}