<?php

namespace Api\Controllers;

use Api\Controllers\BaseController as ApiController;
use Api\Models\Changes;

class ChangesController extends ApiController
{
    public function getChanges()
    {
        if ($this->requestMethod == 'GET') {
            // required parameters
            $userId   = $_SERVER['REQUESTER_ID'];
            $lastDate = $this->validateParams('last_date',$_GET['last_date'] ?? "", false);
            
            $lastDate = \urldecode($lastDate)??"";

            //check user exist
            if (empty($this->user->fetchAllById($userId))) {
                $this->throwError(401, "The user_id that you sent is unauthorized,."); 
            }

            // fetch coontexts
            $changes = new Changes();
            $result = $changes->fetchAllAfterCurrentDate($lastDate,$userId);
            //send result
            $this->returnResponse(200, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET.');
        }
    }
}
