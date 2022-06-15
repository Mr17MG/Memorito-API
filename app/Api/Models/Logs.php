<?php

namespace Api\Models;

class Logs extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Logs");
        $this->setTableId(6);
    }
    
    public function getByUserThing(int $userId, int $thingId)
    {
        return $this->fetchAllByConditions(
            [":user_id" => $userId, ":thing_id" => $thingId],
            "user_id = :user_id AND thing_id = :thing_id "
        )[0] ?? null;
    }
}
