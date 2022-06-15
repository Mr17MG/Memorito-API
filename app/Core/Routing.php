<?php

namespace Core;

class Routing
{
    public $routes = [
        //& ############### Site Route ###############
        [
            'route' => '/^$/',
            'module' => 'Base',
            'controller' => 'DefaultController',
            'action' => 'index',
        ],
        [
            'route' => '/index/',
            'module' => 'Base',
            'controller' => 'DefaultController',
            'action' => 'index',
        ],
        [
            'route' => '/blog/',
            'module' => 'Base',
            'controller' => 'DefaultController',
            'action' => 'blog',
        ],
        [
            'route' => '/post/',
            'module' => 'Base',
            'controller' => 'DefaultController',
            'action' => 'post',
        ],
        //? ############### API Route ###############

        //* START account routes 
        [
            'route' => '/api\/v1\/account\/signup$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'signup',
        ],
        [
            'route' => '/api\/v1\/account\/signin$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'signin',
        ],
        [
            'route' => '/api\/v1\/account\/validate-otp$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'validateOTP',
        ],
        [
            'route' => '/api\/v1\/account\/validate-token$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'validateToken',
        ],
        [
            'route' => '/api\/v1\/account\/delete-account\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'deleteAccount',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/users\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'getUser',
            'hasAuth' => true
        ],
        //|| END account routes 

        //* START password routes 
        [
            'route' => '/api\/v1\/password\/forget-pass$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'forgetPassword',
        ],
        [
            'route' => '/api\/v1\/password\/reset-pass$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'resetPassword',
        ],
        [
            'route' => '/api\/v1\/password\/resend-otp$/',
            'module' => 'Api',
            'controller' => 'UserController',
            'action' => 'resendOTP',
        ],
        //| END password routes 

        //* START contexts routes 
        [
            'route' => '/api\/v1\/contexts$/',
            'module' => 'Api',
            'controller' => 'ContextController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/contexts\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'ContextController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        //| END contexts routes 
        //* START categories routes
        [
            'route' => '/api\/v1\/category\/things$/',
            'module' => 'Api',
            'controller' => 'CategoryThingController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/category\/things\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'CategoryThingController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/category\/files$/',
            'module' => 'Api',
            'controller' => 'CategoryFileController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/category\/files\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'CategoryFileController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        //| END categories routes
        //* START friends routes
        [
            'route' => '/api\/v1\/friends$/',
            'module' => 'Api',
            'controller' => 'FriendController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/friends\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'FriendController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/friends\/search$/',
            'module' => 'Api',
            'controller' => 'FriendController',
            'action' => 'searchUser',
            'hasAuth' => true
        ],
        //| END friends routes
        //* START calendar routes
        [
            'route' => '/api\/v1\/calendar$/',
            'module' => 'Api',
            'controller' => 'CalendarController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/calendar\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'CalendarController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        //| END calendar routes
        //* START Waiting routes
        [
            'route' => '/api\/v1\/coop$/',
            'module' => 'Api',
            'controller' => 'CoopController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/coop\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'CoopController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        //| END Waiting routes
        //* START things routes 
        [
            'route' => '/api\/v1\/things$/',
            'module' => 'Api',
            'controller' => 'ThingController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/things\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'ThingController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        //| END things routes 

        //* START things routes 
        [
            'route' => '/api\/v1\/files$/',
            'module' => 'Api',
            'controller' => 'FileController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/files\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'FileController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        //| END things routes 
        //* START things routes
        [
            'route' => '/api\/v1\/logs$/',
            'module' => 'Api',
            'controller' => 'LogController',
            'action' => 'splitByMethod',
            'hasAuth' => true
        ],
        [
            'route' => '/api\/v1\/logs\/[0-9]+$/',
            'module' => 'Api',
            'controller' => 'LogController',
            'action' => 'splitByMethodAndId',
            'hasAuth' => true
        ],
        //| END things routes
        [
            'route' => '/api\/v1\/changes/',
            'module' => 'Api',
            'controller' => 'ChangesController',
            'action' => 'getChanges',
            'hasAuth' => true
        ]
    ];

    public function __construct()
    {
        return $this->routes;
    }
}
