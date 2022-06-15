<?php

namespace Api\Models;

class Contexts extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Contexts");
        $this->setTableId(2);
    }
}
