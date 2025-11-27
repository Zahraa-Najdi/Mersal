<?php
require_once(__DIR__ . "/../models/Chat.php");
require_once(__DIR__ . "/../models/User.php");
require_once(__DIR__ . "/ServiceHelper.php");

class ChatService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getChatById(int $id)
    {
        try {
            $chat = Chat::find($this->connection, $id, 'id');
            return $chat
                ? ['status' => 200, 'data' => $chat->toArray()]
                : ['status' => 404, 'data' => ['error' => 'chat not found']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting chat by id: ' . $e->getMessage()]];
        }
    }

    public function getAllChats()
    {
        try {
            $chats = Chat::findAll($this->connection);
            $data = array_map(fn($chat) => $chat->toArray(), $chats);

            return ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting all chats: ' . $e->getMessage()]];
        }
    }

    public function createChat(?array $data)
    {
        try {
            if ($data === null) {
                $data = [];
            }
            $data['is_group'] = $data['is_group'] ?? 0;
            $chatId = Chat::create($this->connection, $data);
            if ($chatId) {
                return [
                    'status' => 201,
                    'data' => [
                        'message' => 'chat created successfully',
                        'id' => $chatId
                    ]
                ];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to create chat']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while creating chat: ' . $e->getMessage()]];
        }
    }

    public function updateChat(int $id, array $data)
    {
        try {
            $chat = Chat::find($this->connection, $id, "id");
            if (!$chat) {
                return ['status' => 404, 'data' => ['error' => 'chat not found']];
            }

            if (empty($data)) {
                return ['status' => 400, 'data' => ['error' => 'No data provided for update']];
            }

            $result = $chat->update($this->connection, $data, "id");
            if ($result == "Duplicate") {
                return ['status' => 500, 'data' => ['message' => 'chat name already exists']];
            }

            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'chat updated successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to update chat']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while updating chat: ' . $e->getMessage()]];
        }
    }

    public function deleteChat(int $id)
    {
        try {
            $chat = Chat::find($this->connection, $id, "id");
            if (!$chat) {
                return ['status' => 404, 'data' => ['error' => 'chat not found']];
            }

            $result = Chat::deleteById($id, $this->connection, "id");
            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'chat deleted successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to delete chat']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while deleting chat: ' . $e->getMessage()]];
        }
    }

   

    private function getRequiredFields()
    {
        return [];
    }
}
