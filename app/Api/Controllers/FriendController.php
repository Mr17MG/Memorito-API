<?php

namespace Api\Controllers;

use Api\Controllers\BaseController as ApiController;
use Api\Models\Friends;

class FriendController extends ApiController
{
    function splitByMethod()
    {
        if ($this->requestMethod == 'POST') {
            // required parameters
            $userId = $_SERVER['REQUESTER_ID'];

            $friend1 = $this->validateParams('friend1', $this->request['friend1']  ?? intval($userId) ?? null, true);
            $friend2 = $this->request['friend2'] ?? null;

            $friend1Nickname = $this->validateParams(
                'friend1_nickname',
                $this->request['friend1_nickname']  ?? $this->user->fetchAllById(intval($friend1))["username"] ?? null,
                true
            );
            $friend2Nickname = $this->validateParams(
                'friend2_nickname',
                $this->request['friend2_nickname']  ?? $this->user->fetchAllById(intval($friend2))["username"] ?? null,
                true
            );

            $friendship = $this->request['friendship_state']  ?? 1;

            $friend = new Friends();
            $insertId = $friend->insert([
                "friend1" => $friend1,
                "friend1_nickname" => $friend1Nickname,
                "friend2" => $friend2,
                "friend2_nickname" => $friend2Nickname,
                "friendship_state" => $friendship
            ]);
            $result = $friend->fetchAllById($insertId);

            $this->returnResponse(201, $result);
            //^ end if POST
        } else if ($this->requestMethod == 'GET') {

            $userId  = $_SERVER['REQUESTER_ID'];
            $friendList = $_GET["friend_id_list"] ?? null;

            $friend = new Friends();
            if (!empty($friendList))
                $result = $friend->getChanges($friendList, $userId);
            else
                $result = $friend->getAllFriends($userId);

            $this->returnResponse(200, $result);
            //& end else if GET
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is POST and GET.');
        }
    }

    function splitByMethodAndId()
    {
        $friend = new Friends();
        $friendId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]); //^ get file id from URI
        $friendById = $friend->fetchAllById($friendId) ?? null;

        if(empty($friendById)){
            $this->throwError(404, "This friends row is not exist.");
        }

        if ($_SERVER['REQUESTER_ID'] != $friendById["friend1"] && $_SERVER['REQUESTER_ID'] != $friendById["friend2"]) {
            $this->throwError(403, "You can't access to this item.");
        }

        if ($this->requestMethod == 'GET') {
            $this->returnResponse(200, $friendById);
            // & end if GET
        } else if ($this->requestMethod == 'DELETE') {
            
            if ($friend->delete($friendId))
                $this->returnResponse(204);
            else
                $this->throwError(404, "The friend id not found.");

            // & end else if DELETE
        } else if ($this->requestMethod == 'PATCH') {
            $userId = $_SERVER['REQUESTER_ID'];

            $friend1 = $this->request['friend1'] ?? $friendById["friend1"];
            $friend2 = $this->request['friend2'] ?? $friendById["friend2"];

            $friend1Nickname = $this->request['friend1_nickname'] ?? $friendById["friend1_nickname"];
            $friend2Nickname = $this->request['friend2_nickname'] ?? $friendById["friend2_nickname"];
            $friendship      = $this->request['friendship_state'] ?? $friendById["friendship_state"] ?? 1;

            $res = $friend->update(
                [
                    "friend1" => $friend1,
                    "friend1_nickname" => $friend1Nickname,
                    "friend2" => $friend2,
                    "friend2_nickname" => $friend2Nickname,
                    "friendship_state" => $friendship
                ],
                $friendId
            );

            if ($res) {
                $result = $friend->fetchAllById($friendId);
                $this->returnResponse(200, $result);
            } else {
                $this->throwError(500, "");
            }
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET,PATCH,DELETE.');
        }
    }
    
    public function searchUser()
    {
        if ($this->requestMethod == 'GET') {

            $searchedText = $this->validateParams("searched_text",$_GET['searched_text']??"",true);

            //* Sending response to user
            $result = $this->user->searchUser($searchedText);
            if (empty($result))
                $this->throwError(404, "The user not found");
            else
                $this->returnResponse(200, $result);
        }else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is GET.');
        }
    }
}
