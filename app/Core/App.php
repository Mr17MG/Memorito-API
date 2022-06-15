<?php

namespace Core;

use Api\Controllers\BaseController as ApiController;
use Api\Models\UserSystems;
use Base\Controllers\DefaultController;

class App
{
    private $controller;
    private $action;

    private function parseUrl()
    {
        $request = trim($_SERVER['REQUEST_URI'], '/');
        $request = filter_var($request, FILTER_SANITIZE_URL);
        $request = explode('?', $request);
        $finalRequest["path"] = trim($request[0], '/');
        $finalRequest["params"] = $request[1] ?? "";

        return $finalRequest;
    }

    private function authorization()
    {
        $app = new ApiController;
        $app->validateRequest();

        if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
            $app->throwError(401, "Unauthorized");
        }

        $userSystem = new UserSystems;
        $user = $_SERVER['PHP_AUTH_USER'];
        $pass = $_SERVER['PHP_AUTH_PW'];
        $data = $userSystem->getByEmailToken($user, $pass);
        if (empty($data))
            $app->throwError(401, "Unauthorized");
        else $_SERVER['REQUESTER_ID'] = $data["user_id"];
    }

    public function __construct()
    {
        $url = $this->parseUrl();
        $routing = new Routing;
        if ($routing->routes) {
            $this->controller = new DefaultController;
            $this->action = 'index';
            foreach ($routing->routes as $route) {
                if (preg_match($route['route'].'i', $url["path"])) {
                    if (file_exists('../app/' . $route['module'] . '/Controllers/' . $route['controller'] . '.php')) {
                        if (isset($route['hasAuth']))
                            if ($route['hasAuth'] == true)
                                $this->authorization();

                        $dynamicControllerName = "\\" . $route['module'] . "\Controllers\\" . $route['controller'];
                        $this->controller = new $dynamicControllerName;
                        $this->action = $route['action'];
                        break;
                    } else {
                        $this->action = 'index';
                    }
                } else {
                    $this->action = 'notfound';
                }
            }
        }
        if ($this->action == "notfound" && strpos($url["path"], 'api') !== false) {
            $this->controller = new ApiController;
        }
        call_user_func_array([$this->controller, $this->action], []);
    }
}
