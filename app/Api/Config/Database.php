<?php

namespace Api\Config;

use PDO, PDOException;

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $connection = null;

    private static $instance = null;

    private function __construct()
    {
        $ini = parse_ini_file("database.ini");
        $this->host     = $ini['dbms_host'];
        $this->dbname   = $ini['dbms_database'];
        $this->username = $ini['dbms_username'];
        $this->password = $ini['dbms_password'];

        try {
            $this->connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {

            header('Content-Type: application/json');
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode([
                'ok' => false,
                'message' => $exception->getMessage(),
            ]);
            die;
        }
    }

    public function connect()
    {
        return $this->connection;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}
