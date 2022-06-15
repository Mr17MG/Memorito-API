<?php

namespace Api\Models;

class Friends extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Friends");
        $this->setTableId(5);
    }

    public function getAllFriends(int $userId)
    {
        return $this->fetchAllByConditions(
            [":friend1" => $userId, ":friend2" => $userId],
            "friend1 = :friend1 OR friend2 = :friend2 "
        );
    }
}
