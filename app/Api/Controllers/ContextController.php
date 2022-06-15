<?php

namespace Api\Controllers;

use Api\Controllers\BaseController as ApiController;
use Api\Models\Contexts;

class ContextController extends ApiController
{
    function splitByMethod()
    {
        if ($this->requestMethod == 'POST') {
            //^ required parameters
            $userId = $_SERVER['REQUESTER_ID'];
            $contextName = $this->validateRequiredParam('context_name');

            //check user exist
            if (empty($this->user->fetchAllById($userId))) {
                $this->throwError(401, "The user_id that you sent is unauthorized,.");
            }

            //create context
            $context = new Contexts();
            $contextId = $context->insert(["user_id" => $userId, "context_name" => $contextName]);

            //send result
            $result = $context->fetchAllById($contextId);
            $this->returnResponse(201, $result);
        } else if ($this->requestMethod == 'GET') {
            // required parameters
            $userId = $_SERVER['REQUESTER_ID'];

            $contextList = $_GET["context_id_list"] ?? null;

            // fetch coontexts
            $context = new Contexts();
            if (!empty($contextList))
                $result = $context->getChanges($contextList, $userId);
            else
                $result = $context->fetchAllByUserId($userId);

            //send result
            $this->returnResponse(200, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is POST and GET.');
        }
    }

    function splitByMethodAndId()
    {
        $contextId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]); // get contex id from URI
        if ($this->requestMethod == 'GET') {
            $context = new Contexts();
            $result = $context->fetchAllById($contextId);
            
            if (empty($result))
                $this->throwError(404, 'The context id not found.');

            $this->validatePermision($result["user_id"]);

            $this->returnResponse(200, $result);
        } else if ($this->requestMethod == 'DELETE') {
            $context = new Contexts();
            $result = $context->fetchAllById($contextId);
            $this->validatePermision($result["user_id"]);

            if ($context->delete($contextId))
                $this->returnResponse(204);
            else
                $this->throwError(404, "The context id not found.");
        } else if ($this->requestMethod == 'PATCH') {

            // required parameters
            $contextName   = $this->validateParams('context_name', $this->request['context_name'] ?? null, true);

            $userId   = $_SERVER['REQUESTER_ID'];

            $context = new Contexts();
            $this->validatePermision($context->fetchAllById($contextId)["user_id"]);

            $res = $context->update(["context_name" => $contextName], $contextId);
            if ($res) {
                $result = $context->fetchAllById($contextId);
                $this->returnResponse(200, $result);
            } else {
                $this->throwError(500, "");
            }
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET,PATCH,DELETE.');
        }
    }
}
