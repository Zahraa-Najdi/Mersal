<?php
require_once(__DIR__ . "/../models/ChatMember.php");
require_once(__DIR__ . "/../models/User.php");
require_once(__DIR__ . "/ServiceHelper.php");

class ChatMemberService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getChatMemberById(int $id)
    {
        try {
            $chatMember = ChatMember::find($this->connection, $id, 'id');
            return $chatMember
                ? ['status' => 200, 'data' => $chatMember->toArray()]
                : ['status' => 404, 'data' => ['error' => 'chatMember not found']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting chatMember by id: ' . $e->getMessage()]];
        }
    }

    public function getChatMembersByChatId(int $chatId)
    {
        try {
            $validationResult = ServiceHelper::validateIdExists($this->connection, $chatId);
            if (isset($validationResult['status']) && $validationResult['status'] !== 200) {
                return $validationResult;
            }

            $chatMembers = ChatMember::findAllByOtherId($this->connection, $chatId, 'chat_id');
            $data = array_map(fn($chatMember) => $chatMember->toArray(), $chatMembers);

            return ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while get chatMember by chat id: ' . $e->getMessage()]];
        }
    }

    public function getAllChatMembers()
    {
        try {
            $chatMembers = ChatMember::findAll($this->connection);
            $data = array_map(fn($chatMember) => $chatMember->toArray(), $chatMembers);

            return ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting all chatMembers: ' . $e->getMessage()]];
        }
    }

    public function createChatMember(array $data)
    {
        try {
            $validationResult = ServiceHelper::validateRequiredFields($data, $this->getRequiredFields());
            if ($validationResult !== true) {
                return $validationResult;
            }

            $chatMemberId = ChatMember::create($this->connection, $data);
            if ($chatMemberId == "Duplicate") {
                return [
                    'status' => 500,
                    'data' => [
                        'message' => 'duplicate chatMember name',
                    ]
                ];
            }
            if ($chatMemberId) {
                return [
                    'status' => 201,
                    'data' => [
                        'message' => 'chatMember created successfully',
                        'id' => $chatMemberId
                    ]
                ];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to create chatMember']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while creating chatMember: ' . $e->getMessage()]];
        }
    }

    public function updateChatMember(int $id, array $data)
    {
        try {
            $chatMember = ChatMember::find($this->connection, $id, "id");
            if (!$chatMember) {
                return ['status' => 404, 'data' => ['error' => 'chatMember not found']];
            }

            if (empty($data)) {
                return ['status' => 400, 'data' => ['error' => 'No data provided for update']];
            }

            $result = $chatMember->update($this->connection, $data, "id");
            if ($result == "Duplicate") {
                return ['status' => 500, 'data' => ['message' => 'chatMember name already exists']];
            }

            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'chatMember updated successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to update chatMember']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while updating chatMember: ' . $e->getMessage()]];
        }
    }

    public function deleteChatMember(int $id)
    {
        try {
            $chatMember = ChatMember::find($this->connection, $id, "id");
            if (!$chatMember) {
                return ['status' => 404, 'data' => ['error' => 'chatMember not found']];
            }

            $result = ChatMember::deleteById($id, $this->connection, "id");
            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'chatMember deleted successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to delete chatMember']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while deleting chatMember: ' . $e->getMessage()]];
        }
    }
    public function getAllChats($id){
        try {
            $chatMember = User::find($this->connection, $id, "id");
            if (!$chatMember) {
                return ['status' => 404, 'data' => ['error' => 'chatMember not found']];
            }
            $result = ChatMember::getAllChats($this->connection, $id);
            if ($result) {
                return ['status' => 200, 'data' => $result];
            }
            return ['status' => 500, 'data' => ['error' => 'Failed to delete chatMember']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while gettingAll chats: ' . $e->getMessage()]];
        }
    }

   

    private function getRequiredFields()
    {
        return [ 'chat_id', 'user_id'];
    }
}
