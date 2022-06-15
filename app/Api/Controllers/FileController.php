<?php

namespace Api\Controllers;

use Api\Controllers\BaseController as ApiController;
use Api\Models\Files;

class FileController extends ApiController
{
    function splitByMethod()
    {
        if ($this->requestMethod == 'POST') {            
            //* required parameters
            $userId     = $_SERVER['REQUESTER_ID'];

            $thingId    = $this->validateRequiredParam('thing_id');
            $fileList   = $this->validateParams('file_list', is_array($this->request['file_list']) ? $this->request['file_list'] : null, true);

            $file = new Files();
            $result = [];

            foreach ($fileList as $rowFile) {
                $fileName       = $this->validateParams('file_name',        $rowFile["file_name"]       ?? null, true);
                $base64File     = $this->validateParams('base64_file',      $rowFile["base64_file"]     ?? null, true);
                $fileExtension  = $this->validateParams('file_extension',   $rowFile["file_extension"]  ?? null, true);

                $fileId = $file->insert([
                    "user_id" => $userId,
                    "file" => $base64File,
                    "file_name" => $fileName,
                    "file_extension" => $fileExtension,
                    "thing_id" => $thingId
                ]);

                $result[] = $file->fetchAllById(intval($fileId));
            }
            $this->returnResponse(201, $result);
            //^ end if POST
        } else if ($this->requestMethod == 'GET') {            

            $userId   = $_SERVER['REQUESTER_ID'];
            $fileList = $_GET["file_id_list"] ?? null;
            $thingId  = $this->validateParams('thing_id', $_GET["thing_id"] ?? null, !empty($_GET["file_id_list"]) ? false : true);

            $file = new Files();

            if (!empty($fileList))
                $result = $file->getChanges($fileList, $userId);
            else
                $result = $file->getByUserThing($userId, $thingId);

            $this->returnResponse(200, $result);
            //^ end else if GET
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is POST and GET.');
        }
    }

    function splitByMethodAndId()
    {
        $fileId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]); //^ get file id from URI

        if ($this->requestMethod == 'GET') {            

            $file = new Files();

            $this->validatePermision($file->fetchAllById($fileId)["user_id"]);

            $result = $file->fetchAllById($fileId);

            $this->returnResponse(200, $result);
            //^ end if GET
        } else if ($this->requestMethod == 'DELETE') {
            $file = new Files();
            $this->validatePermision($file->fetchAllById($fileId)["user_id"]);

            if ($file->delete($fileId))
                $this->returnResponse(204);
            else
                $this->throwError(404, "The file id not found.");

            //^ end else if DELETE
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that is acceptable is GET,DELETE.');
        }
    }
}
