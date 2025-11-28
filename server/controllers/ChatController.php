<?php

require_once(__DIR__ . "/../models/Chat.php");
require_once(__DIR__ . "/../connection/connection.php");
require_once(__DIR__ . "/../services/ResponseService.php");
require_once(__DIR__ . "/../services/ChatService.php");

class ChatController
{
    private ChatService $ChatService;

    public function __construct()
    {
        global $connection;
        $this->ChatService = new ChatService($connection);
    }

    public function getChats()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"] ?? null;
            if ($id) {
                $result = $this->ChatService->getChatById($id);
                echo ResponseService::response($result['status'], $result['data']);
                exit;
            }
            $result = $this->ChatService->getAllChats();
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while getting chats: ' . $e->getMessage()]);
        }
    }

    public function deleteChat()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"];
            if (!$id) {
                echo ResponseService::response(400, ['error' => 'ID is required']);
                return;
            }

            $result = $this->ChatService->deleteChat($id);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while deleting the chat: ' . $e->getMessage()]);
        }
    }

    public function createChat()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            // if (!$input) {
            //     echo ResponseService::response(400, ['error' => 'No data provided']);
            //     return;
            // }

            $result = $this->ChatService->createChat($input);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while creating the chat: ' . $e->getMessage()]);
        }
    }

    public function updateChat()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"] ?? null;

            if (!$id) {
                echo ResponseService::response(400, ['error' => 'ID is required']);
                return;
            }
            if (!$input) {
                echo ResponseService::response(400, ['error' => 'provide data to update']);
                return;
            }

            $result = $this->ChatService->updateChat($id, $input);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while updating the chat: ' . $e->getMessage()]);
        }
    }
}
?>