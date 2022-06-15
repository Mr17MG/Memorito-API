<?php

namespace Api\Controllers;

use Api\Controllers\BaseController as ApiController;
use Api\Models\Friends;
use Api\Models\Things;
use Api\Models\Coop;

class CoopController extends ApiController
{
    function splitByMethod()
    {
        if ($this->requestMethod == 'POST') {
            //^ required parameters
            $userId = $_SERVER['REQUESTER_ID'];

            $friendId = $this->validateRequiredParam('friend_id');

            $friends = new Friends();
            $friendsById = $friends->fetchAllById($friendId);

            if (empty($friendsById)) {
                $this->throwError(404, "The friendship not found by friend ID");
            } else {
                if ($friendsById["friendship_state"] != 2)
                    $this->throwError(406, "You can not add thing to this friend, Because you are not friend");
            }

            $thingId    = $this->request['thing_id']  ?? null;
            $thing = new Things();

            if (empty($thingId)) {
                ##### required parameters #####
                $title  = $this->validateRequiredParam('title');
                $typeId = $this->validateRequiredParam('type_id');

                ##### optional parameters #####
                $detail      = $this->request['detail']       ?? null;
                $parent      = $this->request['parent_id']    ?? null;
                $status      = $this->request['status']       ?? null;
                $displayType = $this->request['display_type'] ?? 0;

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
            }

            $this->validatePermision($thing->fetchAllById($thingId)["user_id"] ?? -1);

            $coop = new Coop();
            if (!empty($coop->fetchAllByConditions([":thing_id" => $thingId], "thing_id = :thing_id")))
                $this->throwError(409, "This thing id already added.");

            $coopId = $coop->insert([
                "user_id" => $userId,
                "thing_id" => $thingId,
                "friend_id" => $friendId
            ]);

            //* Sending response to user
            $result = $coop->getAllJoinedById(intval($coopId));
            $this->returnResponse(201, $result);
        } else if ($this->requestMethod == 'GET') {
            //^ required parameters
            $userId = $_SERVER['REQUESTER_ID'];

            $coop = new Coop();

            //* Sending response to user
            $result = $coop->getAllThingsByUser($userId);
            $this->returnResponse(200, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is POST and GET.');
        }
    }

    function splitByMethodAndId()
    {
        $coopId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]); // get coop id from URI
        if ($this->requestMethod == 'GET') {
            $userId     = $_SERVER['REQUESTER_ID'];

            $coop = new Coop();

            $result = $coop->fetchAllById($coopId);
            
            $this->validateCoopPermision($userId ,$result["thing_id"] ?? -1);

            // send result
            $this->returnResponse(200, $result);
        } else if ($this->requestMethod == 'DELETE') {
            $coop = new Coop();
            $result = $coop->fetchAllById($coopId);

            $this->validatePermision($result["user_id"]);

            if ($coop->delete($coopId))
                $this->returnResponse(204);
            else
                $this->throwError(404, "The coop id not found.");
        } else if ($this->requestMethod == 'PATCH') {

            $coop = new Coop();
            $this->validatePermision($coop->fetchAllById($coopId)["user_id"]);

            //^ required parameters
            $userId     = $_SERVER['REQUESTER_ID'];

            $friendId = $this->validateRequiredParam('friend_id');

            $friends = new Friends();
            $friendsById = $friends->fetchAllById($friendId);
            if (empty($friendsById)) {
                $this->throwError(404, "The friendship not found by friend ID");
            }

            $thingId    = $this->request['thing_id']  ?? null;
            if (!empty($thingId)) {
                //^ required parameters
                $title    = $this->validateRequiredParam('title');
                $typeId   = $this->validateRequiredParam('type_id');

                ##### optional parameters #####
                $detail      = $this->request['detail']       ?? null;
                $parent      = $this->request['parent_id']    ?? null;
                $status      = $this->request['status']       ?? null;
                $displayType = $this->request['display_type'] ?? 0;

                $energyId       = $this->request['energy_id']       ?? null;
                $contextId      = $this->request['context_id']      ?? null;
                $priorityId     = $this->request['priority_id']     ?? null;
                $estimatedTime  = $this->request['estimated_time']  ?? null;

                ##### Updating thing #####
                $thing = new Things();
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

                if (!$res) {
                    $this->throwError(500, "");
                }
            }

            $res = $coop->update([
                "friend_id" => $friendId
            ], $coopId);

            if ($res) {
                if (!empty($thingId))
                    $result = $coop->getAllJoinedById($coopId);
                else
                    $result = $coop->fetchAllById($coopId);
                $this->returnResponse(200, $result);
            } else {
                $this->throwError(500, "");
            }
            //& end else 
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET,PATCH,DELETE.');
        }
    }
}
