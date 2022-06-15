<?php

namespace Api\Models;

use Api\Config\Database;
use PDOException;

class Changes
{
    public static function getAllChangesByRequest(string $tableName, int $tableId, string $idList, int $userId)
    {
        $table = "ChangesOfRecords";
        $connection = Database::getInstance()->connect();

        $query = "  SELECT 
                        T1.*,
                        T2.changes_type,T2.id AS change_id
                    FROM
                        $tableName AS T1 
                    JOIN
                    (
                        SELECT 
                            *
                        FROM
                            $table 
                        WHERE 
                            id
                        IN (
                            SELECT 
                                MAX(id)
                            FROM
                                $table 
                            WHERE
                                user_id = :user_id
                            AND
                                table_id = :table_id
                            GROUP BY 
                                record_id
                            )
                    ) AS T2
                ON
                    T1.id = T2.record_id
                AND
                    FIND_IN_SET(T1.id, :ids_list)
        ";

        try {
            $statement = $connection->prepare($query);
            $statement->execute(
                [
                    ":user_id"      => $userId,
                    ":ids_list"     => $idList,
                    ":table_id"     => $tableId
                ]
            );
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public static function fetchAllAfterCurrentDate(string $lastDate, int $userId)
    {
        $table = "ChangesOfRecords";
        $connection = Database::getInstance()->connect();

        $query = "SELECT
                        * 
                FROM
                    $table 
                WHERE id IN 
                (
                    SELECT 
                        MAX(id)
                    FROM
                        $table 
                    WHERE  
                        id
                    IN (
                        SELECT 
                            Max(id)
                        FROM
                        $table
                        WHERE
                            register_date > :last_date
                        AND
                            user_id = :user_id 
                        GROUP BY 
                            table_id,
                            record_id,
                            changes_type
                    )
                    GROUP BY
                        record_id
                )
                ORDER BY 
                    register_date DESC
        ";

        try {
            $statement = $connection->prepare($query);
            $statement->execute(
                [
                    ":last_date"    => $lastDate,
                    ":user_id"      => $userId
                ]
            );
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }
}
