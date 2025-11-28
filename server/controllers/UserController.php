<?php

require_once(__DIR__ . "/../models/User.php");
require_once(__DIR__ . "/../connection/connection.php");
require_once(__DIR__ . "/../services/ResponseService.php");
require_once(__DIR__ . "/../services/UserService.php");

class UserController
{
    private UserService $UserService;

    public function __construct()
    {
        global $connection;
        $this->UserService = new UserService($connection);
    }

    public function getUsers()//if user exists
    {
        try {
            $id = isset($_GET["id"]) ? $_GET["id"] : null;
            $result = $this->UserService->getUsers($id);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while getting users: ' . $e->getMessage()]);
        }
    }

    public function getUserByEmail()// if user exists
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $result = $this->UserService->getUserByEmail($input["email"], $input["password"]);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while retrieving user by email: ' . $e->getMessage()]);
        }
    }

    public function deleteUser()//if user exists
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"];

            if (!$id) {
                echo ResponseService::response(400, ['error' => 'ID is required']);
                return;
            }

            $result = $this->UserService->deleteUser($id);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while deleting the user: ' . $e->getMessage()]);
        }
    }

    public function createUser()// if user does not exist
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (!$input) {
                echo ResponseService::response(400, ['error' => 'No data provided']);
                return;
            }

            $result = $this->UserService->createUser($input);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while creating the user: ' . $e->getMessage()]);
        }
    }

    public function updateUser()//in login
    {
        try {//for error handling
            $input = json_decode(file_get_contents("php://input"), true);//JSON sent from frontend (new input)
            $id = isset($_GET["id"]) ? $_GET["id"] : null;//if id exists store id in $id, otherwise store null

            if (!$id) {
                echo ResponseService::response(400, ['error' => 'ID is required']);
                return;
            }

            if (!$input) {
                echo ResponseService::response(400, ['error' => 'provide data to update']);
                return;
            }

            $result = $this->UserService->updateUser($id, $input);
            echo ResponseService::response($result['status'], $result['data']);//'status' and 'data' are given a value in UserService
        } catch (Exception $e) {//catch error-> if an exception/error occurs
            echo ResponseService::response(500, ['error' => 'An error occurred while updating the user: ' . $e->getMessage()]);
        }
    }
}
?>