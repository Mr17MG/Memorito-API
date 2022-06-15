<?php

namespace Api\Models;

use PDOException;
use Api\Config\Database;

class Coop extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Coop");
        $this->setTableId(3);
    }

    public function getAllJoinedById(int $id)
    {
        $tableName = $this->getTable();
        $connection = Database::getInstance()->connect();

        $query = "SELECT
                        T2.*,
                        T1.friend_id,
                        T1.id AS coop_id,
                        T1.thing_id
                    FROM
                        $tableName AS T1
                    JOIN
                        Things AS T2
                    ON 
                        T1.thing_id = T2.id  
                    AND
                        T1.user_id = T2.user_id
                    AND 
                        T1.id = :id
                    ";
        try {
            $statement = $connection->prepare($query);
            $statement->execute(
                [":id" => $id]
            );

            $res = $statement->fetch(\PDO::FETCH_ASSOC);

            $res['id'] = $res['coop_id'];
            unset($res['coop_id']);

            return $res;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getAllThingsByUser(int $id)
    {
        $tableName = $this->getTable();
        $connection = Database::getInstance()->connect();

        $query = "SELECT 
                        T1.friend_id, T1.id, T1.thing_id
                FROM
                    Coop AS T1
                JOIN 
                    Friends AS T3
                ON
                    T3.friend1 = :id
                OR 
                    T3.friend2 = :id;
                ";
        try {
            $statement = $connection->prepare($query);
            $statement->execute(
                [":id" => $id]
            );

            $res = $statement->fetchAll(\PDO::FETCH_ASSOC);

            return $res;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }
}
