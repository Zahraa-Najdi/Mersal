<?php

require_once(__DIR__ . "/../models/Message.php");
require_once(__DIR__ . "/../connection/connection.php");
require_once(__DIR__ . "/../services/ResponseService.php");
require_once(__DIR__ . "/../services/MessageService.php");

class MessageController
{
    private MessageService $MessageService;

    public function __construct()
    {
        global $connection;
        $this->MessageService = new MessageService($connection);
    }

    public function getMessages()
    {
        try {
            
            $receiverId = $_GET['received_id'] ?? null;
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"] ?? null;
            if ($id) {
                $result = $this->MessageService->getMessageById($id);
                echo ResponseService::response($result['status'], $result['data']);
                exit;
            }
            $chat_id = $input["chat_id"] ?? null;
            if ($chat_id && $receiverId) {
                $result = $this->MessageService->getMessagesByChatId($chat_id, $receiverId,'delivered');
                echo ResponseService::response($result['status'], $result['data']);
                exit;
            }
            $result = $this->MessageService->getAllMessages();
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while getting messages: ' . $e->getMessage()]);
        }
    }

    public function deleteMessage()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"];
            if (!$id) {
                echo ResponseService::response(400, ['error' => 'ID is required']);
                return;
            }

            $result = $this->MessageService->deleteMessage($id);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while deleting the message: ' . $e->getMessage()]);
        }
    }

    public function createMessage()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (!$input) {
                echo ResponseService::response(400, ['error' => 'No data provided']);
                return;
            }

            $result = $this->MessageService->createMessage($input);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while creating the message: ' . $e->getMessage()]);
        }
    }

    public function updateMessage()
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

            $result = $this->MessageService->updateMessage($id, $input);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while updating the message: ' . $e->getMessage()]);
        }
    }

    public function turnMessagesToRead()
    {
        try {

            $receiverId = $_GET['received_id'] ?? null;
            $input = json_decode(file_get_contents("php://input"), true);
            
            $chat_id = $input["chat_id"] ?? null;
            if ($chat_id && $receiverId) {
                $result = $this->MessageService->getMessagesByChatId($chat_id, $receiverId,'read');
                echo ResponseService::response($result['status'], $result['data']);
                exit;
            }
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while getting messages: ' . $e->getMessage()]);
        }
    }
}
?>