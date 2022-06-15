<?php

namespace Api\Models;

use PDOException;
use Api\Config\Database;

class Calendar extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Calendar");
        $this->setTableId(1);
    }

    public function getAllJoinedById(int $id)
    {
        $tableName = $this->getTable();
        $connection = Database::getInstance()->connect();

        $query = "SELECT
                        T2.*,
                        T1.due_date,
                        T1.id AS calendar_id,
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

            $res['id'] = $res['calendar_id'];
            unset($res['calendar_id']);

            return $res;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }
}
