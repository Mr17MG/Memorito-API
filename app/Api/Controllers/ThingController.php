<?php

namespace Api\Controllers;

use Api\Models\Things;
use Api\Controllers\BaseController as ApiController;

class ThingController extends ApiController
{
    function splitByMethod()
    {
        if ($this->requestMethod == 'POST') {
            ##### required parameters #####
            $userId = $_SERVER['REQUESTER_ID'];
            $title  = $this->validateRequiredParam('title');
            $typeId = $this->validateRequiredParam('type_id');

            ##### optional parameters #####
            $detail      = $this->request['detail']       ?? null;
            $parent      = $this->request['parent_id']    ?? null;
            $status      = $this->request['status']       ?? null;
            $displayType = $this->request['display_type'] ?? 1;

            $energyId       = $this->request['energy_id']       ?? null;
            $contextId      = $this->request['context_id']      ?? null;
            $priorityId     = $this->request['priority_id']     ?? null;
            $estimatedTime  = $this->request['estimated_time']  ?? null;

            ##### creating a new thing #####
            $thing = new Things();
            $thingId = $thing->insert([
                'title'          => $title,
                'user_id'        => $userId,
                'type_id'        => $typeId,
                'detail'         => $detail,
                'status'         => $status,
                'parent_id'      => $parent,
                'display_type'   => $displayType,
                'energy_id'      => $energyId,
                'context_id'     => $contextId,
                'priority_id'    => $priorityId,
                'estimated_time' => $estimatedTime,
            ]);

            ##### Getting result based on Status #####
            $result = $thing->fetchAllById(intval($thingId));

            ##### Sending response to user #####
            $this->returnResponse(201, $result);
        } ##### end if #####
        else if ($this->requestMethod == 'GET') {
            $thing = new Things();

            $userId    = $_SERVER['REQUESTER_ID'];
            $thingList = $_GET["thing_id_list"] ?? null;

            if (!empty($thingList)) {
                foreach (explode(",", $thingList) as $id) {
                    $r = $thing->fetchAllById(intval($id));
                    if (!empty($r))
                        $this->validatePermision($r["user_id"] ?? -1);
                }
                $result = $thing->getChanges($thingList, $userId);
            } else
                $result = $thing->fetchAllByUserId($userId);

            ##### Sending response to user #####
            $this->returnResponse(200, $result);
        } ##### end else if #####
        else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is POST and GET.');
        } ##### end else #####
    }

    function splitByMethodAndId()
    {
        ##### getting thing id from URI #####
        $thingId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]);

        if ($this->requestMethod == 'GET') {

            $thing = new Things();
            $result = $thing->fetchAllById($thingId);

            if (empty($result)) {
                $this->throwError(404, "The thing you are looking for is not found.");
            }

            $this->validatePermision($result["user_id"]);

            $this->returnResponse(200, $result);
        }
        ##### end if GET
        else if ($this->requestMethod == 'DELETE') {
            $userId   = $_SERVER['REQUESTER_ID'];

            $thing = new Things();
            if ($thing->delete($thingId))
                $this->returnResponse(204);
            else
                $this->throwError(404, "The thing you are looking for is not found.");
        }
        ##### end else if DELETE
        else if ($this->requestMethod == 'PATCH') {

            $thing = new Things();
            $this->validatePermision($thing->fetchAllById($thingId)["user_id"]);

            ##### required parameters #####
            $userId   = $_SERVER['REQUESTER_ID'];
            $title    = $this->validateRequiredParam('title');
            $typeId   = $this->validateRequiredParam('type_id');

            ##### optional parameters #####
            $detail      = $this->request['detail']       ?? null;
            $parent      = $this->request['parent_id']    ?? null;
            $status      = $this->request['status']       ?? null;
            $displayType = $this->request['display_type'] ?? 1;

            $energyId       = $this->request['energy_id']       ?? null;
            $contextId      = $this->request['context_id']      ?? null;
            $priorityId     = $this->request['priority_id']     ?? null;
            $estimatedTime  = $this->request['estimated_time']  ?? null;

            ##### Updating thing #####
            $res = $thing->update([
                'title'          => $title,
                'user_id'        => $userId,
                'type_id'        => $typeId,
                'detail'         => $detail,
                'status'         => $status,
                'parent_id'      => $parent,
                'display_type'   => $displayType,
                'energy_id'      => $energyId,
                'context_id'     => $contextId,
                'priority_id'    => $priorityId,
                'estimated_time' => $estimatedTime,
            ], $thingId);

            ##### Sending response to user #####
            if ($res) {
                $result = $thing->fetchAllById($thingId);
                $this->returnResponse(200, $result);
            } else {
                $this->throwError(500, "Error in updating thing");
            }
        } ##### end else 
        else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET,PATCH,DELETE.');
        } ##### end else
    }
}
