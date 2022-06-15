<?php

namespace Api\Controllers;

use Api\Config\Database;
use Api\Models\Users;

use PDOException;

class BaseController
{
    public $requestMethod;
    public $request;
    public $user;

    public function __construct()
    {
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->request = (array) json_decode(file_get_contents('php://input'), true);
        $this->user = new Users();
    }

    private $http = array(
        100 => 'HTTP/1.1 100 Continue',                         // ?
        101 => 'HTTP/1.1 101 Switching Protocols',              // ?
        200 => 'HTTP/1.1 200 OK',                               // *
        201 => 'HTTP/1.1 201 Created',                          // *
        202 => 'HTTP/1.1 202 Accepted',                         // *
        203 => 'HTTP/1.1 203 Non-Authoritative Information',    // *
        204 => 'HTTP/1.1 204 No Content',                       // *
        205 => 'HTTP/1.1 205 Reset Content',                    // *
        206 => 'HTTP/1.1 206 Partial Content',                  // *
        300 => 'HTTP/1.1 300 Multiple Choices',                 // ^
        301 => 'HTTP/1.1 301 Moved Permanently',                // ^
        302 => 'HTTP/1.1 302 Found',                            // ^
        303 => 'HTTP/1.1 303 See Other',                        // ^
        304 => 'HTTP/1.1 304 Not Modified',                     // ^
        305 => 'HTTP/1.1 305 Use Proxy',                        // ^
        307 => 'HTTP/1.1 307 Temporary Redirect',               // ^
        400 => 'HTTP/1.1 400 Bad Request',                      // !
        401 => 'HTTP/1.1 401 Unauthorized',                     // !
        402 => 'HTTP/1.1 402 Payment Required',                 // !
        403 => 'HTTP/1.1 403 Forbidden',                        // !
        404 => 'HTTP/1.1 404 Not Found',                        // !
        405 => 'HTTP/1.1 405 Method Not Allowed',               // !
        406 => 'HTTP/1.1 406 Not Acceptable',                   // !
        407 => 'HTTP/1.1 407 Proxy Authentication Required',    // !
        408 => 'HTTP/1.1 408 Request Time-out',                 // !
        409 => 'HTTP/1.1 409 Conflict',                         // !
        410 => 'HTTP/1.1 410 Gone',                             // !
        411 => 'HTTP/1.1 411 Length Required',                  // !
        412 => 'HTTP/1.1 412 Precondition Failed',              // !
        413 => 'HTTP/1.1 413 Request Entity Too Large',         // !
        414 => 'HTTP/1.1 414 Request-URI Too Large',            // !
        415 => 'HTTP/1.1 415 Unsupported Media Type',           // !
        416 => 'HTTP/1.1 416 Requested Range Not Satisfiable',  // !
        417 => 'HTTP/1.1 417 Expectation Failed',               // !
        500 => 'HTTP/1.1 500 Internal Server Error',            // |
        501 => 'HTTP/1.1 501 Not Implemented',                  // |
        502 => 'HTTP/1.1 502 Bad Gateway',                      // |
        503 => 'HTTP/1.1 503 Service Unavailable',              // |
        504 => 'HTTP/1.1 504 Gateway Time-out',                 // |
        505 => 'HTTP/1.1 505 HTTP Version Not Supported',       // |
    );
    public function throwError(int $statusCode, string $message)
    {
        header('Content-Type: application/json');
        header($this->http[$statusCode]);

        echo json_encode([
            'ok' => false,
            'message' => $message,
        ]);
        die;
    }

    public function validateRequest()
    {
        if (($_SERVER["CONTENT_TYPE"] ?? null) != 'application/json'
            && ($_SERVER["CONTENT_TYPE"] ?? null) != 'application/json;charset=UTF-8'
        ) {
            $this->throwError(403, 'The only acceptable content type is application/json.');
        }
    }
    public function validateRequiredParam($fieldName)
    {
        $value = $this->request[$fieldName] ?? null;
        if (isset($value) == false || $value == "") {
            $this->throwError(400, "The '$fieldName' field is required.");
        }
        return $value;
    }
    public function validateParams($fieldName, $value, $isRequired = false)
    {
        if ($isRequired == true && (isset($value) == false || $value == "")) {
            $this->throwError(400, "The '$fieldName' field is required.");
        }
        return $value;
    }

    public function validatePermision(int $userIdItem)
    {
        if ($_SERVER['REQUESTER_ID'] != $userIdItem)
            $this->throwError(403, "You can't access to this item.");
    }

    public function validateCoopPermision(int $userId, int $thingId)
    {
        $query = "SELECT Count(Things.id) FROM Things
        JOIN Coop ON Things.id = Coop.thing_id  AND Things.id = :thing_id
        JOIN Friends on Friends.id = Coop.friend_id 
        WHERE Friends.friend1 = :user_id OR Friends.friend2 = :user_id";

        try {
            $connection = Database::getInstance()->connect();

            $statement = $connection->prepare($query);
            $statement->execute(
                [
                    ":thing_id" => $thingId,
                    ":user_id" => $userId
                ]
            );
            $rs = $statement->fetch(\PDO::FETCH_ASSOC);
            if (empty($rs)) {
                $this->throwError(403, "You can't access to this item.");
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function returnResponse($statusCode, $result = null)
    {
        header('Content-Type: application/json');
        header($this->http[$statusCode]);

        if (!empty($result))
            ksort($result);

        echo json_encode([
            'ok'        => true,
            'result'    => $result
        ], JSON_NUMERIC_CHECK);
        die;
    }

    public function notfound()
    {
        $this->throwError(404, 'The endpoint you are looking for is not found.');
    }
}
