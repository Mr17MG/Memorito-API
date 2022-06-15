<?php

namespace Base\Controllers;

use Base\Controllers\BaseController as WebController;

class DefaultController extends WebController
{
    public function index()
    {
        parent::renderView('Base', 'default', 'index');
    }

    public function blog($param = null)
    {
        parent::renderView('Base', 'default', 'blog', [
            'param' => $param,
        ]);
    }

    public function post($param = null)
    {
        if (file_exists('../app/' . 'Base' . '/Views/default/' . 'post-' . $_GET["p"] . '.php'))
            parent::renderView('Base', 'default', 'post-' . $_GET["p"]);
        else $this->notfound();
    }

    public function notfound()
    {
        parent::renderView('Base', 'default', 'notfound');
    }
}
