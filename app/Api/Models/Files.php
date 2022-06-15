<?php

namespace Api\Models;

class Files extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Files");
        $this->setTableId(4);
    }

    public function getByUserThing(int $userId, int $thingId)
    {
        return $this->fetchAllByConditions(
            [":user_id" => $userId, ":thing_id" => $thingId],
            "user_id = :user_id AND thing_id = :thing_id "
        )[0] ?? null;
    }
}
