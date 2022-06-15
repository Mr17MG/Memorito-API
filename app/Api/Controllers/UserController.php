<?php

namespace Api\Controllers;

use Api\Controllers\BaseController as ApiController;
use Api\Models\UserSystems;
use Exception;

class UserController extends ApiController
{
    private function SHA3_hash($password)
    {
        $salt = "553tjr!EcnW242x8OPbk";
        return hash('sha3-512', $password . $salt, false);
    }

    private function generateToken()
    {
        $sysInfo = new UserSystems();
        $token = "";
        do {
            $token = bin2hex(random_bytes(32));
        } while (!$sysInfo->hasExistToken($token));
        unset($sysInfo);
        return $token;
    }

    private function generateOTP()
    {
        $randomNum = mt_rand(0, 999999);
        $otp = "";
        for ($i = 0; $i < (6 - strlen((string)$randomNum)); $i++)
            $otp .= "0";
        $otp .= $randomNum;
        unset($randomNum);
        return $otp;
    }

    private function getGravatar(string $email, int $size = 512)
    {
        $email_hash =  md5(strtolower(trim($email)));

        $str = file_get_contents('https://www.gravatar.com/' . $email_hash . '.php');
        $profile = unserialize($str);

        if (is_array($profile) && isset($profile['entry'])) {
            $image = $profile["entry"][0]["thumbnailUrl"] . "?s=" . $size;
            return $image = base64_encode($image);
        }
        return NULL;
    }

    private function checkUserByIdentifier(string $identifier)
    {
        $userByIdentifier = $this->user->getByIdentifier($identifier) ?? null;
        //check availability of user
        if (empty($userByIdentifier)) {
            $this->throwError(401, "The email or username is'nt available, please enter correct username or email");
        }
        return $userByIdentifier;
    }

