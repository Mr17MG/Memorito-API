<?php

namespace Api\Models;

use Api\Config\Database;
use PDOException;

class BaseModel
{
    private $connection;
    private $table;
    private $tableIdInChanges;

    public function __construct()
    {
        $this->connection = Database::getInstance()->connect();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }


    public function setTableId(int $tableIdInChanges)
    {
        $this->tableIdInChanges = $tableIdInChanges;
    }

    public function insert(array $data)
    {
        foreach ($data as $key => $value) {
            $columns_var[] =  ":" . $key;
        }
        $columns_var = join(", ", $columns_var);
        $columnsName = str_replace(":", "", $columns_var);
        $query = " INSERT INTO  $this->table ( $columnsName ) VALUES ( $columns_var ); ";
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($data);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            header('HTTP/1.1 500 Internal Server Error');
                echo json_encode([
                    'ok' => false,
                    'message' => $e->getMessage(),
                ]);
                die;
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        foreach ($data as $key => $value) {
            $updateQuery[] = $key . "=" . ":" . $key;
        }
        $updateQuery = join(", ", $updateQuery);
        $data["id"] = $id;

        $query = "UPDATE $this->table SET $updateQuery WHERE id = :id ";

        try {
            $statement = $this->connection->prepare($query);
            $statement = $statement->execute($data);
            return $statement;
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            header('HTTP/1.1 500 Internal Server Error');
                echo json_encode([
                    'ok' => false,
                    'message' => $e->getMessage(),
                ]);
                die;
            return null;
        }
    }

    public function delete(int $id)
    {
        $query = " DELETE FROM $this->table WHERE id = ? ";
        try {
            $statement = $this->connection->prepare($query);
            return $statement->execute([$id]);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function fetchAllByConditions(array $data, string $conditions)
    {
        $query = "SELECT * FROM $this->table WHERE $conditions ";
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($data);
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function fetchAllByUserId(int $userId)
    {
        return $this->fetchAllByConditions([":user_id" => $userId], "user_id = :user_id");
    }

    public function fetchAllById(int $id)
    {
        return $this->fetchAllByConditions([":id" => $id], "id = :id")[0] ?? null;
    }

    public function getChanges(string $idList, int $userId)
    {
        return Changes::getAllChangesByRequest($this->table, $this->tableIdInChanges, $idList, $userId);
    }
}
