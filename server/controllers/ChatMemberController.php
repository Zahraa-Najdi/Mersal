<?php

require_once(__DIR__ . "/../models/ChatMember.php");
require_once(__DIR__ . "/../connection/connection.php");
require_once(__DIR__ . "/../services/ResponseService.php");
require_once(__DIR__ . "/../services/ChatMemberService.php");

class ChatMemberController
{
    private ChatMemberService $ChatMemberService;

    public function __construct()
    {
        global $connection;
        $this->ChatMemberService = new ChatMemberService($connection);
    }

    public function getChatMembers()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"] ?? null;
            if ($id) {
                $result = $this->ChatMemberService->getChatMemberById($id);
                echo ResponseService::response($result['status'], $result['data']);
                exit;
            }
            $chat_id = $input["chat_id"] ?? null;
            if ($chat_id) {
                $result = $this->ChatMemberService->getChatMembersByChatId($chat_id);
                echo ResponseService::response($result['status'], $result['data']);
                exit;
            }
            $result = $this->ChatMemberService->getAllChatMembers();
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while getting habits: ' . $e->getMessage()]);
        }
    }

    public function deleteChatMember()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $input["id"];
            if (!$id) {
                echo ResponseService::response(400, ['error' => 'ID is required']);
                return;
            }

            $result = $this->ChatMemberService->deleteChatMember($id);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while deleting the habit: ' . $e->getMessage()]);
        }
    }

    public function createChatMember()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (!$input) {
                echo ResponseService::response(400, ['error' => 'No data provided']);
                return;
            }

            $result = $this->ChatMemberService->createChatMember($input);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while creating the habit: ' . $e->getMessage()]);
        }
    }

    public function updateChatMember()
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

            $result = $this->ChatMemberService->updateChatMember($id, $input);
            echo ResponseService::response($result['status'], $result['data']);
        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while updating the habit: ' . $e->getMessage()]);
        }
    }

    public function getAllChats(){
        try {
            $id = isset($_GET["id"]) ? $_GET["id"] : null;
            if (!$id) {
                echo ResponseService::response(400, ['error' => 'ID is required']);
                return;
            }
            $result = $this->ChatMemberService->getAllChats($id);
            echo ResponseService::response($result['status'], $result['data']);

        } catch (Exception $e) {
            echo ResponseService::response(500, ['error' => 'An error occurred while updating the habit: ' . $e->getMessage()]);
        }
        
    }
}
?>