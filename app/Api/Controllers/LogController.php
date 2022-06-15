<?php

namespace Api\Controllers;

use Api\Models\Logs;
use Api\Controllers\BaseController as ApiController;

class LogController extends ApiController
{
    function splitByMethod()
    {
        if ($this->requestMethod == 'POST') {            

            $userId     = $_SERVER['REQUESTER_ID'];
            $thingId    = $this->validateRequiredParam('thing_id');
            $logText    = $this->validateRequiredParam('log_text');

            $log = new Logs();
            $logId = $log->insert(
                [
                    "user_id" => $userId,
                    "thing_id" => $thingId,
                    "log_text" => $logText
                ]
            );

            $result = $log->fetchAllById(intval($logId));
            $this->returnResponse(201, $result);
        } else if ($this->requestMethod == 'GET') {

            $userId  = $_SERVER['REQUESTER_ID'];
            
            $logList = $_GET["log_id_list"] ?? null;
            $thingId = $this->validateParams('thing_id', $_GET["thing_id"] ?? null, $logList ? false : true);

            $log = new Logs();
            if (!empty($logList))
                $result = $log->getChanges($logList, $userId);
            else
                $result = $log->getByUserThing($userId, $thingId);

            $this->returnResponse(200, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is POST and GET.');
        }
    }

    function splitByMethodAndId()
    {
        $logId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]); // get contex id from URI
        if ($this->requestMethod == 'GET') {

            $log = new Logs();
            $result = $log->fetchAllById($logId);

            $this->validatePermision($result["user_id"]);

            $this->returnResponse(200, $result);
        } else if ($this->requestMethod == 'DELETE') {

            $log = new Logs();
            $this->validatePermision($log->fetchAllById($logId)["user_id"]);

            if ($log->delete($logId))
                $this->returnResponse(204);
            else
                $this->throwError(404, "The log id not found.");
        } else if ($this->requestMethod == 'PATCH') {            

            $logText   = $this->validateParams('log_text', $this->request['log_text'] ?? null, true);

            $log = new Logs();
            $this->validatePermision($log->fetchAllById($logId)["user_id"]);

            $res = $log->update(
                ["log_text" => $logText],
                $logId
            );
            if ($res) {
                $result = $log->fetchAllById($logId);
                $this->returnResponse(200, $result);
            } else {
                $this->throwError(500, "");
            }
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET,PATCH,DELETE.');
        }
    }
}
