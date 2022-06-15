<?php

namespace Base\Controllers;

class BaseController
{
    public function renderView($moduleType, $controllerName, $view, $data = [])
    {
        require_once "../app/$moduleType/Views/$controllerName/$view.php";
    }
}