    public function signup()
    {
        if ($this->requestMethod == 'POST') {

            //* required parameters
            $email      = $this->validateRequiredParam('email');
            $username   = $this->validateRequiredParam('username');
            $password   = $this->validateRequiredParam('password');

            $appName    = $this->validateRequiredParam('app_name');
            $appVersion = $this->validateRequiredParam('app_version');

            $machineUniqueId = $this->validateRequiredParam('machine_unique_id');

            //^ optional parameters
            //| os_name , cpu_arch , os_version , kernel_version

            //^ Checking availability of username
            $byUsername = $this->user->getByUsername($username);
            if (!empty($byUsername)) {
                if ($byUsername["is_activated"] == 1)
                    $this->throwError(409, "This username isn't available. Please try another");
                else
                    $this->user->delete(intval($byUsername["id"]));
            }
            unset($byUsername);

            //^ Checking availability of email
            $byEmail = $this->user->getByEmail($email);
            if (!empty($byEmail)) {
                if ($byEmail["is_activated"] == 1)
                    $this->throwError(409, "Another account is using this email: $email");
                else
                    $this->user->delete(intval($byEmail["id"]));
            }
            unset($byEmail);

            //* Creating a new user.
            $password = $this->SHA3_hash($password);
            $createdUserId = $this->user->insert(
                [
                    'username' => $username,
                    'email' => $email,
                    'hashed_password' => $password,
                    'avatar' => $this->getGravatar($email)
                ]
            );

            //* Saving user's system informations
            $sysInfo = new UserSystems();

            $token = $this->generateToken();
            $hasExistInfo = $sysInfo->getByMachineId($createdUserId, $machineUniqueId);

            if (!empty($hasExistInfo)) {
                $sysInfo->update(
                    [
                        'app_name'          => $appName,
                        'app_version'       => $appVersion,
                        'auth_token'        => $token,
                        'email'             => $email,
                        'user_id'           => $createdUserId,
                        'machine_unique_id' => $machineUniqueId,
                        'os_name'           => $this->request['os_name'] ?? "",
                        'cpu_arch'          => $this->request['cpu_arch'] ?? "",
                        'os_version'        => $this->request['os_version'] ?? "",
                        'kernel_version'    => $this->request['kernel_version'] ?? ""
                    ],
                    $hasExistInfo['id']
                );
            } else {
                $sysInfo->insert([
                    'app_name'          => $appName,
                    'app_version'       => $appVersion,
                    'auth_token'        => $token,
                    'email'             => $email,
                    'user_id'           => $createdUserId,
                    'machine_unique_id' => $machineUniqueId,
                    'os_name'           => $this->request['os_name'] ?? "",
                    'cpu_arch'          => $this->request['cpu_arch'] ?? "",
                    'os_version'        => $this->request['os_version'] ?? "",
                    'kernel_version'    => $this->request['kernel_version'] ?? ""
                ]);
            }
            /*****************************/

            //* Sending otp code to user's mail
            $otp = $this->generateOTP();

            $mailSender = new MailController();
            $mailSender->sendOtpMail($otp, $email);

            //* Saving OTP on SESSION
            $this->user->saveOtp($email, $otp);
            $result = array("email"=>$email);
            //* Sending response to user
            $this->returnResponse(201,$result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function validateOTP()
    {
        if ($this->requestMethod == 'POST') {
            //* required parameters
            $otp             = $this->validateRequiredParam('otp');
            $identifier      = $this->validateRequiredParam('identifier');
            $machineUniqueId = $this->validateRequiredParam('machine_unique_id');

            //* Checking user by Id or email
            $userByIdentifier = $this->checkUserByIdentifier($identifier);

            //* Checking OTP code correctness
            if ($this->user->checkOTP($userByIdentifier["email"], $otp) == FALSE) {
                $this->throwError(403, 'Your OTP is wrong, please send me right OTP');
            }

            //* activating user's account
            $this->user->update(["is_activated" => 1], intval($userByIdentifier['id']));

            //* Sending response to user
            $result = $this->user->getCompleteData(intval($userByIdentifier["id"]), $machineUniqueId);
            $this->returnResponse(202, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function signin()
    {
        if ($this->requestMethod == 'POST') {
            //* required parameters
            $password        = $this->validateRequiredParam('password');
            $identifier      = $this->validateRequiredParam('identifier');
            $machineUniqueId = $this->validateRequiredParam('machine_unique_id');

            $appName    = $this->validateRequiredParam('app_name');
            $appVersion = $this->validateRequiredParam('app_version');

            //^ optional parameters
            //| os_name , cpu_arch , os_version , kernel_version

            //* Checking user by Id or email
            $userByIdentifier = $this->checkUserByIdentifier($identifier);

            //* checking password correctness
            $password = $this->SHA3_hash($password);
            if ($password != $userByIdentifier["hashed_password"]) {
                $this->throwError(401, "The password is incorrect, please enter correct password");
            }

            //* Saving user's system informations
            $sysInfo = new UserSystems();

            $token = $this->generateToken();
            $hasExistInfo = $sysInfo->getAll(intval($userByIdentifier["id"]), $machineUniqueId);

            if (!empty($hasExistInfo)) {
                $sysInfo->update(
                    [
                        'auth_token'        => $token,
                        'app_name'          => $appName,
                        'app_version'       => $appVersion,
                        'machine_unique_id' => $machineUniqueId,
                        'user_id'           => $userByIdentifier["id"],
                        'email'             => $userByIdentifier["email"],
                        'cpu_arch'          => $this->request['cpu_arch'] ?? "",
                        'os_name'           => $this->request['os_name'] ?? "",
                        'os_version'        => $this->request['os_version'] ?? "",
                        'kernel_version'    => $this->request['kernel_version'] ?? "",
                    ],
                    intval($hasExistInfo['id'])
                );
            } else {
                $sysInfo->insert(
                    [
                        'auth_token'        => $token,
                        'app_name'          => $appName,
                        'app_version'       => $appVersion,
                        'machine_unique_id' => $machineUniqueId,
                        'user_id'           => $userByIdentifier["id"],
                        'email'             => $userByIdentifier["email"],
                        'os_name'           => $this->request['os_name'] ?? "",
                        'cpu_arch'          => $this->request['cpu_arch'] ?? "",
                        'os_version'        => $this->request['os_version'] ?? "",
                        'kernel_version'    => $this->request['kernel_version'] ?? "",
                    ]
                );
            }

            // Send Mail
            if (!empty($userByIdentifier["two_step"])) {

                $otp = $this->generateOTP();
                $email = $userByIdentifier["email"];

                $sender = new MailController();
                $sender->sendOtpMail($otp, $email);

                //* Saving OTP on SESSION
                $this->user->saveOtp($email, $otp);

                //* Sending response to user
                $result = ["email" => $email];
                $this->returnResponse(200, $result);
            } else {

                //* Sending response to user
                $result = $this->user->getCompleteData(intval($userByIdentifier["id"]), $machineUniqueId);
                $this->returnResponse(202, $result);
            }
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function validateToken()
    {
        if ($this->requestMethod == 'POST') {
            $authToken       = $this->validateRequiredParam('auth_token');
            $machineUniqueId = $this->validateRequiredParam('machine_unique_id');

            $sysInfo = new UserSystems();
            $infoByToken = $sysInfo->getByTokenMahine($authToken, $machineUniqueId);

            if (empty($infoByToken)) {
                $this->throwError(403, "maybe your account has deleted, please try again.");
            }
            // update last_online in UserSystems
            $sysInfo->updateLastOnline($infoByToken["id"]);
            $this->returnResponse(204);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function forgetPassword()
    {
        if ($this->requestMethod == 'POST') {
            //* required parameters
            $identifier      = $this->validateRequiredParam('identifier');
            $machineUniqueId = $this->validateRequiredParam('machine_unique_id');

            $appName    = $this->validateRequiredParam('app_name');
            $appVersion = $this->validateRequiredParam('app_version');

            //^ optional parameters //
            //| os_name , cpu_arch , os_version , kernel_version

            $userByIdentifier = $this->checkUserByIdentifier($identifier);

            //& Saving user's system informations on UserSystems table of Database
            $sysInfo = new UserSystems();

            $token = $this->generateToken();
            $hasExistInfo = $sysInfo->getAll(intval($userByIdentifier["id"]), $machineUniqueId);

            if (!empty($hasExistInfo)) {
                $sysInfo->update(
                    [
                        'auth_token'        => $token,
                        'app_name'          => $appName,
                        'app_version'       => $appVersion,
                        'machine_unique_id' => $machineUniqueId,
                        'user_id'           => $userByIdentifier["id"],
                        'email'             => $userByIdentifier["email"],
                        'os_name'           => $this->request['os_name'] ?? "",
                        'cpu_arch'          => $this->request['cpu_arch'] ?? "",
                        'os_version'        => $this->request['os_version'] ?? "",
                        'kernel_version'    => $this->request['kernel_version'] ?? "",
                    ],
                    intval($hasExistInfo['id'])
                );
            } else {
                $sysInfo->insert(
                    [
                        'auth_token'        => $token,
                        'app_name'          => $appName,
                        'app_version'       => $appVersion,
                        'machine_unique_id' => $machineUniqueId,
                        'user_id'           => $userByIdentifier["id"],
                        'email'             => $userByIdentifier["email"],
                        'os_name'           => $this->request['os_name'] ?? "",
                        'cpu_arch'          => $this->request['cpu_arch'] ?? "",
                        'os_version'        => $this->request['os_version'] ?? "",
                        'kernel_version'    => $this->request['kernel_version'] ?? "",
                    ]
                );
            }

            $otp = $this->generateOTP();
            $email = $userByIdentifier["email"];

            $sender = new MailController();
            $sender->sendOtpMail($otp, $email);

            //* Saving OTP on SESSION
            $this->user->saveOtp($email, $otp);

            //* Sending response to user
            $result = ["email" => $email];
            $this->returnResponse(200, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function resetPassword()
    {
        if ($this->requestMethod == 'POST') {

            //* required parameters
            $otp                = $this->validateRequiredParam('otp');
            $password           = $this->validateRequiredParam('password');
            $identifier         = $this->validateRequiredParam('identifier');
            $machineUniqueId    = $this->validateRequiredParam('machine_unique_id');

            //* Checking user by Id or email
            $userByIdentifier = $this->checkUserByIdentifier($identifier);

            //* Checking OTP code correctness
            if (!$this->user->checkOTP($userByIdentifier["email"], $otp)) {
                $this->throwError(403, 'Your OTP is wrong, please send me right OTP');
            }

            $password = $this->SHA3_hash($password);
            $userByIdentifier["hashed_password"] = $password;

            $this->user->update($userByIdentifier, $userByIdentifier['id']);

            //* Sending response to user
            $result = $this->user->getCompleteData($userByIdentifier["id"], $machineUniqueId);
            $this->returnResponse(200, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function resendOTP()
    {
        if ($this->requestMethod == 'POST') {
            //* required parameters
            $identifier         = $this->validateRequiredParam('identifier');
            $machineUniqueId    = $this->validateRequiredParam('machine_unique_id');

            //* Checking user by Id or email
            $userByIdentifier = $this->checkUserByIdentifier($identifier);

            $email = $userByIdentifier["email"];
            $otp = $this->generateOTP();

            $sender = new MailController();
            $sender->sendOtpMail($otp, $email);

            //* Saving OTP on SESSION
            $this->user->saveOtp($email, $otp);

            $result = ["email" => $email];
            $this->returnResponse(200, $result);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function deleteAccount()
    {
        //* getting user id from URI
        $userId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[4]);

        //* Checking permission
        $this->validatePermision($userId);

        if ($this->requestMethod == 'POST') {

            $password = $this->validateRequiredParam('password');
            $password = $this->SHA3_hash($password);

            $userInfo = $this->user->fetchAllById($userId);

            if (empty($userInfo)) {
                $this->throwError(403, "Your account has already been deleted");
            } else if ($userInfo["hashed_password"] != $password) {
                $this->throwError(401, "The password is wrong, please enter correct password");
            }

            $this->user->delete($userInfo["id"]);
            $this->returnResponse(204);
        } else {
            $this->throwError(405, 'Method Not Allowed. The only method that is acceptable is POST.');
        }
    }

    public function getUser()
    {
        //* getting user id from URI
        $userId = intval(explode('/', trim($_SERVER['REQUEST_URI'], "/"))[3]);

        //* Checking permission
        $this->validatePermision($userId);

        if ($this->requestMethod == 'GET') {

            //* Sending response to user
            $result = $this->user->fetchAllById($userId);
            if (empty($result))
                $this->throwError(404, "The user not found");
            else
                $this->returnResponse(200, $result);
        }

        if ($this->requestMethod == 'PATCH') {

            $result = $this->user->fetchAllById($userId);
            if (empty($result))
                $this->throwError(404, "The user not found");

            //^ Checking availability of username
            $username = $this->request['username']  ?? null;
            if ($username) {
                $userByUsername = $this->user->getByUsername($username) ?? null;
                if (!empty($userByUsername)) {
                    if ($userByUsername["is_activated"] == 1) {
                        if ($userByUsername["id"] != $userId)
                            $this->throwError(409, "This username isn't available. Please try another");
                    } else
                        $this->user->delete(intval($userByUsername["id"]));
                }
                $result['username'] = $username;
            }
            unset($userByUsername);

            //^ Checking availability of email
            $email = $this->request['email'] ?? null;
            if ($email) {
                $userByEmail = $this->user->getByEmail($email) ?? null;
                if (!empty($userByEmail)) {
                    if ($userByEmail["is_activated"] == 1) {
                        if ($userByEmail["id"] != $userId)
                            $this->throwError(409, "Another account is using this email: $email");
                    } else
                        $this->user->delete(intval($userByEmail["id"]));
                }
                $result['email'] = $email;
            }
            unset($userByEmail);

            $avatar = $this->request['avatar'] ?? null;
            echo isset($this->request['avatar']);
            if ($avatar || array_key_exists('avatar',$this->request)) {
                if ($avatar == "SET_GRAVATAR")
                    $avatar = $this->getGravatar($result['email']);

                $result['avatar'] = $avatar;
            }

            $twoStep = $this->request['two_step'] ?? null;
            if ($twoStep !== null)
                $result['two_step'] = $twoStep;

            try {
                $res = $this->user->update($result, $userId);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            if ($res == TRUE) {
                $result = $this->user->fetchAllById($userId);
                $this->returnResponse(200, $result);
            } else
                $this->throwError(500, "we can't update your information");
        } else {
            $this->throwError(405, 'Method Not Allowed. The only methods that are acceptable is GET and PATCH.');
        }
    }
}
