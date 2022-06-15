<?php

namespace Api\Models;

use Api\Config\Database;
use PDOException;

class Users extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Users");
        $this->setTableId(8);
    }

    public function searchUser(string $text)
    {
        $tableName = $this->getTable();
        $query = "  SELECT 
                        id, username, email, avatar
                    FROM 
                        $tableName
                    WHERE 
                        username=:text
                    OR 
                        email=:text
                ";

        // for users privacy it's commented
        // $query = "  SELECT 
        //                 id, username, email, avatar
        //             FROM 
        //                 $tableName
        //             WHERE 
        //                 username LIKE CONCAT( '%', :text, '%') 
        //             OR 
        //                 email LIKE CONCAT( '%', :text, '%')
        //         ";

        try {
            $statement = $this->getConnection()->prepare($query);
            $statement->execute(
                [
                    ":text" => $text
                ]
            );
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getByUsername(string $username)
    {
        return $this->fetchAllByConditions([":username" => $username], "username = :username")[0] ?? null;
    }
    public function getByEmail(string $email)
    {
        return $this->fetchAllByConditions([":email" => $email], "email = :email")[0] ?? null;
    }

    public function getByIdentifier(string $identifier)
    {
        return $this->fetchAllByConditions(
            [":email" => $identifier, ":username" => $identifier],
            "username = :email OR email = :email"
        )[0] ?? null;
    }

    public function getCompleteData(int $id, string $machineUniqueId)
    {
        $tableName = $this->getTable();
        $query = "  SELECT 
                        T1.*,
                        T2.auth_token as auth_token
                    FROM 
                        $tableName as T1 
                    JOIN 
                        UserSystems AS T2 
                    ON 
                        T1.id=T2.user_id 
                    AND
                        T1.id= :id 
                    AND 
                        machine_unique_id = :machine_unique_id
                ";
        try {
            $statement = $this->getConnection()->prepare($query);
            $statement->execute(
                [
                    ":id" => $id,
                    ":machine_unique_id" => $machineUniqueId
                ]
            );
            return $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function saveOtp(string $email, string $otp)
    {
        session_start();

        $_SESSION[$email]['OTP'] = $otp;
        $_SESSION[$email]["CREATED_TIME"] = time();
        return isset($_SESSION[$email]);
    }
    public function getSavedOtp(string $email)
    {
        if (session_status() != PHP_SESSION_ACTIVE)
            session_start();

        if (isset($_SESSION[$email])) {
            $lifetime =  time() - $_SESSION[$email]["CREATED_TIME"];

            if ($lifetime - 200 < 0) {
                $OTP = $_SESSION[$email]["OTP"];
                return $OTP;
            } else {
                unset($_SESSION[$email]);
            }
        }
        return NULL;
    }

    public function checkOTP(string $email, string $userOTP)
    {
        session_start();
        if (isset($_SESSION[$email])) {
            $savedOTP = $this->getSavedOtp($email);
            if ($savedOTP == $userOTP) {
                unset($_SESSION[$email]);
                return true;
            }
        }
        return false;
    }
}
