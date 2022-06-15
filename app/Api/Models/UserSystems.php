<?php

namespace Api\Models;

class UserSystems extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("UserSystems");
        $this->setTableId(9);
    }

    public function getByMachineId(int $userId, string $machineId)
    {
        return $this->fetchAllByConditions([
            ":user_id" => $userId,
            ":machine_unique_id" => $machineId
        ], "user_id = :user_id AND machine_unique_id = :machine_unique_id");
    }
    public function hasExistToken(string $token)
    {
        return empty($this->fetchAllByConditions([":auth_token" => $token], "auth_token = :auth_token"));
    }

    public function getAll(int $userId, string $machineUniqueId)
    {
        return $this->fetchAllByConditions([":user_id" => $userId, ":machine_unique_id" => $machineUniqueId], "user_id = :user_id AND machine_unique_id = :machine_unique_id ")[0];
    }

    public function getByTokenMahine(string $authToken, string $machineId)
    {
        return $this->fetchAllByConditions([
            ":auth_token" => $authToken,
            ":machine_unique_id" => $machineId
        ], "auth_token = :auth_token AND machine_unique_id = :machine_unique_id")[0] ?? null;
    }

    public function updateLastOnline($id)
    {
        return $this->update(["last_online" => date('Y-m-d H:i:s', time())], $id);
    }

    public function getByEmailToken(string $email, string $token)
    {
        return $this->fetchAllByConditions([
            ":auth_token" => $token,
            ":email" => $email
        ], "auth_token = :auth_token AND email = :email")[0] ?? null;
    }
}
