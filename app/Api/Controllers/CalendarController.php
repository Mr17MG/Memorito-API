<?php

namespace Api\Controllers;

use Api\Models\Things;
use Api\Models\Calendar;
use Api\Controllers\BaseController as ApiController;

class CalendarController extends ApiController
{
    function splitByMethod()
    {
        if ($this->requestMethod == 'POST') {
            //^ required parameters
            $userId     = $_SERVER['REQUESTER_ID'];

            $dueDate    = $this->validateRequiredParam('due_date');
            $dueDate    = !empty($due_date) ? date("Y-m-d H:i:s", strtotime(\urldecode($dueDate))) : null;

            $thingId    = $this->request['thing_id']  ?? null;

            if (empty($thingId)) {
                $title      = $this->validateRequiredParam('title');
                $typeId     = $this->validateRequiredParam('type_id');

                //| optional parameters
                $detail         = $this->request['detail'] ?? null;
                $status         = $this->request['status'] ?? null;
                $hasFiles       = $this->request['has_files'] ?? 0;
                $is_removed     = $this->request['is_removed'] ?? 0;
                $is_archived    = $this->request['is_archived'] ?? 0;
                $parent         = $this->request['parent_id'] ?? null;
                $energyId       = $this->request['energy_id'] ?? null;
                $contextId      = $this->request['context_id'] ?? null;
                $priorityId     = $this->request['priority_id'] ?? null;
                $estimatedTime  = $this->request['estimated_time'] ?? null;
                //& create thing

                $thing = new Things();
                $thingId = $thing->insert([
                    'title'          => $title,
                    'user_id'        => $userId,
                    'type_id'        => $typeId,
                    'detail'         => $detail,
                    'has_files'      => $hasFiles,
                    'status'         => $status,
                    'parent_id'      => $parent,
                    'energy_id'      => $energyId,
                    'context_id'     => $contextId,
                    'is_removed'     => $is_removed,
                    'is_archived'    => $is_archived,
                    'priority_id'    => $priorityId,
                    'estimated_time' => $estimatedTime,
                ]);
            }

            $calendar = new Calendar();
            $calendarId = $calendar->insert([
                "user_id" => $userId,
                "thing_id" => $thingId,
                "due_date" => $dueDate
            ]);

            //*send result
            $result = $calendar->getAllJoinedById(intval($calendarId));
            $this->returnResponse(201, $result);
            //& end if
        } else if ($this->requestMethod == 'GET') {

            $userId    = $_SERVER['REQUESTER_ID'];
            $calendarList = $_GET["calendar_id_list"] ?? null;

            $calendar = new Calendar();

            if (!empty($calendarList))
                $result = $calendar->getChanges($calendarList, $userId);
            else
                $result = $calendar->fetchAllByUserId($userId);

            //* send result
            $this->returnResponse(200, $result);
            //& end else if
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is POST and GET.');
            //& end else
        }
    }

    function splitByMethodAndId()
    {
        $calendarId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]); // get calendar id from URI
        if ($this->requestMethod == 'GET') {

            $calendar = new Calendar();
            $result = $calendar->fetchAllById($calendarId);

            $this->validatePermision($result["user_id"]);

            $this->returnResponse(200, $result);

            //& end if
        } else if ($this->requestMethod == 'DELETE') {
            $calendar = new Calendar();
            $result = $calendar->fetchAllById($calendarId);

            $this->validatePermision($result["user_id"]);

            if ($calendar->delete($calendarId))
                $this->returnResponse(204);
            else
                $this->throwError(404, "The calendar id not found.");
            //& end else if
        } else if ($this->requestMethod == 'PATCH') {

            $calendar = new Calendar();
            $this->validatePermision($calendar->fetchAllById($calendarId)["user_id"]);

            //^ required parameters
            $userId     = $_SERVER['REQUESTER_ID'];

            $dueDate    = $this->validateRequiredParam('due_date');
            $dueDate    = !empty($due_date) ? date("Y-m-d H:i:s", strtotime(\urldecode($dueDate))) : null;
            
            $thingId    = $this->request['thing_id']  ?? null;
            if (!empty($thingId)) {
                //^ required parameters
                $title    = $this->validateRequiredParam('title');
                $typeId   = $this->validateRequiredParam('type_id');

                //| optional parameters
                $detail         = $this->request['detail'] ?? null;
                $status         = $this->request['status'] ?? null;
                $hasFiles       = $this->request['has_files'] ?? 0;
                $is_removed     = $this->request['is_removed'] ?? 0;
                $is_archived    = $this->request['is_archived'] ?? 0;
                $parent         = $this->request['parent_id'] ?? null;
                $energyId       = $this->request['energy_id'] ?? null;
                $contextId      = $this->request['context_id'] ?? null;
                $priorityId     = $this->request['priority_id'] ?? null;
                $estimatedTime  = $this->request['estimated_time'] ?? null;

                $thing = new Things();
                $res = $thing->update([
                    'title'         => $title,
                    'user_id'       => $userId,
                    'type_id'       => $typeId,
                    'detail'        => $detail,
                    'has_files'     => $hasFiles,
                    'status'        => $status,
                    'parent_id'     => $parent,
                    'energy_id'     => $energyId,
                    'context_id'    => $contextId,
                    'is_removed'    => $is_removed,
                    'is_archived'   => $is_archived,
                    'priority_id'   => $priorityId,
                    'estimated_time' => $estimatedTime,
                ], $thingId);

                if (!$res) {
                    $this->throwError(500, "");
                }
            }

            $res = $calendar->update([
                "due_date" => $dueDate
            ], $calendarId);

            if ($res) {
                if (!empty($thingId))
                    $result = $calendar->getAllJoinedById($calendarId);
                else
                    $result = $calendar->fetchAllById($calendarId);
                $this->returnResponse(200, $result);
            } else {
                $this->throwError(500, "");
            }
            //& end else 
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET,PATCH,DELETE.');
            //& end else
        }
    }
}
