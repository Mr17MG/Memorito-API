<?php

namespace Api\Models;

use Api\Config\Database;
use PDOException;

class Things extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("Things");
        $this->setTableId(7);
    }
}
